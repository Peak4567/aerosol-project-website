"use client";

import useSWR from "swr";
import { fetcher } from "@/lib/fetcher";
import { DashboardHeader } from "@/components/dashboard/DashboardHeader";
import { StatCards } from "@/components/dashboard/StatCards";
import { SensorChart } from "@/components/dashboard/SensorChart";
import { SummaryList } from "@/components/dashboard/SummaryList";
import { SensorHistoryTable } from "@/components/dashboard/SensorHistoryTable";
import type { AllSensorsResponse } from "@/components/dashboard/sensor-config";

export default function DashboardPage() {
  const { data, isLoading } = useSWR<AllSensorsResponse>(
    "/api/sensors?type=all",
    fetcher,
    { refreshInterval: 5000 }
  );

  return (
    <div>
      <DashboardHeader />

      <div className="grid grid-cols-1 gap-6 lg:grid-cols-3 xl:grid-cols-2">
        <StatCards data={data} />

        <div className="card rounded-xl p-4 shadow-md sm:p-6 lg:col-span-2 xl:col-span-3">
          <h2 className="mb-2 text-lg font-semibold text-gray-700">
            Sensor Overview
          </h2>
          <SensorChart data={data} />
          <p className="mt-3 text-center text-xs text-gray-400">
            {isLoading
              ? "Loading data..."
              : `Last updated: ${new Date().toLocaleTimeString()}`}
          </p>
        </div>

        <div className="grid grid-cols-1 gap-6 lg:col-span-3 xl:col-span-4 xl:grid-cols-3">
          <div className="grid grid-cols-1 gap-6 lg:col-span-3 xl:col-span-4 xl:grid-cols-2">
            <SensorHistoryTable />
            <div className="card rounded-xl p-4 shadow-md sm:p-6 xl:col-span-1">
              <h2 className="mb-4 text-lg font-semibold text-gray-700">
                ข้อมูลเซนเซอร์
              </h2>
              <SummaryList data={data} />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
