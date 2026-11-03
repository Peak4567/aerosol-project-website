"use client";

import { useEffect, useState } from "react";
import useSWR from "swr";
import { toast } from "sonner";
import { fetcher } from "@/lib/fetcher";
import type { AllSensorsResponse } from "@/components/dashboard/sensor-config";
import { DbStatusBadge } from "@/components/dashboard/DbStatusBadge";
import { ManualSlidersCard } from "./ManualSlidersCard";
import { DeviceStatusCard } from "./DeviceStatusCard";
import { WaterTankCard } from "./WaterTankCard";
import { DepletionCard } from "./DepletionCard";
import { GrowthProgramEditor } from "./GrowthProgramEditor";
import { CultivationCard } from "./CultivationCard";
import { ResearchCard } from "./ResearchCard";
import { SystemStatusModal } from "./SystemStatusModal";
import type { DeviceControls, GrowthPeriod, LogEntry } from "./smartfarm-types";

type Tab = "basic" | "ai-control" | "research";

const DEFAULT_TARGETS = {
  ph: 6.0,
  humidity: 80,
  temperature: 25,
  ec: 1.5,
  light: 7000,
  tds: 750,
};

let periodIdCounter = 1;

function defaultPeriod(start: number): GrowthPeriod {
  return {
    id: periodIdCounter++,
    start,
    end: start + 9,
    ph: 6,
    ec: 1.5,
    temp: 25,
    humid: 80,
    lightStart: "07:00",
    lightEnd: "14:00",
    lightInt: 7000,
    tds: 750,
  };
}

function estimateDays(history: { time: number; value: number }[], current: number, min: number) {
  if (history.length < 2) return { text: "--", rate: "--" };
  const diff = history[0].value - history[history.length - 1].value;
  const time = history[history.length - 1].time - history[0].time;
  if (diff <= 0 || time <= 0) return { text: "∞", rate: "0.0" };
  const rate = diff / time;
  const msLeft = (current - min) / rate;
  const totalMin = Math.floor(msLeft / 60000);
  const days = Math.floor(totalMin / 1440);
  const hrs = Math.floor((totalMin % 1440) / 60);
  const mins = totalMin % 60;
  const ratePerDay = rate * 1000 * 3600 * 24;
  return {
    text: `${days} วัน ${hrs} ชม. ${mins} น.`,
    rate: ratePerDay.toFixed(2),
  };
}

export function SmartFarmPanel() {
  const { data: sensorData } = useSWR<AllSensorsResponse>(
    "/api/sensors?type=all",
    fetcher,
    { refreshInterval: 2000 }
  );

  const [activeTab, setActiveTab] = useState<Tab>("basic");
  const [aiMode, setAiMode] = useState(true);
  const [targets, setTargets] = useState<Record<string, number>>(DEFAULT_TARGETS);
  const [simulated, setSimulated] = useState({ light: 7000, water_level: 80 });
  const [controls, setControls] = useState<DeviceControls>({
    return_pump_on: false,
    mister_pump_on: false,
    uv_light_on: false,
    ultrasonic_mister_on: false,
    exhaust_fan_on: false,
    mist_fan_on: false,
    grow_light_on: false,
  });

  const [cultivation, setCultivation] = useState<{
    status: "idle" | "running" | "paused";
    startTime: number | null;
    accumulated: number;
  }>({ status: "idle", startTime: null, accumulated: 0 });
  const [elapsed, setElapsed] = useState(0);

  const [tankCapacity, setTankCapacity] = useState(100);
  const [waterMinThreshold, setWaterMinThreshold] = useState(10);
  const [tdsMinThreshold, setTdsMinThreshold] = useState(400);
  const [ecMinThreshold, setEcMinThreshold] = useState(1.0);

  const [waterHistory, setWaterHistory] = useState<
    { time: number; value: number }[]
  >([]);
  const [tdsHistory, setTdsHistory] = useState<{ time: number; value: number }[]>(
    []
  );
  const [ecHistory, setEcHistory] = useState<{ time: number; value: number }[]>(
    []
  );

  const [periods, setPeriods] = useState<GrowthPeriod[]>([defaultPeriod(1)]);

  const [logs, setLogs] = useState<LogEntry[]>([
    {
      timestamp: new Date().toLocaleString("th-TH"),
      level: "INFO",
      message: "ระบบเริ่มทำงาน",
    },
  ]);
  const [statusModalOpen, setStatusModalOpen] = useState(false);

  const [mistCycle, setMistCycle] = useState({
    enabled: false,
    onMin: 1,
    onSec: 0,
    offMin: 2,
    offSec: 0,
    status: "standby" as "standby" | "working" | "stopped",
  });

  const temperature = sensorData?.datasets.sht40_temp?.at(-1) ?? 0;
  const humidity = sensorData?.datasets.sht40_humi?.at(-1) ?? 0;
  const ph = sensorData?.datasets.ph?.at(-1) ?? 0;
  const ec = sensorData?.datasets.ec?.at(-1) ?? 0;
  const tds = sensorData?.datasets.tds?.at(-1) ?? 0;

  function addLog(level: LogEntry["level"], message: string) {
    setLogs((prev) => {
      const last = prev[prev.length - 1];
      if (last && last.message === message && last.level === level) return prev;
      const next = [
        ...prev,
        { timestamp: new Date().toLocaleString("th-TH"), level, message },
      ];
      return next.length > 50 ? next.slice(-50) : next;
    });
  }

  // Simulated light + water level random walk, and derived AI control logic.
  // Mirrors the old app.py/get_data.php combo: real chemistry sensors come
  // from MySQL, light/water/device-control state is simulated client-side.
  useEffect(() => {
    const interval = setInterval(() => {
      setSimulated((prev) => {
        const light = Math.max(0, prev.light + (Math.random() - 0.5) * 200);
        const water_level = Math.max(0, prev.water_level - Math.random() * 0.05);
        return { light, water_level };
      });

      if (Math.random() < 0.01) {
        addLog("WARNING", "ค่าความชื้นผันผวนสูงกว่าปกติ (Simulated)");
      }
    }, 2000);
    return () => clearInterval(interval);
  }, []);

  useEffect(() => {
    if (!aiMode) return;
    // eslint-disable-next-line react-hooks/set-state-in-effect -- derives simulated device state from live sensor readings, the reason this effect exists
    setControls((prev) => ({
      ...prev,
      exhaust_fan_on: temperature > targets.temperature + 1,
      mister_pump_on: humidity < targets.humidity - 5,
      ultrasonic_mister_on: humidity < targets.humidity - 5,
      mist_fan_on: humidity < targets.humidity - 5,
      grow_light_on: simulated.light < 5000,
      return_pump_on: Math.floor(Date.now() / 1000) % 60 < 30,
      uv_light_on: Math.floor(Date.now() / 1000) % 60 < 30,
    }));
  }, [aiMode, temperature, humidity, simulated.light, targets]);

  // History tracking for the depletion calculators.
  useEffect(() => {
    const now = Date.now();
    // eslint-disable-next-line react-hooks/set-state-in-effect -- appends a history sample whenever the external water reading changes
    setWaterHistory((prev) => {
      if (prev.length > 0 && prev[prev.length - 1].value === simulated.water_level) {
        return prev;
      }
      return [...prev, { time: now, value: simulated.water_level }].slice(-100);
    });
  }, [simulated.water_level]);

  useEffect(() => {
    const now = Date.now();
    // eslint-disable-next-line react-hooks/set-state-in-effect -- appends a history sample whenever the polled TDS/EC reading changes
    setTdsHistory((prev) => [...prev, { time: now, value: tds }].slice(-100));
    setEcHistory((prev) => [...prev, { time: now, value: ec }].slice(-100));
  }, [tds, ec]);

  // Cultivation timer tick.
  useEffect(() => {
    if (cultivation.status !== "running" || !cultivation.startTime) return;
    const interval = setInterval(() => {
      setElapsed((Date.now() - cultivation.startTime!) / 1000 + cultivation.accumulated);
    }, 1000);
    return () => clearInterval(interval);
  }, [cultivation]);

  function startCultivation() {
    setCultivation((prev) => ({
      status: "running",
      startTime: Date.now(),
      accumulated: prev.accumulated,
    }));
    toast.success(cultivation.status === "idle" ? "เริ่มการเพาะปลูก" : "ทำงานต่อ");
    addLog("INFO", "Cultivation Started/Resumed");
  }

  function stopCultivation() {
    setCultivation((prev) => ({
      status: "paused",
      startTime: null,
      accumulated: elapsed,
    }));
    toast.info("หยุดชั่วคราว");
    addLog("INFO", "Cultivation Paused");
  }

  function resetCultivation() {
    setCultivation({ status: "idle", startTime: null, accumulated: 0 });
    setElapsed(0);
    toast.success("รีเซ็ตระบบ");
    addLog("WARNING", "Cultivation Reset");
  }

  const waterEstimate = estimateDays(
    waterHistory,
    simulated.water_level,
    waterMinThreshold
  );
  const tdsEstimate = estimateDays(tdsHistory, tds, tdsMinThreshold);
  const ecEstimate = estimateDays(ecHistory, ec, ecMinThreshold);

  function handlePeriodChange(id: number, patch: Partial<GrowthPeriod>) {
    setPeriods((prev) => prev.map((p) => (p.id === id ? { ...p, ...patch } : p)));
  }

  function handleAddPeriod() {
    setPeriods((prev) => {
      const last = prev[prev.length - 1];
      return [...prev, defaultPeriod(last ? last.end + 1 : 1)];
    });
  }

  function handleRemovePeriod(id: number) {
    setPeriods((prev) => prev.filter((p) => p.id !== id));
  }

  return (
    <div className="min-h-screen bg-gray-100">
      <div className="fixed right-3 top-3 z-40">
        <DbStatusBadge />
      </div>

      <div className="container relative z-10 mx-auto p-4 md:p-8">
        <header className="mb-12 text-center">
          <h1 className="text-5xl font-bold text-gray-800">AI Aerosol</h1>
          <p className="mt-2 text-lg text-gray-600">
            ระบบอัจฉริยะควบคุมสภาพแวดล้อม
          </p>
        </header>

        <main className="grid grid-cols-1 gap-8 lg:grid-cols-3">
          <div className="space-y-8 lg:col-span-1">
            <div className="card rounded-2xl p-6 shadow-lg">
              <div className="mb-4 flex items-center justify-between">
                <h2 className="text-xl font-bold">1. ข้อมูลเซ็นเซอร์ปัจจุบัน</h2>
              </div>
              <div className="grid grid-cols-2 gap-4 text-center">
                <div>
                  <p className="text-sm text-gray-500">PH</p>
                  <p className="text-2xl font-bold text-blue-600">{ph}</p>
                </div>
                <div>
                  <p className="text-sm text-gray-500">ความชื้น</p>
                  <p className="text-2xl font-bold text-indigo-600">{humidity} %</p>
                </div>
                <div>
                  <p className="text-sm text-gray-500">อุณหภูมิ</p>
                  <p className="text-2xl font-bold text-red-600">{temperature} °C</p>
                </div>
                <div>
                  <p className="text-sm text-gray-500">EC</p>
                  <p className="text-2xl font-bold text-green-600">{ec}</p>
                </div>
                <div>
                  <p className="text-sm text-gray-500">แสง</p>
                  <p className="text-2xl font-bold text-yellow-600">
                    {simulated.light.toFixed(0)}
                  </p>
                </div>
                <div>
                  <p className="text-sm text-gray-500">TDS</p>
                  <p className="text-2xl font-bold text-purple-600">{tds} ppm</p>
                </div>
              </div>
            </div>

            <CultivationCard
              status={cultivation.status}
              elapsedSeconds={elapsed}
              onStart={startCultivation}
              onStop={stopCultivation}
              onReset={resetCultivation}
            />

            <div className="card rounded-2xl p-6 shadow-lg">
              <div className="mb-4 flex items-center justify-between">
                <h2 className="text-xl font-bold">3. โหมดควบคุม AI</h2>
                <label className="relative inline-flex cursor-pointer items-center">
                  <input
                    type="checkbox"
                    checked={aiMode}
                    onChange={(e) => setAiMode(e.target.checked)}
                    className="peer sr-only"
                  />
                  <div className="h-7 w-12 rounded-full bg-gray-300 transition-colors peer-checked:bg-blue-600" />
                  <div className="absolute left-1 top-1 h-5 w-5 rounded-full bg-white transition-transform peer-checked:translate-x-5" />
                </label>
              </div>
              <p className="text-sm text-gray-500">
                เปิดใช้งานเพื่อให้ AI ควบคุมอัตโนมัติตามโปรแกรม
              </p>
            </div>

            <DeviceStatusCard controls={controls} />
          </div>

          <div className="space-y-8 lg:col-span-2">
            <div className="flex overflow-hidden rounded-xl border-b border-gray-200 bg-white shadow-sm">
              {(
                [
                  ["basic", "การปลูกขั้นพื้นฐาน"],
                  ["ai-control", "ระบบควบคุม AI"],
                  ["research", "ระบบเก็บข้อมูลวิจัย"],
                ] as [Tab, string][]
              ).map(([tab, label]) => (
                <button
                  key={tab}
                  onClick={() => setActiveTab(tab)}
                  className={`flex-1 py-3 text-lg font-semibold transition ${
                    activeTab === tab
                      ? "border-b-[3px] border-blue-600 bg-blue-50 text-blue-600"
                      : "text-gray-500 hover:text-blue-500"
                  }`}
                >
                  {label}
                </button>
              ))}
            </div>

            {activeTab === "basic" && (
              <ManualSlidersCard
                targets={targets}
                current={{
                  ph,
                  humidity,
                  temperature,
                  ec,
                  light: simulated.light,
                  tds,
                }}
                onChange={(key, value) =>
                  setTargets((prev) => ({ ...prev, [key]: value }))
                }
              />
            )}

            {activeTab === "ai-control" && (
              <div className="space-y-8">
                <div className="card rounded-2xl border-2 border-cyan-400 bg-white p-6 shadow-lg">
                  <div className="mb-4 flex items-center justify-between">
                    <div className="flex items-center">
                      <span className="mr-2 text-2xl">☁️</span>
                      <div>
                        <h2 className="text-xl font-bold text-gray-800">
                          ระบบปั๊มพ่นหมอก (Ultrasonic Cycle)
                        </h2>
                        <p className="text-sm text-gray-500">
                          ควบคุมเวลาเปิด-ปิดหมอก
                        </p>
                      </div>
                    </div>
                    <label className="relative inline-flex cursor-pointer items-center">
                      <input
                        type="checkbox"
                        checked={mistCycle.enabled}
                        onChange={(e) =>
                          setMistCycle((m) => ({ ...m, enabled: e.target.checked }))
                        }
                        className="peer sr-only"
                      />
                      <div className="h-7 w-12 rounded-full bg-gray-300 transition-colors peer-checked:bg-blue-600" />
                      <div className="absolute left-1 top-1 h-5 w-5 rounded-full bg-white transition-transform peer-checked:translate-x-5" />
                    </label>
                  </div>
                  <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div className="rounded-xl border border-cyan-100 bg-cyan-50 p-4">
                      <p className="mb-2 text-sm font-bold text-cyan-800">
                        เวลาทำงาน (ON)
                      </p>
                      <div className="flex items-center space-x-2">
                        <input
                          type="number"
                          value={mistCycle.onMin}
                          onChange={(e) =>
                            setMistCycle((m) => ({
                              ...m,
                              onMin: parseInt(e.target.value) || 0,
                            }))
                          }
                          className="w-full rounded border p-2 text-center font-bold text-gray-700"
                        />
                        <span className="text-sm text-gray-500">นาที</span>
                        <input
                          type="number"
                          value={mistCycle.onSec}
                          onChange={(e) =>
                            setMistCycle((m) => ({
                              ...m,
                              onSec: parseInt(e.target.value) || 0,
                            }))
                          }
                          className="w-full rounded border p-2 text-center font-bold text-gray-700"
                        />
                        <span className="text-sm text-gray-500">วินาที</span>
                      </div>
                    </div>
                    <div className="rounded-xl border border-gray-200 bg-gray-50 p-4">
                      <p className="mb-2 text-sm font-bold text-gray-600">
                        เวลาพัก (OFF)
                      </p>
                      <div className="flex items-center space-x-2">
                        <input
                          type="number"
                          value={mistCycle.offMin}
                          onChange={(e) =>
                            setMistCycle((m) => ({
                              ...m,
                              offMin: parseInt(e.target.value) || 0,
                            }))
                          }
                          className="w-full rounded border p-2 text-center font-bold text-gray-700"
                        />
                        <span className="text-sm text-gray-500">นาที</span>
                        <input
                          type="number"
                          value={mistCycle.offSec}
                          onChange={(e) =>
                            setMistCycle((m) => ({
                              ...m,
                              offSec: parseInt(e.target.value) || 0,
                            }))
                          }
                          className="w-full rounded border p-2 text-center font-bold text-gray-700"
                        />
                        <span className="text-sm text-gray-500">วินาที</span>
                      </div>
                    </div>
                  </div>
                  <div className="mt-6 flex items-center justify-between border-t border-gray-100 pt-4">
                    <div className="flex items-center space-x-2">
                      <div
                        className={`h-3 w-3 rounded-full ${
                          mistCycle.status === "working"
                            ? "animate-pulse bg-green-500"
                            : mistCycle.status === "stopped"
                              ? "bg-gray-400"
                              : "bg-yellow-400"
                        }`}
                      />
                      <span className="text-sm font-bold text-gray-500">
                        {mistCycle.status === "working"
                          ? "กำลังพ่นหมอก (Working)"
                          : mistCycle.status === "stopped"
                            ? "ปิดระบบ (Off)"
                            : "พักเครื่อง (Standby)"}
                      </span>
                    </div>
                    <button
                      onClick={() => {
                        setMistCycle((m) => ({
                          ...m,
                          status: m.enabled ? "working" : "stopped",
                        }));
                        toast.success("บันทึกรอบการพ่นหมอกแล้ว");
                        addLog("INFO", "Mist cycle settings saved");
                      }}
                      className="transform rounded-lg bg-cyan-600 px-6 py-2 font-bold text-white shadow-md transition hover:scale-105 hover:bg-cyan-700"
                    >
                      บันทึกและเริ่มทำงาน
                    </button>
                  </div>
                </div>

                <WaterTankCard
                  currentWater={simulated.water_level}
                  tankCapacity={tankCapacity}
                  minThreshold={waterMinThreshold}
                  daysLeftText={waterEstimate.text}
                  refillDateText={`คาดการณ์จากอัตราการใช้ล่าสุด`}
                  depletionRateText={waterEstimate.rate}
                  onCapacityChange={setTankCapacity}
                  onThresholdChange={setWaterMinThreshold}
                />

                <DepletionCard
                  tds={{
                    current: tds,
                    minThreshold: tdsMinThreshold,
                    daysLeftText: tdsEstimate.text,
                    rateText: `~${tdsEstimate.rate}/วัน`,
                  }}
                  ec={{
                    current: ec,
                    minThreshold: ecMinThreshold,
                    daysLeftText: ecEstimate.text,
                    rateText: `~${ecEstimate.rate}/วัน`,
                  }}
                  onTdsThresholdChange={setTdsMinThreshold}
                  onEcThresholdChange={setEcMinThreshold}
                  onSave={() => {
                    toast.success("บันทึกค่าแล้ว");
                    addLog("INFO", "Refill settings saved");
                  }}
                />

                <GrowthProgramEditor
                  periods={periods}
                  onChange={handlePeriodChange}
                  onAdd={handleAddPeriod}
                  onRemove={handleRemovePeriod}
                  onSave={() => {
                    toast.success("บันทึกโปรแกรมสำเร็จ");
                    addLog("INFO", "Growth Program Saved");
                  }}
                />
              </div>
            )}

            {activeTab === "research" && (
              <div className="space-y-8">
                <ResearchCard />
                <button
                  onClick={() => setStatusModalOpen(true)}
                  className="card w-full rounded-2xl p-6 text-left shadow-lg transition hover:bg-gray-50"
                >
                  <div className="flex items-center space-x-4">
                    <div className="rounded-full bg-gray-100 p-3">
                      <i className="fa-solid fa-circle-check text-xl text-gray-600" />
                    </div>
                    <div>
                      <h2 className="text-xl font-bold">สถานะระบบและประวัติ</h2>
                      <p className="text-sm text-gray-500">
                        คลิกดูรายละเอียดทั้งหมด
                      </p>
                    </div>
                  </div>
                  <div className="mt-4 flex space-x-4">
                    <span className="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                      ระบบ: ปกติ
                    </span>
                    <span className="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
                      Errors: {logs.filter((l) => l.level === "ERROR").length}
                    </span>
                  </div>
                </button>
              </div>
            )}
          </div>
        </main>
      </div>

      <SystemStatusModal
        open={statusModalOpen}
        logs={logs}
        onClose={() => setStatusModalOpen(false)}
      />
    </div>
  );
}
