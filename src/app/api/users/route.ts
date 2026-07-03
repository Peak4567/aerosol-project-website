import { NextRequest, NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { query } from "@/lib/db";
import { hashPassword } from "@/lib/password";
import { createUserSchema } from "@/lib/validation";

interface UserRow {
  id: number;
  username: string;
  email: string;
  level: "member" | "admin";
  created_at: string;
}

async function requireAdmin() {
  const session = await auth();
  if (session?.user?.level !== "admin") {
    return null;
  }
  return session;
}

export async function GET() {
  const session = await requireAdmin();
  if (!session) {
    return NextResponse.json({ message: "ไม่ได้รับอนุญาต" }, { status: 403 });
  }

  const users = await query<UserRow[]>(
    "SELECT id, username, email, level, created_at FROM users ORDER BY created_at DESC"
  );
  return NextResponse.json({ users });
}

export async function POST(request: NextRequest) {
  const session = await requireAdmin();
  if (!session) {
    return NextResponse.json({ message: "ไม่ได้รับอนุญาต" }, { status: 403 });
  }

  const body = await request.json().catch(() => null);
  const parsed = createUserSchema.safeParse(body);
  if (!parsed.success) {
    return NextResponse.json(
      { message: parsed.error.issues[0]?.message ?? "ข้อมูลไม่ถูกต้อง" },
      { status: 400 }
    );
  }

  const { username, email, level, password } = parsed.data;

  try {
    const hashed = await hashPassword(password);
    await query(
      "INSERT INTO users (username, email, password, level) VALUES (?, ?, ?, ?)",
      [username, email, hashed, level]
    );
    return NextResponse.json({ message: "เพิ่มผู้ใช้สำเร็จแล้ว!" }, { status: 201 });
  } catch (error) {
    console.error("create user failed:", error);
    return NextResponse.json(
      { message: "เกิดข้อผิดพลาดของฐานข้อมูล (ชื่อผู้ใช้หรืออีเมลอาจซ้ำ)" },
      { status: 500 }
    );
  }
}
