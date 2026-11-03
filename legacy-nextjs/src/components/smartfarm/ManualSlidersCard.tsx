"use client";

import { MANUAL_SENSOR_CONFIG } from "./smartfarm-types";

interface ManualSlidersCardProps {
  targets: Record<string, number>;
  current: Record<string, number>;
  onChange: (key: string, value: number) => void;
}

export function ManualSlidersCard({
  targets,
  current,
  onChange,
}: ManualSlidersCardProps) {
  return (
    <div className="card rounded-lg p-4">
      <h2 className="mb-4 text-xl font-semibold text-gray-800">
        ตั้งเป้าหมาย (ควบคุมเอง)
      </h2>
      <div className="space-y-6">
        {MANUAL_SENSOR_CONFIG.map((config) => (
          <div key={config.key}>
            <div className="mb-1 flex justify-between text-xs">
              <label className="font-semibold text-gray-600">
                {config.label}
              </label>
              <span className="font-mono text-lg text-gray-800">
                {(current[config.key] ?? 0).toFixed(1)}
                {config.unit}
              </span>
            </div>
            <input
              type="range"
              min={config.min}
              max={config.max}
              step={config.step}
              value={targets[config.key] ?? config.min}
              onChange={(e) => onChange(config.key, parseFloat(e.target.value))}
              className="accent-track w-full"
            />
            <p className="text-right text-sm text-gray-500">
              เป้าหมาย:{" "}
              <span className="font-mono text-green-600">
                {(targets[config.key] ?? 0).toFixed(1)}
                {config.unit}
              </span>
            </p>
          </div>
        ))}
      </div>
    </div>
  );
}
