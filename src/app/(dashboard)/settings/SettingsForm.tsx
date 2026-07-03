"use client";

import { useRouter } from "next/navigation";
import { useRef, useState } from "react";
import Link from "next/link";
import { toast } from "sonner";
import type { SiteSettings } from "@/lib/settings";

interface SettingsFormProps {
  initialSettings: SiteSettings;
}

export function SettingsForm({ initialSettings }: SettingsFormProps) {
  const router = useRouter();
  const formRef = useRef<HTMLFormElement>(null);
  const [submitting, setSubmitting] = useState(false);
  const [logoPreview, setLogoPreview] = useState<string | null>(
    initialSettings.site_logo ? `/uploads/${initialSettings.site_logo}` : null
  );

  async function handleSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setSubmitting(true);

    try {
      const formData = new FormData(event.currentTarget);
      const res = await fetch("/api/settings", {
        method: "POST",
        body: formData,
      });
      const data = await res.json();

      if (!res.ok) {
        toast.error(data.message ?? "เกิดข้อผิดพลาด");
        return;
      }

      toast.success(data.message ?? "บันทึกการตั้งค่าสำเร็จแล้ว!");
      router.refresh();
    } catch {
      toast.error("เกิดข้อผิดพลาดในการเชื่อมต่อ");
    } finally {
      setSubmitting(false);
    }
  }

  function handleLogoChange(event: React.ChangeEvent<HTMLInputElement>) {
    const file = event.target.files?.[0];
    if (file) {
      setLogoPreview(URL.createObjectURL(file));
    }
  }

  return (
    <div className="rounded-lg bg-white p-8 shadow-md">
      <form ref={formRef} onSubmit={handleSubmit} encType="multipart/form-data">
        <div className="mb-6 border-b border-gray-200 pb-6">
          <h2 className="text-xl font-semibold text-gray-700">ทั่วไป (General)</h2>
          <div className="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
              <label
                htmlFor="site_name"
                className="block text-sm font-medium text-gray-600"
              >
                ชื่อเว็บไซต์
              </label>
              <input
                type="text"
                name="site_name"
                id="site_name"
                defaultValue={initialSettings.site_name ?? ""}
                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>

            <div>
              <label
                htmlFor="site_logo"
                className="block text-sm font-medium text-gray-600"
              >
                โลโก้
              </label>
              <input
                type="file"
                name="site_logo"
                id="site_logo"
                accept="image/*"
                onChange={handleLogoChange}
                className="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-full file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100"
              />
              {logoPreview && (
                // eslint-disable-next-line @next/next/no-img-element
                <img
                  src={logoPreview}
                  alt="Current Logo"
                  className="mt-3 h-16 rounded"
                />
              )}
            </div>

            <div className="md:col-span-2">
              <label
                htmlFor="site_description"
                className="block text-sm font-medium text-gray-600"
              >
                คำอธิบายเว็บไซต์
              </label>
              <textarea
                name="site_description"
                id="site_description"
                rows={3}
                defaultValue={initialSettings.site_description ?? ""}
                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>
          </div>
        </div>

        <div>
          <h2 className="text-xl font-semibold text-gray-700">ขั้นสูง (Advanced)</h2>
          <div className="mt-4">
            <label className="flex items-center">
              <input
                type="checkbox"
                name="maintenance_mode"
                value="1"
                defaultChecked={initialSettings.maintenance_mode === "1"}
                className="h-4 w-4 rounded border-gray-300 text-indigo-600"
              />
              <span className="ml-2 text-sm text-gray-600">
                เปิดโหมดซ่อมบำรุง (Maintenance Mode)
              </span>
            </label>
          </div>
          <br />
          <Link
            href="/simulator"
            className="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            สุ่มจำลองค่า
          </Link>
        </div>

        <div className="mt-8 flex justify-end border-t border-gray-200 pt-6">
          <button
            type="submit"
            disabled={submitting}
            className="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-60"
          >
            {submitting ? "กำลังบันทึก..." : "บันทึกการเปลี่ยนแปลง"}
          </button>
        </div>
      </form>
    </div>
  );
}
