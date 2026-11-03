import { DEVICE_LABELS, type DeviceControls } from "./smartfarm-types";

interface DeviceStatusCardProps {
  controls: DeviceControls;
}

export function DeviceStatusCard({ controls }: DeviceStatusCardProps) {
  const entries = Object.entries(controls) as [keyof DeviceControls, boolean][];

  return (
    <div className="card rounded-2xl p-6 shadow-lg">
      <div className="mb-4 flex items-center justify-between">
        <h2 className="text-xl font-bold">สถานะอุปกรณ์</h2>
      </div>
      <div className="space-y-3 text-sm">
        {entries.map(([key, isOn], index) => (
          <div
            key={key}
            className={`flex items-center justify-between ${
              index < entries.length - 1 ? "border-b pb-1" : ""
            }`}
          >
            <p>{DEVICE_LABELS[key]}</p>
            <div className="flex items-center space-x-2">
              <span
                className={`font-bold ${isOn ? "text-green-500" : "text-red-500"}`}
              >
                {isOn ? "เปิด" : "ปิด"}
              </span>
              <div
                className={`h-3 w-3 rounded-full ${
                  isOn
                    ? "bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.7)]"
                    : "bg-red-400"
                }`}
              />
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
