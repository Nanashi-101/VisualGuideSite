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

// Light by default; dark only when she chose it with the toggle. Runs before paint so there is no flash.
const themeScript = `try{if(localStorage.getItem("theme")==="dark")document.documentElement.classList.add("dark")}catch(e){}`;

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en" className={`${inter.variable} ${fredoka.variable} ${mono.variable}`} suppressHydrationWarning>
      <head><script dangerouslySetInnerHTML={{ __html: themeScript }} /></head>
      <body>
        <Motion>
          <ReadingProgress />
          <div className="ambient" aria-hidden><span /><span /><span /></div>
          <header className="sticky top-3 z-40 px-3 sm:top-4 sm:px-6">
            <div className="mx-auto flex h-14 max-w-5xl items-center gap-2 glass rounded-2xl px-2 sm:gap-3 sm:px-3">
              <MobileNav />
              <Link href="/" className="flex shrink-0 items-center gap-2"><Mascot size={34} still /><span className="brand hidden text-lg sm:inline">Code Garden</span></Link>
              <div className="flex flex-1 justify-end gap-2 sm:justify-center"><Search index={searchIndex()} /></div>
              <ThemeToggle />
            </div>
          </header>
          {children}
          <footer className="py-10 text-center text-sm text-[var(--muted)]">
            Code Garden · a wiki for the Computer and Media Lab, written for Ishika <span aria-hidden>💙</span>
          </footer>
        </Motion>
      </body>
    </html>
  );
}
