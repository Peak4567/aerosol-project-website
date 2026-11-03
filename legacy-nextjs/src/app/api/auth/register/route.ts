import { NextRequest, NextResponse } from "next/server";
import { query } from "@/lib/db";
import { hashPassword } from "@/lib/password";
import { registerSchema } from "@/lib/validation";

interface ExistingUserRow {
  id: number;
}

export async function POST(request: NextRequest) {
  const body = await request.json().catch(() => null);
  if (!body) {
    return NextResponse.json({ message: "ข้อมูลไม่ถูกต้อง" }, { status: 400 });
  }

  const parsed = registerSchema.safeParse(body);
  if (!parsed.success) {
    const message = parsed.error.issues[0]?.message ?? "ข้อมูลไม่ถูกต้อง";
    return NextResponse.json({ message }, { status: 400 });
  }

  const { username, email, password } = parsed.data;

  try {
    const existing = await query<ExistingUserRow[]>(
      "SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1",
      [username, email]
    );
    if (existing.length > 0) {
      return NextResponse.json(
        { message: "ชื่อผู้ใช้หรืออีเมลนี้มีอยู่ในระบบแล้ว" },
        { status: 409 }
      );
    }

    const hashed = await hashPassword(password);
    await query(
      "INSERT INTO users (username, email, password, level) VALUES (?, ?, ?, 'member')",
      [username, email, hashed]
    );

    return NextResponse.json({ message: "ลงทะเบียนสำเร็จ" }, { status: 201 });
  } catch (error) {
    console.error("register failed:", error);
    return NextResponse.json(
      { message: "ไม่สามารถเชื่อมต่อฐานข้อมูลได้ กรุณาลองใหม่ภายหลัง" },
      { status: 503 }
    );
  }
}
