export interface ManualSensorConfig {
  key: "ph" | "humidity" | "temperature" | "ec" | "light" | "tds";
  label: string;
  unit: string;
  min: number;
  max: number;
  step: number;
}

export const MANUAL_SENSOR_CONFIG: ManualSensorConfig[] = [
  { key: "ph", label: "PH", unit: "", min: 0, max: 14, step: 0.1 },
  { key: "humidity", label: "ความชื้น (%)", unit: "%", min: 0, max: 100, step: 1 },
  { key: "temperature", label: "อุณหภูมิ (°C)", unit: "°C", min: 15, max: 40, step: 1 },
  { key: "ec", label: "EC", unit: "", min: 0, max: 5, step: 0.1 },
  { key: "light", label: "แสง (Light)", unit: " lux", min: 0, max: 20000, step: 100 },
  { key: "tds", label: "TDS (ppm)", unit: " ppm", min: 0, max: 2000, step: 10 },
];

export interface DeviceControls {
  return_pump_on: boolean;
  mister_pump_on: boolean;
  uv_light_on: boolean;
  ultrasonic_mister_on: boolean;
  exhaust_fan_on: boolean;
  mist_fan_on: boolean;
  grow_light_on: boolean;
}

export const DEVICE_LABELS: Record<keyof DeviceControls, string> = {
  return_pump_on: "ปั๊มน้ำดูดกลับ",
  mister_pump_on: "ปั๊มพ่นหมอก",
  uv_light_on: "หลอด UV",
  ultrasonic_mister_on: "หัวพ่นหมอกอัลตราโซนิก",
  exhaust_fan_on: "พัดลมระบาย",
  mist_fan_on: "พัดลมส่งหมอก",
  grow_light_on: "ไฟปลูกพืช",
};

export interface GrowthPeriod {
  id: number;
  start: number;
  end: number;
  ph: number;
  ec: number;
  temp: number;
  humid: number;
  lightStart: string;
  lightEnd: string;
  lightInt: number;
  tds: number;
}

export interface LogEntry {
  timestamp: string;
  level: "INFO" | "WARNING" | "ERROR";
  message: string;
}
