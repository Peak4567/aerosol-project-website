"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useState, type ReactNode } from "react";
import { signOut } from "next-auth/react";
import { ConfirmDialog } from "@/components/ui/ConfirmDialog";
import { DbStatusBadge } from "@/components/dashboard/DbStatusBadge";

interface NavItem {
  href: string;
  label: string;
  icon: string;
}

const MAIN_NAV: NavItem[] = [
  { href: "/dashboard", label: "แผงหน้าปัด", icon: "fa-table-columns" },
  { href: "/smartfarm", label: "ปัญญาประดิษฐ์", icon: "fa-user-robot" },
];

const ADMIN_NAV: NavItem[] = [
  { href: "/settings", label: "ตั้งค่าหน้าเว็บ", icon: "fa-browser" },
  { href: "/users", label: "จัดการผู้ใช้", icon: "fa-users" },
];

interface DashboardShellProps {
  siteName: string;
  username: string;
  isAdmin: boolean;
  children: ReactNode;
}

export function DashboardShell({
  siteName,
  username,
  isAdmin,
  children,
}: DashboardShellProps) {
  const pathname = usePathname();
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);

  function NavLink({ item }: { item: NavItem }) {
    const active = pathname === item.href;
    return (
      <li>
        <Link
          href={item.href}
          onClick={() => setSidebarOpen(false)}
          className={`flex items-center rounded-lg p-2 transition-colors ${
            active
              ? "bg-green-50 text-green-700"
              : "text-gray-700 hover:bg-gray-100"
          }`}
        >
          <i
            className={`fa-solid ${item.icon} h-6 w-6 ${
              active ? "text-green-600" : "text-gray-500"
            }`}
          />
          <span className="ms-3">{item.label}</span>
        </Link>
      </li>
    );
  }

  return (
    <div className="min-h-screen bg-slate-50">
      {sidebarOpen && (
        <div
          className="fixed inset-0 z-30 bg-black/40 sm:hidden"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      <aside
        className={`fixed left-0 top-0 z-40 h-screen w-64 border-r border-gray-200 bg-white shadow-xl transition-transform sm:translate-x-0 ${
          sidebarOpen ? "translate-x-0" : "-translate-x-full"
        }`}
      >
        <div className="flex items-center justify-center border-b border-gray-200 p-5">
          <span className="self-center whitespace-nowrap text-2xl font-semibold text-gray-800">
            {siteName}
          </span>
        </div>
        <div className="flex h-full flex-col justify-between overflow-y-auto px-3 pb-[100px] pt-4">
          <ul className="space-y-1 font-medium">
            <li className="px-2 pb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">
              เมนูหลัก
            </li>
            {MAIN_NAV.map((item) => (
              <NavLink key={item.href} item={item} />
            ))}

            {isAdmin && (
              <>
                <li className="px-2 pb-1 pt-4 text-xs font-semibold uppercase tracking-wide text-gray-400">
                  ตั้งค่า
                </li>
                {ADMIN_NAV.map((item) => (
                  <NavLink key={item.href} item={item} />
                ))}
              </>
            )}
          </ul>

          <ul className="mt-4 space-y-1 border-t border-gray-200 pt-4 font-medium">
            <li>
              <Link
                href="/"
                className="group flex items-center rounded-lg p-2 text-gray-700 hover:bg-gray-100"
              >
                <i className="fa-solid fa-home h-6 w-6 text-gray-500 group-hover:text-gray-900" />
                <span className="ms-3 flex-1 whitespace-nowrap">
                  กลับหน้าหลัก
                </span>
              </Link>
            </li>
          </ul>
        </div>
      </aside>

      <div className="sm:ml-64">
        <nav className="sticky top-0 z-20 w-full border-b border-gray-200 bg-white shadow-sm">
          <div className="px-4 py-3">
            <div className="flex items-center justify-between">
              <div className="flex items-center">
                <button
                  onClick={() => setSidebarOpen((open) => !open)}
                  type="button"
                  className="inline-flex items-center rounded-lg p-2 text-sm text-gray-500 hover:bg-gray-100 focus:outline-none sm:hidden"
                >
                  <span className="sr-only">Open sidebar</span>
                  <i className="fa-solid fa-bars text-xl" />
                </button>
                <h1 className="ml-2 text-xl font-semibold text-gray-800 sm:ml-0">
                  Dashboard
                </h1>
              </div>

              <div className="flex items-center space-x-4">
                <DbStatusBadge />
                <span className="hidden text-sm font-medium text-gray-600 sm:inline">
                  {username}
                </span>
                <button
                  onClick={() => setConfirmOpen(true)}
                  type="button"
                  className="rounded-full p-2 text-gray-600 hover:bg-gray-100"
                  title="ออกจากระบบ"
                >
                  <i className="fa-solid fa-right-from-bracket text-lg" />
                </button>
              </div>
            </div>
          </div>
        </nav>

        <main className="p-4 md:p-6">{children}</main>
      </div>

      <ConfirmDialog
        open={confirmOpen}
        title="ยืนยันการออกจากระบบ?"
        description="คุณต้องการออกจากระบบใช่หรือไม่"
        confirmLabel="ใช่, ออกจากระบบ"
        onCancel={() => setConfirmOpen(false)}
        onConfirm={() => signOut({ callbackUrl: "/" })}
      />
    </div>
  );
}
