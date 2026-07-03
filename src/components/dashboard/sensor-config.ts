export const SENSOR_CONFIG = {
  tds: { label: "TDS", color: "#14b8a6", icon: "fa-flask-vial", unit: "ppm" },
  ec: { label: "EC", color: "#38bdf8", icon: "fa-bolt", unit: "µS/cm" },
  sht40_temp: {
    label: "Temperature",
    color: "#f59e0b",
    icon: "fa-temperature-half",
    unit: "°C",
  },
  sht40_humi: {
    label: "Humidity",
    color: "#f43f5e",
    icon: "fa-droplet",
    unit: "%RH",
  },
  ph: {
    label: "pH",
    color: "#8b5cf6",
    icon: "fa-vial-circle-check",
    unit: "",
  },
} as const;

export type SensorKey = keyof typeof SENSOR_CONFIG;

export const SENSOR_KEYS = Object.keys(SENSOR_CONFIG) as SensorKey[];

export interface AllSensorsResponse {
  labels: string[];
  datasets: Record<SensorKey, number[]>;
}

export interface SensorHistoryResponse {
  labels: string[];
  data: number[];
  datasetLabel: string;
}
