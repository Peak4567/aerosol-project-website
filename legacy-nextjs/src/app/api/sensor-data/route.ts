import { NextRequest, NextResponse } from "next/server";
import { query } from "@/lib/db";

/**
 * Ingestion endpoint for the ESP32 device / simulator. Kept
 * field-name-compatible with the old sensor_data.php so no firmware
 * changes are needed — just point the device at this URL instead.
 * Accepts application/x-www-form-urlencoded, multipart/form-data, or
 * application/json.
 */
async function readFields(request: NextRequest): Promise<Record<string, string>> {
  const contentType = request.headers.get("content-type") ?? "";
  if (contentType.includes("application/json")) {
    const body = await request.json().catch(() => ({}));
    const fields: Record<string, string> = {};
    for (const [key, value] of Object.entries(body ?? {})) {
      fields[key] = String(value);
    }
    return fields;
  }

  const formData = await request.formData();
  const fields: Record<string, string> = {};
  for (const [key, value] of formData.entries()) {
    fields[key] = String(value);
  }
  return fields;
}

export async function POST(request: NextRequest) {
  const fields = await readFields(request);
  const data: Record<string, number> = {};

  if (fields.ph_value !== undefined) data.ph_value = parseFloat(fields.ph_value);
  if (fields.tds_value !== undefined) data.tds_value = parseFloat(fields.tds_value);
  if (fields.ec_value !== undefined) data.ec_value = parseFloat(fields.ec_value);
  if (fields.temperature !== undefined && parseFloat(fields.temperature) > -999) {
    data.temperature = parseFloat(fields.temperature);
  }
  if (fields.humidity !== undefined && parseFloat(fields.humidity) > -999) {
    data.humidity = parseFloat(fields.humidity);
  }

  const columns = Object.keys(data).filter((key) => !Number.isNaN(data[key]));
  if (columns.length === 0) {
    return NextResponse.json(
      { status: "error", message: "No valid sensor data received." },
      { status: 400 }
    );
  }

  try {
    const placeholders = columns.map(() => "?").join(", ");
    const sql = `INSERT INTO sensor (${columns.join(", ")}) VALUES (${placeholders})`;
    await query(sql, columns.map((key) => data[key]));

    return NextResponse.json(
      { status: "success", message: "Record created successfully." },
      { status: 201 }
    );
  } catch (error) {
    console.error("sensor-data insert failed:", error);
    return NextResponse.json(
      { status: "error", message: "Database error." },
      { status: 500 }
    );
  }
}
