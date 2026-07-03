import { query } from "@/lib/db";
import { UsersTable, type UserRow } from "./UsersTable";

export const dynamic = "force-dynamic";

export default async function UsersPage() {
  const users = await query<UserRow[]>(
    "SELECT id, username, email, level, created_at FROM users ORDER BY created_at DESC"
  );

  return (
    <div>
      <h1 className="mb-6 text-3xl font-bold text-gray-800">⚙️ จัดการผู้ใช้</h1>
      <UsersTable initialUsers={users} />
    </div>
  );
}
