import "./globals.css";
import Link from "next/link";
import { Fredoka, Inter, JetBrains_Mono } from "next/font/google";
import { Mascot } from "@/components/Mascot";
import { MobileNav, Motion, ReadingProgress, Search, ThemeToggle } from "@/components/wiki/Shell";
import { searchIndex } from "@/lib/content";

const inter = Inter({ subsets: ["latin"], variable: "--font-inter" });
const fredoka = Fredoka({ subsets: ["latin"], variable: "--font-fredoka", weight: ["500", "600"] });
const mono = JetBrains_Mono({ subsets: ["latin"], variable: "--font-mono" });

export const metadata = { title: { default: "Code Garden", template: "%s · Code Garden" }, description: "Ishika's wiki for the Computer and Media Lab: HTML, PHP, MySQL, security and the projects built with them." };

// Runs before paint so the saved theme (or the system setting) applies without a flash.
const themeScript = `try{const t=localStorage.getItem("theme");if(t==="dark"||(!t&&matchMedia("(prefers-color-scheme: dark)").matches))document.documentElement.classList.add("dark")}catch(e){}`;

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en" className={`${inter.variable} ${fredoka.variable} ${mono.variable}`} suppressHydrationWarning>
      <head><script dangerouslySetInnerHTML={{ __html: themeScript }} /></head>
      <body>
        <Motion>
          <ReadingProgress />
          <header className="sticky top-0 z-40 h-16 border-b border-[var(--line)] bg-[var(--bg)]/85 backdrop-blur">
            <div className="mx-auto flex h-full max-w-[90rem] items-center gap-3 px-4 sm:px-6">
              <MobileNav />
              <Link href="/" className="flex shrink-0 items-center gap-2"><Mascot size={34} still /><span className="brand hidden text-lg sm:inline">Code Garden</span></Link>
              <div className="flex flex-1 justify-end gap-2 sm:justify-center"><Search index={searchIndex()} /></div>
              <ThemeToggle />
            </div>
          </header>
          {children}
          <footer className="border-t border-[var(--line)] py-8 text-center text-sm text-[var(--muted)]">
            Code Garden · a wiki for the Computer and Media Lab, written for Ishika <span aria-hidden>💙</span>
          </footer>
        </Motion>
      </body>
    </html>
  );
}
