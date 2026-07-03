import { NextResponse } from "next/server";
import { query } from "@/lib/db";

/**
 * Dev-tool endpoint used by /simulator to insert one random sensor
 * reading, mirroring the old api_simulate.php (same random ranges).
 */
export async function GET() {
  const temp = Math.round((2500 + Math.random() * 1000)) / 100;
  const humid = Math.round((5000 + Math.random() * 3000)) / 100;
  const tds = Math.round(Math.random() * 500);
  const ec = Math.round(Math.random() * 200) / 100;
  const ph = Math.round((600 + Math.random() * 200)) / 100;
  const createAt = new Date();

  try {
    await query(
      `INSERT INTO sensor (temperature, humidity, tds_value, ec_value, ph_value, create_at)
       VALUES (?, ?, ?, ?, ?, ?)`,
      [temp, humid, tds, ec, ph, createAt]
    );

    return NextResponse.json({
      status: "success",
      data: {
        time: createAt.toISOString(),
        temp,
        humid,
        ph,
        ec,
      },
    });
  } catch (error) {
    console.error("simulate insert failed:", error);
    return NextResponse.json(
      { status: "error", message: "Database error." },
      { status: 500 }
    );
  }
}
