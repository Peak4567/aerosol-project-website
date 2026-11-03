import { NextRequest, NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { query } from "@/lib/db";
import { hashPassword } from "@/lib/password";
import { updateUserSchema } from "@/lib/validation";

async function requireAdmin() {
  const session = await auth();
  if (session?.user?.level !== "admin") {
    return null;
  }
  return session;
}

export async function PATCH(
  request: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  const session = await requireAdmin();
  if (!session) {
    return NextResponse.json({ message: "ไม่ได้รับอนุญาต" }, { status: 403 });
  }

  const { id } = await params;
  const body = await request.json().catch(() => null);
  const parsed = updateUserSchema.safeParse(body);
  if (!parsed.success) {
    return NextResponse.json(
      { message: parsed.error.issues[0]?.message ?? "ข้อมูลไม่ถูกต้อง" },
      { status: 400 }
    );
  }

  const { username, email, level, password } = parsed.data;

  try {
    if (password) {
      const hashed = await hashPassword(password);
      await query(
        "UPDATE users SET username = ?, email = ?, level = ?, password = ? WHERE id = ?",
        [username, email, level, hashed, id]
      );
    } else {
      await query(
        "UPDATE users SET username = ?, email = ?, level = ? WHERE id = ?",
        [username, email, level, id]
      );
    }
    return NextResponse.json({ message: "แก้ไขผู้ใช้สำเร็จแล้ว!" });
  } catch (error) {
    console.error("update user failed:", error);
    return NextResponse.json(
      { message: "เกิดข้อผิดพลาดของฐานข้อมูล" },
      { status: 500 }
    );
  }
}

export async function DELETE(
  _request: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  const session = await requireAdmin();
  if (!session) {
    return NextResponse.json({ message: "ไม่ได้รับอนุญาต" }, { status: 403 });
  }

  const { id } = await params;

  try {
    await query("DELETE FROM users WHERE id = ?", [id]);
    return NextResponse.json({ message: "ลบผู้ใช้สำเร็จแล้ว!" });
  } catch (error) {
    console.error("delete user failed:", error);
    return NextResponse.json(
      { message: "เกิดข้อผิดพลาดในการลบข้อมูล" },
      { status: 500 }
    );
  }
}
