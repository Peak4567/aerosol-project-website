"use client";

import useSWR from "swr";
import { fetcher } from "@/lib/fetcher";

interface HealthResponse {
  connected: boolean;
}

export function DbStatusBadge() {
  const { data, error, isLoading } = useSWR<HealthResponse>(
    "/api/health",
    fetcher,
    {
      refreshInterval: 5000,
      shouldRetryOnError: false,
    }
  );

  const connected = !error && !!data?.connected;
  const label = isLoading
    ? "กำลังตรวจสอบ..."
    : connected
      ? "เชื่อมต่อฐานข้อมูลแล้ว"
      : "ขาดการเชื่อมต่อฐานข้อมูล";

  return (
    <div
      title={label}
      className={`flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-medium transition-colors ${
        isLoading
          ? "border-gray-200 bg-gray-50 text-gray-500"
          : connected
            ? "border-green-200 bg-green-50 text-green-700"
            : "border-red-200 bg-red-50 text-red-700"
      }`}
    >
      <span
        className={`h-2 w-2 rounded-full ${
          isLoading
            ? "bg-gray-400"
            : connected
              ? "animate-pulse bg-green-500 shadow-[0_0_6px_rgba(34,197,94,0.7)]"
              : "bg-red-500"
        }`}
      />
      <span className="hidden sm:inline">{label}</span>
    </div>
  );
}
