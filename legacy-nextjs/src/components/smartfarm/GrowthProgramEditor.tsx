"use client";

import type { GrowthPeriod } from "./smartfarm-types";

interface GrowthProgramEditorProps {
  periods: GrowthPeriod[];
  onChange: (id: number, patch: Partial<GrowthPeriod>) => void;
  onAdd: () => void;
  onRemove: (id: number) => void;
  onSave: () => void;
}

function lightDuration(start: string, end: string) {
  if (!start || !end) return "คำนวณ...";
  const [sh, sm] = start.split(":").map(Number);
  const [eh, em] = end.split(":").map(Number);
  let diff = eh + em / 60 - (sh + sm / 60);
  if (diff < 0) diff += 24;
  return `${diff.toFixed(1)} ชม.`;
}

function MiniSlider({
  label,
  value,
  min,
  max,
  step,
  onChange,
}: {
  label: string;
  value: number;
  min: number;
  max: number;
  step: number;
  onChange: (value: number) => void;
}) {
  return (
    <div>
      <div className="mb-1 flex justify-between text-xs">
        <label className="font-semibold text-gray-600">{label}</label>
        <span className="font-bold text-blue-600">{value}</span>
      </div>
      <div className="flex items-center space-x-2">
        <button
          type="button"
          onClick={() => onChange(Math.max(min, +(value - step).toFixed(2)))}
          className="precision-btn flex h-7 w-7 items-center justify-center rounded-full bg-gray-200 font-bold text-gray-700 hover:bg-gray-300"
        >
          -
        </button>
        <input
          type="range"
          min={min}
          max={max}
          step={step}
          value={value}
          onChange={(e) => onChange(parseFloat(e.target.value))}
          className="accent-track flex-grow"
        />
        <button
          type="button"
          onClick={() => onChange(Math.min(max, +(value + step).toFixed(2)))}
          className="precision-btn flex h-7 w-7 items-center justify-center rounded-full bg-gray-200 font-bold text-gray-700 hover:bg-gray-300"
        >
          +
        </button>
      </div>
    </div>
  );
}

export function GrowthProgramEditor({
  periods,
  onChange,
  onAdd,
  onRemove,
  onSave,
}: GrowthProgramEditorProps) {
  return (
    <div className="card rounded-2xl p-6 shadow-lg">
      <div className="mb-4 flex items-center justify-between">
        <h2 className="text-xl font-bold text-gray-700">
          3. โปรแกรมการเติบโตอัตโนมัติ
        </h2>
        <button
          onClick={onSave}
          className="rounded bg-green-500 px-3 py-2 text-xs font-bold text-white hover:bg-green-600"
        >
          บันทึก
        </button>
      </div>

      <div className="space-y-6">
        {periods.map((period) => (
          <div
            key={period.id}
            className="relative rounded-xl border border-gray-200 bg-gray-50 p-4"
          >
            <div className="mb-4 flex items-center justify-between">
              <div className="flex items-center space-x-2 text-gray-800">
                <span className="font-bold">วันที่</span>
                <input
                  type="number"
                  value={period.start}
                  onChange={(e) =>
                    onChange(period.id, { start: parseInt(e.target.value) || 0 })
                  }
                  className="profile-input w-16 text-center"
                />
                <span className="font-bold">ถึง</span>
                <input
                  type="number"
                  value={period.end}
                  onChange={(e) =>
                    onChange(period.id, { end: parseInt(e.target.value) || 0 })
                  }
                  className="profile-input w-16 text-center"
                />
              </div>
              <button
                onClick={() => onRemove(period.id)}
                className="text-2xl font-bold text-gray-400 hover:text-red-500"
              >
                &times;
              </button>
            </div>

            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
              <MiniSlider
                label="PH"
                value={period.ph}
                min={0}
                max={14}
                step={0.1}
                onChange={(v) => onChange(period.id, { ph: v })}
              />
              <MiniSlider
                label="EC"
                value={period.ec}
                min={0}
                max={5}
                step={0.1}
                onChange={(v) => onChange(period.id, { ec: v })}
              />
              <MiniSlider
                label="อุณหภูมิ"
                value={period.temp}
                min={15}
                max={40}
                step={1}
                onChange={(v) => onChange(period.id, { temp: v })}
              />
              <MiniSlider
                label="ความชื้น"
                value={period.humid}
                min={0}
                max={100}
                step={1}
                onChange={(v) => onChange(period.id, { humid: v })}
              />

              <div className="col-span-1 rounded border border-yellow-200 bg-yellow-50 p-3 md:col-span-2">
                <div className="mb-2 flex items-center justify-between">
                  <label className="font-bold text-yellow-800">
                    ระบบแสง (Light Schedule)
                  </label>
                  <span className="font-mono text-sm text-yellow-700">
                    {lightDuration(period.lightStart, period.lightEnd)}
                  </span>
                </div>
                <div className="mb-2 flex flex-wrap gap-2 md:flex-nowrap">
                  <div className="flex-1">
                    <label className="text-xs text-gray-500">เปิด (Start)</label>
                    <input
                      type="time"
                      value={period.lightStart}
                      onChange={(e) =>
                        onChange(period.id, { lightStart: e.target.value })
                      }
                      className="w-full rounded border p-1 text-sm"
                    />
                  </div>
                  <div className="flex-1">
                    <label className="text-xs text-gray-500">ปิด (End)</label>
                    <input
                      type="time"
                      value={period.lightEnd}
                      onChange={(e) =>
                        onChange(period.id, { lightEnd: e.target.value })
                      }
                      className="w-full rounded border p-1 text-sm"
                    />
                  </div>
                </div>
                <MiniSlider
                  label="ความเข้ม"
                  value={period.lightInt}
                  min={0}
                  max={20000}
                  step={100}
                  onChange={(v) => onChange(period.id, { lightInt: v })}
                />
              </div>

              <MiniSlider
                label="TDS"
                value={period.tds}
                min={0}
                max={2000}
                step={10}
                onChange={(v) => onChange(period.id, { tds: v })}
              />
            </div>
          </div>
        ))}
      </div>

      <button
        onClick={onAdd}
        className="mt-6 w-full rounded-xl border-2 border-dashed border-blue-300 p-3 font-bold text-blue-500 hover:bg-blue-50"
      >
        + เพิ่มช่วงเวลาใหม่
      </button>
    </div>
  );
}
