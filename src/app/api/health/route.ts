import { NextResponse } from "next/server";
import { getPool } from "@/lib/db";

/**
 * Lightweight DB connectivity check used by the live status badge in the
 * dashboard topbar. Runs a trivial query against the pool — cheap enough
 * to poll every few seconds.
 */
export async function GET() {
  try {
    await getPool().query("SELECT 1");
    return NextResponse.json({ connected: true });
  } catch (error) {
    console.error("DB health check failed:", error);
    return NextResponse.json({ connected: false }, { status: 503 });
  }
}
