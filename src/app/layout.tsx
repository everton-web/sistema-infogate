import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "InfoGate Gestão",
  description: "Gestão empresarial integrada",
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="pt-BR">
      <body>{children}</body>
    </html>
  );
}
