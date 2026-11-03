import { getCurrentUser } from "@/lib/current-user";
import { getSetting } from "@/lib/settings";
import { Navbar } from "@/components/site/Navbar";
import { Footer } from "@/components/site/Footer";
import { LandingContent } from "@/components/site/LandingContent";
import { MaintenanceNotice } from "@/components/site/MaintenanceNotice";

// Always hit the DB for maintenance_mode so an admin toggle takes
// effect immediately, matching the old check_maintenance.php behavior.
export const dynamic = "force-dynamic";

export default async function HomePage() {
  const [user, maintenanceMode] = await Promise.all([
    getCurrentUser(),
    getSetting("maintenance_mode", "0"),
  ]);

  const isBlocked = maintenanceMode === "1" && user?.level !== "admin";

  if (isBlocked) {
    return <MaintenanceNotice />;
  }

  return (
    <>
      <Navbar isLoggedIn={!!user} />
      <LandingContent />
      <Footer />
    </>
  );
}
