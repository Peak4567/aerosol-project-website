import { SENSOR_CONFIG, SENSOR_KEYS, type AllSensorsResponse } from "./sensor-config";

interface SummaryListProps {
  data?: AllSensorsResponse;
}

export function SummaryList({ data }: SummaryListProps) {
  return (
    <ul className="space-y-4">
      {SENSOR_KEYS.map((key) => {
        const config = SENSOR_CONFIG[key];
        const values = data?.datasets?.[key];
        const latest = values && values.length > 0 ? values[values.length - 1] : "N/A";
        return (
          <li key={key} className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                <i className={`fa-solid ${config.icon}`} />
              </div>
              <span className="font-medium text-gray-600">{config.label}</span>
            </div>
            <span className="font-bold text-gray-800">
              {latest} {config.unit}
            </span>
          </li>
        );
      })}
    </ul>
  );
}
