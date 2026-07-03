"use client";

import useSWR from "swr";
import { fetcher } from "@/lib/fetcher";

interface HealthResponse {
  connected: boolean;
}

export function DbConnectionWarning() {
  const { data, error, isLoading } = useSWR<HealthResponse>(
    "/api/health",
    fetcher,
    {
      refreshInterval: 5000,
      shouldRetryOnError: false,
    }
  );

  const disconnected = !isLoading && (error || data?.connected === false);

  if (!disconnected) return null;

  return (
    <div className="mb-5 flex items-start gap-2 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
      <i className="fa-solid fa-triangle-exclamation mt-0.5" />
      <span>
        ขณะนี้ไม่สามารถเชื่อมต่อฐานข้อมูลได้ ระบบเข้าสู่ระบบ/ลงทะเบียนอาจไม่ทำงาน
        กรุณาตรวจสอบการตั้งค่าฐานข้อมูล (.env) หรือติดต่อผู้ดูแลระบบ
      </span>
    </div>
  );
}
