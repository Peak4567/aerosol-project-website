"use client";

import { useState } from "react";
import Image from "next/image";
import Link from "next/link";
import { useRouter, useSearchParams } from "next/navigation";
import { signIn } from "next-auth/react";
import { toast } from "sonner";
import { DbConnectionWarning } from "@/components/site/DbConnectionWarning";

export default function LoginForm() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [loading, setLoading] = useState(false);
  const [loginError, setLoginError] = useState<string | null>(null);

  const registered = searchParams.get("status") === "success";

  async function handleSubmit(event: React.FormEvent) {
    event.preventDefault();
    setLoginError(null);

    if (!username.trim() || !password) {
      toast.warning("กรุณากรอกชื่อผู้ใช้และรหัสผ่าน");
      return;
    }

    setLoading(true);
    const result = await signIn("credentials", {
      username,
      password,
      redirect: false,
    });
    setLoading(false);

    if (result?.error) {
      const message = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
      setLoginError(message);
      toast.error(message);
      return;
    }

    toast.success("เข้าสู่ระบบสำเร็จ! กำลังพาท่านไปที่หน้า Dashboard...");
    router.push("/dashboard");
    router.refresh();
  }

  return (
    <div
      className="relative flex min-h-screen items-center justify-center overflow-hidden p-4"
      style={{ fontFamily: "var(--font-body)" }}
    >
      <Image
        src="/img/background_2.png"
        alt=""
        fill
        priority
        className="-z-10 object-cover brightness-[0.6]"
      />

      <div className="auth-card w-full max-w-md p-8">
        <div className="mb-8 text-center">
          <Image
            src="/img/aerosol.png"
            alt="aerosol_logo"
            width={160}
            height={160}
            className="mx-auto h-32 w-auto"
          />
          <h2 className="mt-4 text-3xl font-bold text-gray-800">เข้าสู่ระบบ</h2>
          <p className="mt-2 text-gray-500">สัมผัสธรรมชาติแห่งการเชื่อมต่อ</p>
        </div>

        <DbConnectionWarning />

        {registered && (
          <div className="mb-5 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-700">
            ลงทะเบียนสำเร็จ! กรุณาเข้าสู่ระบบ
          </div>
        )}

        {loginError && (
          <div className="mb-5 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
            {loginError}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-5">
          <div className="relative">
            <i className="fa fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-500" />
            <input
              type="text"
              placeholder="ชื่อผู้ใช้"
              value={username}
              onChange={(e) => setUsername(e.target.value)}
              className="w-full rounded-lg border border-gray-300 py-3 pl-12 pr-4 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500"
              required
            />
          </div>

          <div className="relative">
            <i className="fa fa-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-500" />
            <input
              type="password"
              placeholder="รหัสผ่าน"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="w-full rounded-lg border border-gray-300 py-3 pl-12 pr-4 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500"
              required
            />
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full rounded-lg bg-gradient-to-r from-emerald-500 to-green-600 px-4 py-3 font-bold text-white transition-all duration-300 hover:scale-[1.02] hover:from-emerald-600 hover:to-green-700 hover:shadow-[0_0_15px_rgba(34,197,94,0.6)] focus:outline-none focus:ring-4 focus:ring-emerald-300 disabled:opacity-60"
          >
            {loading ? "กำลังเข้าสู่ระบบ..." : "เข้าสู่ระบบ"}
          </button>

          <div className="mt-4 text-center">
            <p className="text-gray-600">
              ยังไม่มีบัญชี?{" "}
              <Link
                href="/register"
                className="font-bold text-emerald-600 hover:underline"
              >
                สมัครสมาชิกที่นี่
              </Link>
            </p>
          </div>
        </form>
      </div>
    </div>
  );
}
