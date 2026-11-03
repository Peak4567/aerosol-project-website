"use client";

import { useState } from "react";

function downloadDoc(content: string, filename: string) {
  const blob = new Blob(["﻿", content], { type: "application/msword" });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = filename;
  link.click();
}

export function ResearchCard() {
  const [form, setForm] = useState({
    potatoName: "",
    variety: "",
    researcher: "",
    requirements: "",
    note: "",
  });

  function handleExport() {
    const content = `<html><body><h1>Potato Research</h1>
      <p>Crop: มันฝรั่ง (Potato)</p>
      <p>Name: ${form.potatoName}</p>
      <p>Variety: ${form.variety}</p>
      <p>Researcher: ${form.researcher}</p>
      <p>Reqs: ${form.requirements}</p>
      <p>Note: ${form.note}</p></body></html>`;
    downloadDoc(content, "Potato_Research.doc");
  }

  return (
    <div className="card rounded-2xl border-t-4 border-blue-500 p-6 shadow-lg">
      <div className="mb-4 flex items-center justify-between">
        <h2 className="text-xl font-bold text-blue-900">
          ข้อมูลงานวิจัยมันฝรั่ง
        </h2>
        <button
          onClick={handleExport}
          className="rounded bg-blue-600 px-3 py-1 text-sm text-white shadow"
        >
          Export Word
        </button>
      </div>
      <div className="space-y-4">
        <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
          <div>
            <label className="text-xs font-bold text-gray-500">ชนิดพืช</label>
            <input
              disabled
              value="มันฝรั่ง (Potato)"
              className="w-full rounded border bg-gray-100 p-2"
            />
          </div>
          <div>
            <label className="text-xs font-bold text-gray-500">
              ชื่อโปรเจกต์
            </label>
            <input
              value={form.potatoName}
              onChange={(e) =>
                setForm((f) => ({ ...f, potatoName: e.target.value }))
              }
              className="w-full rounded border p-2"
            />
          </div>
          <div>
            <label className="text-xs font-bold text-gray-500">สายพันธุ์</label>
            <input
              value={form.variety}
              onChange={(e) => setForm((f) => ({ ...f, variety: e.target.value }))}
              className="w-full rounded border p-2"
            />
          </div>
        </div>
        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label className="text-xs font-bold text-gray-500">
              ชื่อผู้วิจัย
            </label>
            <input
              value={form.researcher}
              onChange={(e) =>
                setForm((f) => ({ ...f, researcher: e.target.value }))
              }
              className="w-full rounded border p-2"
            />
          </div>
          <div>
            <label className="text-xs font-bold text-gray-500">
              สิ่งที่ต้องการ
            </label>
            <input
              value={form.requirements}
              onChange={(e) =>
                setForm((f) => ({ ...f, requirements: e.target.value }))
              }
              className="w-full rounded border p-2"
            />
          </div>
        </div>
        <div>
          <label className="text-xs font-bold text-gray-500">หมายเหตุ</label>
          <textarea
            rows={3}
            value={form.note}
            onChange={(e) => setForm((f) => ({ ...f, note: e.target.value }))}
            className="w-full rounded border p-2"
          />
        </div>
      </div>
    </div>
  );
}
