import { getAllSettings } from "@/lib/settings";
import { SettingsForm } from "./SettingsForm";

export const dynamic = "force-dynamic";

export default async function SettingsPage() {
  const settings = await getAllSettings();

  return (
    <div>
      <h1 className="mb-6 text-3xl font-bold text-gray-800">
        การตั้งค่าเว็บไซต์
      </h1>
      <SettingsForm initialSettings={settings} />
    </div>
  );
}
