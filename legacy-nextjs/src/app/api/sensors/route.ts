import { NextRequest, NextResponse } from "next/server";
import { format } from "date-fns";
import { query } from "@/lib/db";
import { SENSOR_COLUMNS, SensorType } from "@/lib/validation";

interface AllSensorRow {
  create_at: string;
  tds_value: number;
  ec_value: number;
  temperature: number;
  humidity: number;
  ph_value: number;
}

/**
 * Consolidates the old get_all_sensors_data.php (?type=all, combined
 * chart + stat cards, last 100 rows) and get_sensor_history.php
 * (?type=<sensor>, single-series history table) into one route.
 */
export async function GET(request: NextRequest) {
  const type = (request.nextUrl.searchParams.get("type") ?? "all") as
    | "all"
    | SensorType;

  try {
    if (type === "all") {
      const rows = await query<AllSensorRow[]>(
        `SELECT create_at, tds_value, ec_value, temperature, humidity, ph_value
         FROM (SELECT * FROM sensor ORDER BY create_at DESC LIMIT 100) AS latest
         ORDER BY create_at ASC`
      );

      const labels: string[] = [];
      const datasets = {
        tds: [] as number[],
        ec: [] as number[],
        sht40_temp: [] as number[],
        sht40_humi: [] as number[],
        ph: [] as number[],
      };

      for (const row of rows) {
        labels.push(format(new Date(row.create_at), "HH:mm:ss"));
        datasets.tds.push(row.tds_value);
        datasets.ec.push(row.ec_value);
        datasets.sht40_temp.push(row.temperature);
        datasets.sht40_humi.push(row.humidity);
        datasets.ph.push(row.ph_value);
      }

      return NextResponse.json({ labels, datasets });
    }

    const sensorConfig = SENSOR_COLUMNS[type];
    if (!sensorConfig) {
      return NextResponse.json(
        { labels: [], data: [], datasetLabel: "Invalid Sensor" },
        { status: 400 }
      );
    }

    const rows = await query<Record<string, string | number>[]>(
      `SELECT create_at, ${sensorConfig.column} AS value
       FROM (SELECT create_at, ${sensorConfig.column} FROM sensor ORDER BY id DESC LIMIT 100) AS latest
       ORDER BY create_at ASC`
    );

    const labels = rows.map((row) =>
      format(new Date(row.create_at as string), "dd/MM/yyyy HH:mm:ss")
    );
    const data = rows.map((row) => Number(row.value));

    return NextResponse.json({ labels, data, datasetLabel: sensorConfig.label });
  } catch (error) {
    console.error("sensors query failed:", error);
    return NextResponse.json(
      { error: "Database error" },
      { status: 500 }
    );
  }
}
