import type { Metadata } from "next";
import { Prompt, Sarabun } from "next/font/google";
import { Toaster } from "sonner";
import "./globals.css";

const prompt = Prompt({
  subsets: ["thai", "latin"],
  weight: ["300", "400", "500", "600", "700", "800"],
  variable: "--font-prompt",
  display: "swap",
});

const sarabun = Sarabun({
  subsets: ["thai", "latin"],
  weight: ["300", "400", "500", "700"],
  variable: "--font-sarabun",
  display: "swap",
});

export const metadata: Metadata = {
  title: "Aerosol | AeroFogPonic Farming",
  description: "Aerosol นวัตกรรม AeroFogPonic Farming",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="th" className={`${prompt.variable} ${sarabun.variable}`}>
      <head>
        {/* Vendored Font Awesome Pro v6 (self-hosted, not an npm package) — see public/vendor/fontawesome */}
        {/* eslint-disable-next-line @next/next/no-css-tags */}
        <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css" />
      </head>
      <body>
        {children}
        <Toaster position="top-center" richColors closeButton />
      </body>
    </html>
  );
}
