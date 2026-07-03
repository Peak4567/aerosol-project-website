import { redirect } from "next/navigation";
import { getCurrentUser } from "@/lib/current-user";
import { getSetting } from "@/lib/settings";
import { DashboardShell } from "@/components/dashboard/DashboardShell";

export const dynamic = "force-dynamic";

export default async function DashboardLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const user = await getCurrentUser();
  if (!user) {
    redirect("/login");
  }

  const siteName = await getSetting("site_name", "Aerosol");

  return (
    <DashboardShell
      siteName={siteName}
      username={user.username}
      isAdmin={user.level === "admin"}
    >
      {children}
    </DashboardShell>
  );
}
