import mysql from "mysql2/promise";

declare global {
  var __aerosolDbPool: mysql.Pool | undefined;
}

function createPool() {
  return mysql.createPool({
    host: process.env.DB_HOST ?? "localhost",
    port: Number(process.env.DB_PORT ?? 3306),
    user: process.env.DB_USER ?? "aerosol",
    password: process.env.DB_PASSWORD ?? "",
    database: process.env.DB_NAME ?? "aerosol",
    waitForConnections: true,
    connectionLimit: 10,
    maxIdle: 10,
    idleTimeout: 60_000,
    dateStrings: true,
  });
}

// Reuse the pool across hot-reloads / route handler invocations in dev.
const pool = global.__aerosolDbPool ?? createPool();
if (process.env.NODE_ENV !== "production") {
  global.__aerosolDbPool = pool;
}

export function getPool() {
  return pool;
}

export async function query<T = unknown>(
  sql: string,
  params: unknown[] = []
): Promise<T> {
  const [rows] = await pool.query(sql, params);
  return rows as T;
}
