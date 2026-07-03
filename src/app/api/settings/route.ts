import { NextRequest, NextResponse } from "next/server";
import { unlink, writeFile, mkdir } from "fs/promises";
import { existsSync } from "fs";
import path from "path";
import { auth } from "@/lib/auth";
import { query } from "@/lib/db";
import { getAllSettings } from "@/lib/settings";

const UPLOADS_DIR = path.join(process.cwd(), "public", "uploads");

async function upsertSetting(key: string, value: string) {
  await query(
    `INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)`,
    [key, value]
  );
}

export async function POST(request: NextRequest) {
  const session = await auth();
  if (session?.user?.level !== "admin") {
    return NextResponse.json({ message: "ไม่ได้รับอนุญาต" }, { status: 403 });
  }

  const formData = await request.formData();
  const currentSettings = await getAllSettings();

  for (const [key, value] of formData.entries()) {
    if (key === "maintenance_mode" || key === "site_logo") continue;
    if (typeof value === "string") {
      await upsertSetting(key, value);
    }
  }

  const maintenanceValue = formData.get("maintenance_mode") ? "1" : "0";
  await upsertSetting("maintenance_mode", maintenanceValue);

  const logo = formData.get("site_logo");
  if (logo instanceof File && logo.size > 0) {
    if (!existsSync(UPLOADS_DIR)) {
      await mkdir(UPLOADS_DIR, { recursive: true });
    }

    if (currentSettings.site_logo) {
      const oldPath = path.join(UPLOADS_DIR, currentSettings.site_logo);
      if (existsSync(oldPath)) {
        await unlink(oldPath).catch(() => undefined);
      }
    }

    const safeName = logo.name.replace(/[^a-zA-Z0-9._-]/g, "_");
    const fileName = `${Date.now()}_${safeName}`;
    const buffer = Buffer.from(await logo.arrayBuffer());
    await writeFile(path.join(UPLOADS_DIR, fileName), buffer);
    await upsertSetting("site_logo", fileName);
  }

  return NextResponse.json({ message: "บันทึกการตั้งค่าสำเร็จแล้ว!" });
}
