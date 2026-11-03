"use client";

import type { LogEntry } from "./smartfarm-types";

interface SystemStatusModalProps {
  open: boolean;
  logs: LogEntry[];
  onClose: () => void;
}

function downloadDoc(content: string, filename: string) {
  const blob = new Blob(["﻿", content], { type: "application/msword" });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = filename;
  link.click();
}

export function SystemStatusModal({ open, logs, onClose }: SystemStatusModalProps) {
  if (!open) return null;

  const errors = logs.filter((l) => l.level === "ERROR");
  const commands = logs.filter((l) => l.level !== "ERROR");

  function handleExport() {
    const content = `<html><body><h1>System Log</h1>${errors
      .map((l) => `<p>[${l.timestamp}] ${l.level}: ${l.message}</p>`)
      .join("")}<hr>${commands
      .map((l) => `<p>[${l.timestamp}] ${l.level}: ${l.message}</p>`)
      .join("")}</body></html>`;
    downloadDoc(content, "System_Logs.doc");
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div className="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-8 shadow-2xl">
        <div className="mb-6 flex items-center justify-between border-b pb-4">
          <h2 className="text-2xl font-bold text-gray-800">
            รายละเอียดสถานะระบบ
          </h2>
          <button
            onClick={onClose}
            className="text-2xl font-bold text-gray-500 hover:text-red-500"
          >
            &times;
          </button>
        </div>

        <div className="space-y-6">
          <div>
            <h3 className="mb-2 font-bold text-red-600">
              รายงานข้อผิดพลาด (Error Logs)
            </h3>
            <div className="h-48 space-y-2 overflow-y-auto rounded-lg border border-red-100 bg-red-50 p-4 font-mono text-sm">
              {errors.length === 0 && (
                <p className="text-gray-400">ไม่มีข้อผิดพลาด</p>
              )}
              {errors
                .slice()
                .reverse()
                .map((log, i) => (
                  <div key={i}>
                    <span className="text-xs text-gray-500">
                      [{log.timestamp}]
                    </span>{" "}
                    <b>{log.level}:</b> {log.message}
                  </div>
                ))}
            </div>
          </div>
          <div>
            <h3 className="mb-2 font-bold text-gray-700">
              ประวัติคำสั่งและการทำงาน
            </h3>
            <div className="h-48 space-y-1 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 p-4 font-mono text-sm">
              {commands
                .slice()
                .reverse()
                .map((log, i) => (
                  <div key={i}>
                    <span className="text-xs text-gray-500">
                      [{log.timestamp}]
                    </span>{" "}
                    <b>{log.level}:</b> {log.message}
                  </div>
                ))}
            </div>
          </div>
        </div>

        <div className="mt-8 flex justify-end space-x-3 border-t pt-4">
          <button
            onClick={onClose}
            className="rounded-lg bg-gray-200 px-4 py-2 font-medium text-gray-700 hover:bg-gray-300"
          >
            ปิด
          </button>
          <button
            onClick={handleExport}
            className="rounded-lg bg-indigo-600 px-4 py-2 font-bold text-white shadow hover:bg-indigo-700"
          >
            Export Logs (.doc)
          </button>
        </div>
      </div>
    </div>
  );
}
