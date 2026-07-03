import { query } from "@/lib/db";

export type SiteSettings = Record<string, string>;

interface SettingRow {
  setting_key: string;
  setting_value: string | null;
}

/**
 * Reads every row from `settings` fresh from the DB. Intentionally
 * uncached (not React.cache-wrapped) because maintenance_mode is checked
 * on every request in the public layout and must reflect admin changes
 * immediately.
 */
export async function getAllSettings(): Promise<SiteSettings> {
  try {
    const rows = await query<SettingRow[]>(
      "SELECT setting_key, setting_value FROM settings"
    );
    const settings: SiteSettings = {};
    for (const row of rows) {
      settings[row.setting_key] = row.setting_value ?? "";
    }
    return settings;
  } catch (error) {
    console.error("Failed to load settings:", error);
    return {};
  }
}

export async function getSetting(key: string, fallback = ""): Promise<string> {
  const settings = await getAllSettings();
  return settings[key] ?? fallback;
}
