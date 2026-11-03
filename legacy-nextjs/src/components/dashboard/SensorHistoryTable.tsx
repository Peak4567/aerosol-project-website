"use client";

import { useMemo, useState } from "react";
import useSWR from "swr";
import { fetcher } from "@/lib/fetcher";
import {
  SENSOR_CONFIG,
  SENSOR_KEYS,
  type SensorHistoryResponse,
} from "./sensor-config";

const ITEMS_PER_PAGE = 5;

export function SensorHistoryTable() {
  const [sensorType, setSensorType] = useState<keyof typeof SENSOR_CONFIG>(
    "sht40_temp"
  );
  const [page, setPage] = useState(1);

  const { data } = useSWR<SensorHistoryResponse>(
    `/api/sensors?type=${sensorType}`,
    fetcher,
    { refreshInterval: 5000 }
  );

  function selectSensor(key: keyof typeof SENSOR_CONFIG) {
    setSensorType(key);
    setPage(1);
  }

  const rows = useMemo(() => {
    if (!data) return [];
    return data.data
      .map((value, index) => ({ value, label: data.labels[index] }))
      .reverse();
  }, [data]);

  const totalPages = Math.max(1, Math.ceil(rows.length / ITEMS_PER_PAGE));
  const currentPage = Math.min(page, totalPages);
  const pageRows = rows.slice(
    (currentPage - 1) * ITEMS_PER_PAGE,
    currentPage * ITEMS_PER_PAGE
  );

  return (
    <div className="card rounded-xl p-4 shadow-md sm:p-6">
      <div className="mb-4 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
        <h2 className="text-lg font-semibold text-gray-700">
          ประวัติข้อมูลเซ็นเซอร์
        </h2>
        <div className="flex flex-wrap gap-2">
          {SENSOR_KEYS.map((key) => (
            <button
              key={key}
              onClick={() => selectSensor(key)}
              className={`sensor-btn ${sensorType === key ? "active" : ""}`}
            >
              {SENSOR_CONFIG[key].label}
            </button>
          ))}
        </div>
      </div>

      <div className="overflow-x-auto">
        <table className="w-full text-left">
          <thead className="text-sm text-gray-400">
            <tr>
              <th className="w-16 px-3 py-2">#</th>
              <th className="px-3 py-2">
                ค่า ({data?.datasetLabel ?? "..."})
              </th>
              <th className="px-3 py-2 text-right">เวลา (Timestamp)</th>
            </tr>
          </thead>
          <tbody className="text-gray-600">
            {pageRows.length === 0 ? (
              <tr>
                <td colSpan={3} className="py-10 text-center text-gray-400">
                  ไม่พบข้อมูล...
                </td>
              </tr>
            ) : (
              pageRows.map((row, index) => {
                const rowNumber =
                  rows.length - (currentPage - 1) * ITEMS_PER_PAGE - index;
                return (
                  <tr key={`${row.label}-${index}`} className="border-t border-gray-100">
                    <td className="px-3 py-2 font-mono text-gray-400">
                      {rowNumber}
                    </td>
                    <td className="px-3 py-2 font-semibold">{row.value}</td>
                    <td className="px-3 py-2 text-right font-mono text-sm">
                      {row.label}
                    </td>
                  </tr>
                );
              })
            )}
          </tbody>
        </table>
      </div>

      {totalPages > 1 && (
        <div className="mt-4 flex items-center justify-center gap-1">
          <button
            onClick={() => setPage((p) => Math.max(1, p - 1))}
            disabled={currentPage === 1}
            className="pagination-btn"
          >
            «
          </button>
          {Array.from({ length: totalPages }, (_, i) => i + 1).map((p) => (
            <button
              key={p}
              onClick={() => setPage(p)}
              className={`pagination-btn ${p === currentPage ? "active" : ""}`}
            >
              {p}
            </button>
          ))}
          <button
            onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
            disabled={currentPage === totalPages}
            className="pagination-btn"
          >
            »
          </button>
        </div>
      )}
    </div>
  );
}
