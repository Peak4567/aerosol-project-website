import { z } from "zod";

export const registerSchema = z
  .object({
    username: z.string().trim().min(3, "ชื่อผู้ใช้ต้องมีอย่างน้อย 3 ตัวอักษร"),
    email: z.string().trim().email("รูปแบบอีเมลไม่ถูกต้อง"),
    password: z.string().min(8, "รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร"),
    confirmPassword: z.string(),
  })
  .refine((data) => data.password === data.confirmPassword, {
    message: "รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน",
    path: ["confirmPassword"],
  });

export const userLevelSchema = z.enum(["member", "admin"]);

export const createUserSchema = z.object({
  username: z.string().trim().min(3),
  email: z.string().trim().email(),
  level: userLevelSchema,
  password: z.string().min(8),
});

export const updateUserSchema = z.object({
  username: z.string().trim().min(3),
  email: z.string().trim().email(),
  level: userLevelSchema,
  password: z.string().min(8).optional().or(z.literal("")),
});

export const SENSOR_COLUMNS = {
  tds: { column: "tds_value", label: "TDS (ppm)" },
  ec: { column: "ec_value", label: "EC (µS/cm)" },
  sht40_temp: { column: "temperature", label: "Temperature (°C)" },
  sht40_humi: { column: "humidity", label: "Humidity (%RH)" },
  ph: { column: "ph_value", label: "pH Value" },
} as const;

export type SensorType = keyof typeof SENSOR_COLUMNS;
