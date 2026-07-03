import type { NextAuthConfig } from "next-auth";

/**
 * Edge-safe auth config (no DB/bcrypt imports) shared between the full
 * config in auth.ts (used by route handlers, runs on Node) and
 * middleware.ts (runs on the Edge runtime). Keeping providers out of
 * this file is what makes it edge-safe.
 */
export const authConfig = {
  pages: {
    signIn: "/login",
  },
  session: {
    strategy: "jwt",
  },
  providers: [],
  callbacks: {
    jwt({ token, user }) {
      if (user) {
        token.id = user.id;
        token.username = user.username;
        token.level = user.level;
      }
      return token;
    },
    session({ session, token }) {
      if (session.user) {
        session.user.id = token.id;
        session.user.username = token.username;
        session.user.level = token.level;
      }
      return session;
    },
    authorized({ auth, request }) {
      const isLoggedIn = !!auth?.user;
      const { pathname } = request.nextUrl;
      const isDashboardRoute = pathname.startsWith("/dashboard");

      if (!isDashboardRoute) return true;
      if (!isLoggedIn) return false;

      const isAdminOnlyRoute =
        pathname.startsWith("/dashboard/settings") ||
        pathname.startsWith("/dashboard/users");

      if (isAdminOnlyRoute && auth?.user?.level !== "admin") {
        return Response.redirect(new URL("/dashboard", request.nextUrl));
      }

      return true;
    },
  },
} satisfies NextAuthConfig;
