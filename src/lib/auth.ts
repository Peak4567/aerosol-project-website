import NextAuth from "next-auth";
import Credentials from "next-auth/providers/credentials";
import { authConfig } from "@/lib/auth.config";
import { query } from "@/lib/db";
import { verifyPassword } from "@/lib/password";
import type { UserLevel } from "@/types/next-auth";

interface UserRow {
  id: number;
  username: string;
  password: string;
  level: UserLevel;
}

export const { handlers, auth, signIn, signOut } = NextAuth({
  ...authConfig,
  providers: [
    Credentials({
      name: "credentials",
      credentials: {
        username: { label: "Username", type: "text" },
        password: { label: "Password", type: "password" },
      },
      authorize: async (credentials) => {
        const username = credentials?.username;
        const password = credentials?.password;
        if (typeof username !== "string" || typeof password !== "string") {
          return null;
        }

        const rows = await query<UserRow[]>(
          "SELECT id, username, password, level FROM users WHERE username = ? LIMIT 1",
          [username.trim()]
        );
        const user = rows[0];
        if (!user) return null;

        const valid = await verifyPassword(password, user.password);
        if (!valid) return null;

        return {
          id: String(user.id),
          username: user.username,
          level: user.level,
        };
      },
    }),
  ],
});
