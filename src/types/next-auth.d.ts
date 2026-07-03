import type { DefaultSession } from "next-auth";

export type UserLevel = "member" | "admin";

declare module "next-auth" {
  interface User {
    id: string;
    username: string;
    level: UserLevel;
  }

  interface Session {
    user: {
      id: string;
      username: string;
      level: UserLevel;
    } & DefaultSession["user"];
  }
}

declare module "next-auth/jwt" {
  interface JWT {
    id: string;
    username: string;
    level: UserLevel;
  }
}

declare module "@auth/core/jwt" {
  interface JWT {
    id: string;
    username: string;
    level: UserLevel;
  }
}
