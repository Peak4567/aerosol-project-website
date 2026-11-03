"use client";

import { useState } from "react";
import Image from "next/image";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { toast } from "sonner";
import { DbConnectionWarning } from "@/components/site/DbConnectionWarning";

export default function RegisterPage() {
  const router = useRouter();
  const [form, setForm] = useState({
    username: "",
    email: "",
    password: "",
    confirmPassword: "",
  });
  const [loading, setLoading] = useState(false);
  const [formError, setFormError] = useState<string | null>(null);

  function update(field: keyof typeof form) {
    return (event: React.ChangeEvent<HTMLInputElement>) =>
      setForm((prev) => ({ ...prev, [field]: event.target.value }));
  }

  async function handleSubmit(event: React.FormEvent) {
    event.preventDefault();
    setLoading(true);
    setFormError(null);

    try {
      const res = await fetch("/api/auth/register", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(form),
      });
      const data = await res.json();

      if (!res.ok) {
        const message = data.message ?? "เกิดข้อผิดพลาด";
        setFormError(message);
        toast.error(message);
        return;
      }

      toast.success("ลงทะเบียนสำเร็จ! กำลังพาท่านไปที่หน้าเข้าสู่ระบบ...");
      setTimeout(() => router.push("/login?status=success"), 1200);
    } catch {
      const message = "เกิดข้อผิดพลาดในการเชื่อมต่อ";
      setFormError(message);
      toast.error(message);
    } finally {
      setLoading(false);
    }
  }

  return (
    <div
      className="relative flex min-h-screen items-center justify-center overflow-hidden p-4"
      style={{ fontFamily: "var(--font-body)" }}
    >
      <Image
        src="/img/background_3.png"
        alt=""
        fill
        priority
        className="-z-10 object-cover brightness-[0.6]"
      />

      <div className="auth-card w-full max-w-md p-8">
        <Image
          src="/img/aerosol.png"
          alt="aerosol_logo"
          width={160}
          height={160}
          className="mx-auto mb-4 h-28 w-auto"
        />
        <h2 className="text-center text-3xl font-bold text-gray-800">
          สร้างบัญชีใหม่
        </h2>
        <p className="mb-6 mt-2 text-center text-gray-500">
          เริ่มต้นการเดินทางของคุณกับเรา
        </p>

        <DbConnectionWarning />

        {formError && (
          <div className="mb-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
            {formError}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="relative">
            <i className="fa fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" />
            <input
              type="text"
              placeholder="ชื่อผู้ใช้"
              value={form.username}
              onChange={update("username")}
              className="w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-3 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500"
              required
            />
          </div>

          <div className="relative">
            <i className="fa fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" />
            <input
              type="email"
              placeholder="อีเมล"
              value={form.email}
              onChange={update("email")}
              className="w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-3 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500"
              required
            />
          </div>

          <div className="relative">
            <i className="fa fa-key absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" />
            <input
              type="password"
              placeholder="รหัสผ่าน"
              value={form.password}
              onChange={update("password")}
              className="w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-3 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500"
              required
            />
          </div>

          <div className="relative">
            <i className="fa fa-check-double absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" />
            <input
              type="password"
              placeholder="ยืนยันรหัสผ่าน"
              value={form.confirmPassword}
              onChange={update("confirmPassword")}
              className="w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-3 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500"
              required
            />
          </div>

          <button
            type="submit"
            disabled={loading}
            className="!mt-6 w-full rounded-lg bg-gradient-to-r from-emerald-500 to-green-600 px-4 py-3 font-bold text-white transition-all duration-300 hover:scale-[1.02] hover:from-emerald-600 hover:to-green-700 hover:shadow-[0_0_15px_rgba(34,197,94,0.6)] focus:outline-none focus:ring-4 focus:ring-emerald-300 disabled:opacity-60"
          >
            {loading ? "กำลังลงทะเบียน..." : "ลงทะเบียน"}
          </button>

          <div className="mt-4 text-center">
            <p className="text-gray-600">
              เป็นสมาชิกอยู่แล้ว?{" "}
              <Link
                href="/login"
                className="font-bold text-emerald-600 hover:underline"
              >
                เข้าสู่ระบบที่นี่
              </Link>
            </p>
          </div>
        </form>
      </div>
    </div>
  );
}
