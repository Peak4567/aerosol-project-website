import { auth } from "@/lib/auth";

export async function getCurrentUser() {
  const session = await auth();
  return session?.user ?? null;
}

export async function isAdmin() {
  const user = await getCurrentUser();
  return user?.level === "admin";
}
