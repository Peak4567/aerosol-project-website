"use client";

import Image from "next/image";
import Link from "next/link";
import { useState } from "react";
import { signOut } from "next-auth/react";
import { ConfirmDialog } from "@/components/ui/ConfirmDialog";

const NAV_LINKS = [
  { href: "#home", label: "หน้าหลัก" },
  { href: "#about", label: "เกี่ยวกับเรา" },
  { href: "#what", label: "ข้อสงสัย" },
  { href: "#myteam", label: "คณะผู้จัดทำ" },
];

interface NavbarProps {
  isLoggedIn: boolean;
}

export function Navbar({ isLoggedIn }: NavbarProps) {
  const [mobileOpen, setMobileOpen] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);

  return (
    <>
      <nav className="fixed top-0 start-0 z-20 w-full border-b border-gray-200 bg-white p-3">
        <div className="relative mx-auto flex max-w-screen-xl flex-wrap items-center justify-between p-2">
          <div className="absolute left-1/2 top-full flex h-24 w-24 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border-4 border-white bg-white shadow-lg">
            <Link href="/">
              <Image
                src="/img/aerosol_logo.png"
                alt="Aerosol Logo"
                width={100}
                height={100}
                className="h-25 w-25 object-contain"
              />
            </Link>
          </div>

          <div
            className={`${
              mobileOpen ? "block" : "hidden"
            } w-full md:flex md:w-auto md:items-center md:justify-between`}
          >
            <ul className="mt-4 flex flex-col rounded-lg border border-gray-100 bg-gray-50 p-4 font-medium md:mt-0 md:flex-row md:space-x-8 md:border-0 md:bg-white md:p-0 rtl:space-x-reverse">
              {NAV_LINKS.map((link) => (
                <li key={link.href}>
                  <a
                    href={link.href}
                    className="block rounded px-3 py-2 text-gray-900 transition-colors hover:bg-gray-100 md:p-0 md:hover:bg-transparent md:hover:text-emerald-500"
                  >
                    {link.label}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          <div className="flex items-center space-x-3 rtl:space-x-reverse">
            {isLoggedIn ? (
              <>
                <Link
                  href="/dashboard"
                  className="hidden rounded-lg bg-gradient-to-r from-emerald-500 to-green-600 px-5 py-2.5 text-center text-sm font-medium text-white transition-all hover:from-emerald-600 hover:to-green-700 md:block"
                >
                  Dashboard
                </Link>
                <button
                  onClick={() => setConfirmOpen(true)}
                  className="hidden items-center rounded-lg bg-red-500 px-5 py-2.5 text-center text-sm font-medium text-white transition-colors hover:bg-red-600 md:inline-flex"
                >
                  <i className="fa-solid fa-right-from-bracket mr-1" /> Logout
                </button>
              </>
            ) : (
              <Link
                href="/login"
                className="hidden rounded-lg bg-gradient-to-r from-emerald-500 to-green-600 px-5 py-2.5 text-center text-sm font-medium text-white transition-all hover:from-emerald-600 hover:to-green-700 md:block"
              >
                เข้าสู่ระบบ
              </Link>
            )}

            <button
              onClick={() => setMobileOpen((open) => !open)}
              type="button"
              className="inline-flex h-10 w-10 items-center justify-center rounded-lg p-2 text-sm text-gray-500 hover:bg-gray-100 focus:outline-none md:hidden"
            >
              <span className="sr-only">Open main menu</span>
              <i className="fa-solid fa-bars text-lg" />
            </button>
          </div>
        </div>
      </nav>

      <ConfirmDialog
        open={confirmOpen}
        title="ยืนยันการออกจากระบบ?"
        description="คุณต้องการออกจากระบบใช่หรือไม่"
        confirmLabel="ใช่, ออกจากระบบ"
        onCancel={() => setConfirmOpen(false)}
        onConfirm={() => signOut({ callbackUrl: "/" })}
      />
    </>
  );
}
