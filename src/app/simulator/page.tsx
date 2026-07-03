"use client";

import { useEffect, useRef, useState } from "react";

interface LogLine {
  id: number;
  text: string;
  isError: boolean;
}

let logIdCounter = 0;

export default function SimulatorPage() {
  const [running, setRunning] = useState(false);
  const [logs, setLogs] = useState<LogLine[]>([]);
  const intervalRef = useRef<ReturnType<typeof setInterval> | null>(null);

  function log(text: string, isError = false) {
    setLogs((prev) => {
      const next = [...prev, { id: logIdCounter++, text, isError }];
      return next.length > 100 ? next.slice(-100) : next;
    });
  }

  async function insertData() {
    try {
      const res = await fetch("/api/simulate");
      const result = await res.json();
      if (result.status === "success") {
        const d = result.data;
        const time = new Date(d.time).toLocaleTimeString();
        log(
          `[${time}] 🌡️Temp:${d.temp} 💧Hum:${d.humid}% 🧪pH:${d.ph} ⚡EC:${d.ec}`
        );
      } else {
        log(`❌ Error: ${result.message}`, true);
      }
    } catch {
      log("⚠️ Connection Error!", true);
    }
  }

  useEffect(() => {
    const stored = localStorage.getItem("sim_running") === "true";
    // eslint-disable-next-line react-hooks/set-state-in-effect -- localStorage is only available client-side, must read after mount
    if (stored) setRunning(true);
  }, []);

  useEffect(() => {
    localStorage.setItem("sim_running", String(running));
    if (intervalRef.current) clearInterval(intervalRef.current);

    if (running) {
      // eslint-disable-next-line react-hooks/set-state-in-effect -- starting the polling loop is the side effect this hook exists for
      log("🚀 System Started...");
      insertData();
      intervalRef.current = setInterval(insertData, 3000);
    } else {
      intervalRef.current = null;
    }

    return () => {
      if (intervalRef.current) clearInterval(intervalRef.current);
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [running]);

  return (
    <div
      className="flex min-h-screen items-center justify-center bg-slate-100 p-4"
      style={{ fontFamily: "var(--font-body)" }}
    >
      <div className="w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-8 shadow-xl">
        <div className="mb-6 text-center">
          <h1 className="text-3xl font-bold text-slate-800">
            🎮 Data Simulator
          </h1>
          <p className="mt-1 text-sm text-slate-500">
            ระบบจำลองการส่งค่าเซ็นเซอร์เข้า Database
          </p>
        </div>

        <div className="mb-6 flex flex-col items-center space-y-6">
          <div
            className={`rounded-full border px-6 py-2 text-sm font-bold shadow-sm transition-all duration-300 ${
              running
                ? "animate-pulse border-green-200 bg-green-100 text-green-600"
                : "border-red-200 bg-red-100 text-red-600"
            }`}
          >
            {running ? "● กำลังจำลองข้อมูล... (Running)" : "● หยุดการทำงาน (Stopped)"}
          </div>

          <label className="relative flex cursor-pointer items-center">
            <input
              type="checkbox"
              checked={running}
              onChange={(e) => setRunning(e.target.checked)}
              className="peer sr-only"
            />
            <div className="h-10 w-20 rounded-full bg-gray-300 shadow-inner transition duration-300 peer-checked:bg-blue-500" />
            <div className="absolute left-1 top-1 h-8 w-8 transform rounded-full bg-white shadow-md transition duration-300 peer-checked:translate-x-10" />
          </label>
          <p className="text-xs text-gray-400">
            กดปุ่มเพื่อ เปิด/ปิด การจำลองข้อมูล (ทุก 3 วินาที)
          </p>
        </div>

        <div className="my-4 border-t border-gray-200" />

        <div className="mb-2 flex items-center justify-between">
          <span className="text-xs font-bold uppercase text-gray-500">
            Live Logs
          </span>
          <button
            onClick={() => setLogs([])}
            className="text-xs text-blue-500 hover:underline"
          >
            Clear
          </button>
        </div>
        <div className="h-64 overflow-y-auto rounded-lg bg-slate-900 p-4 font-mono text-xs leading-relaxed text-green-400 shadow-inner">
          {logs.length === 0 ? (
            <p className="opacity-30">-- Ready to start --</p>
          ) : (
            logs.map((line) => (
              <div
                key={line.id}
                className={
                  line.isError
                    ? "mb-1 border-l-2 border-red-500 pl-2 text-red-400"
                    : "mb-1 hover:text-green-300"
                }
              >
                &gt; {line.text}
              </div>
            ))
          )}
        </div>
      </div>
    </div>
  );
}
