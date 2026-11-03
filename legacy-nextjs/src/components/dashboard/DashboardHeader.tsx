"use client";

import { useEffect, useState } from "react";

const DAYS = [
  "วันอาทิตย์",
  "วันจันทร์",
  "วันอังคาร",
  "วันพุธ",
  "วันพฤหัสบดี",
  "วันศุกร์",
  "วันเสาร์",
];
const MONTHS = [
  "มกราคม",
  "กุมภาพันธ์",
  "มีนาคม",
  "เมษายน",
  "พฤษภาคม",
  "มิถุนายน",
  "กรกฎาคม",
  "สิงหาคม",
  "กันยายน",
  "ตุลาคม",
  "พฤศจิกายน",
  "ธันวาคม",
];

function formatThaiDate(date: Date) {
  const day = DAYS[date.getDay()];
  const dateNum = date.getDate();
  const month = MONTHS[date.getMonth()];
  const year = date.getFullYear() + 543;
  return `${day}, ${dateNum} ${month} ${year}`;
}

function getTimePeriod(hour: number) {
  if (hour < 12) return "เช้า";
  if (hour < 18) return "บ่าย";
  return "เย็น";
}

export function DashboardHeader() {
  const [now, setNow] = useState<Date | null>(null);

  useEffect(() => {
    // eslint-disable-next-line react-hooks/set-state-in-effect -- client-only clock, must init after mount to avoid SSR/client mismatch
    setNow(new Date());
    const interval = setInterval(() => setNow(new Date()), 1000);
    return () => clearInterval(interval);
  }, []);

  const timeString = now
    ? now.toLocaleTimeString("th-TH", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
      })
    : "กำลังโหลดเวลา";

  return (
    <div className="mb-6 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
      <div className="flex flex-col gap-2">
        <h1 className="text-3xl font-bold text-gray-800">แผงหน้าปัด</h1>
        <div className="flex flex-wrap items-center gap-4 text-gray-600">
          <div className="flex items-center gap-2">
            <i className="fa-solid fa-calendar-day" />
            <span>{now ? formatThaiDate(now) : "กำลังโหลดวันที่"}</span>
          </div>
          <div className="flex items-center gap-2">
            <i className="fa-solid fa-clock" />
            <span>
              {timeString} {now ? `(${getTimePeriod(now.getHours())})` : ""}
            </span>
          </div>
        </div>
      </div>
      <button className="flex items-center gap-2 rounded-lg bg-green-500 px-5 py-2 font-semibold text-white shadow-md transition-colors hover:bg-green-600">
        <i className="fa-solid fa-download" />
        <span>Export</span>
      </button>
    </div>
  );
}
