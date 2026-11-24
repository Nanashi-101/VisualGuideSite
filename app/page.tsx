import Link from "next/link";
import * as Icons from "lucide-react";
import { ArrowRight, BookOpen, PencilLine, Search, FolderKanban, Flag } from "lucide-react";
import { Mascot } from "@/components/Mascot";
import { Reveal } from "@/components/ui";
import { Progress } from "@/components/wiki/Shell";
import { Preloader } from "@/components/wiki/Immersive";
import { articles, sections } from "@/lib/wiki";

const icon = (name: string) => (Icons as unknown as Record<string, React.ComponentType<{ size?: number; className?: string }>>)[name] ?? Icons.Circle;

export default function Home() {
  const hours = Math.round(articles.reduce((n, a) => n + a.minutes, 0) / 60);
  let chapter = 0;
  return (
    <main className="mx-auto max-w-6xl overflow-x-clip px-4 sm:px-6">
      <Preloader />

      {/* Hero */}
      <section className="relative grid items-center gap-10 py-16 sm:py-24 lg:grid-cols-[1.4fr_1fr]">
        <div>
          <span className="chip cursor-default bg-[var(--surface)]"><BookOpen size={14} />Computer and Media Lab · SRH Munich</span>
          <h1 className="mt-6 text-4xl font-bold leading-[1.1] tracking-tight text-[var(--ink)] sm:text-6xl">
            Hi Ishika.<br />Welcome to your <span className="brand bg-gradient-to-r from-blue-600 to-sky-400 bg-clip-text text-transparent">Code Garden</span>.
          </h1>
          <p className="mt-6 max-w-xl text-lg leading-relaxed text-[var(--text)]">
            A little world for everything the course teaches, from your first HTML form to a full PHP and MySQL website with Google Authenticator login.
            Walk the path one chapter at a time, play with the demos, and by the end you will write it all yourself.
          </p>
          <div className="mt-8 flex flex-wrap gap-3">
            <Link href="/wiki/welcome" className="inline-flex min-h-12 items-center gap-2 rounded-2xl bg-[var(--btn)] px-6 font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:-translate-y-0.5 hover:bg-blue-700">Begin the journey<ArrowRight size={18} /></Link>
            <Link href="/wiki/fitness-overview" className="inline-flex min-h-12 items-center gap-2 rounded-2xl border border-[var(--line)] bg-[var(--surface)] px-6 font-semibold text-[var(--ink)] transition hover:border-[var(--accent)]/50">See the project<FolderKanban size={18} /></Link>
          </div>
          <div className="mt-8 flex flex-wrap gap-x-8 gap-y-2 text-sm text-[var(--muted)]">
            <span><b className="text-[var(--ink)]">{articles.length}</b> chapters</span>
            <span><b className="text-[var(--ink)]">~{hours} h</b> of reading</span>
            <span><b className="text-[var(--ink)]">2</b> real projects</span>
          </div>
        </div>
        <div className="flex flex-col items-center gap-6">
          <div className="relative">
            <div aria-hidden className="absolute inset-4 rounded-full bg-gradient-to-br from-sky-200 to-indigo-200 blur-2xl dark:from-sky-500/20 dark:to-indigo-500/20" />
            <div className="relative"><Mascot size={180} /></div>
          </div>
          <div className="w-full max-w-sm"><Progress total={articles.length} /></div>
        </div>
      </section>

      {/* Three small promises */}
      <section className="grid gap-4 pb-20 sm:grid-cols-3">
        {[
          [BookOpen, "Follow the path", "Each chapter builds on the one before. The next one is always waiting at the bottom of the page."],
          [PencilLine, "Try it yourself", "Every chapter ends with small tasks. Give them a real try in XAMPP before peeking at the solution."],
          [Search, "Find anything", "Press Ctrl + K anywhere to jump to any topic. The cheat sheet and glossary are there for quick reminders."],
        ].map(([I, t, d], k) => {
          const C = I as typeof BookOpen;
          return (
            <Reveal key={t as string} delay={k * 0.08}>
              <div className="h-full glass rounded-3xl p-6">
                <div className="mb-3 flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--soft)] text-[var(--accent)]"><C size={20} /></div>
                <div className="font-semibold text-[var(--ink)]">{t as string}</div>
                <p className="mt-1 text-sm leading-relaxed text-[var(--muted)]">{d as string}</p>
              </div>
            </Reveal>
          );
        })}
      </section>

      {/* The journey: a winding path of stops */}
      <section className="pb-24">
        <Reveal>
          <div className="text-center">
            <h2 className="brand text-3xl text-[var(--ink)] sm:text-4xl">Your journey</h2>
            <p className="mx-auto mt-3 max-w-xl text-[var(--muted)]">Seven stops, from how the web works to building and protecting your own application.</p>
          </div>
        </Reveal>
        <ol className="relative mt-14">
          <div aria-hidden className="absolute bottom-0 left-5 top-0 w-0.5 bg-gradient-to-b from-sky-300 via-blue-400 to-indigo-300 md:left-1/2 md:-translate-x-1/2 dark:from-sky-500/40 dark:via-blue-500/40 dark:to-indigo-500/40" />
          {sections.map((s, n) => {
            const I = icon(s.icon);
            const right = n % 2 === 1;
            return (
              <li key={s.id} className="relative mb-12 pl-16 md:grid md:grid-cols-2 md:gap-16 md:pl-0">
                <div aria-hidden className="absolute left-0 top-2 flex h-10 w-10 items-center justify-center rounded-full border-4 border-[var(--bg)] bg-[var(--btn)] text-white shadow-lg shadow-blue-600/30 md:left-1/2 md:-translate-x-1/2"><I size={17} /></div>
                <Reveal delay={0.05} className={right ? "md:col-start-2" : ""}>
                  <div className="glass rounded-3xl p-6 transition hover:-translate-y-0.5">
                    <div className="text-xs font-semibold uppercase tracking-wider text-[var(--accent)]">Stop {n + 1}</div>
                    <h3 className="mt-1 text-xl font-bold text-[var(--ink)]">{s.title}</h3>
                    <ul className="mt-4 space-y-1">
                      {s.articles.map((a) => {
                        chapter++;
                        return (
                          <li key={a.slug}>
                            <Link href={`/wiki/${a.slug}`} className="group flex min-h-11 items-center gap-3 rounded-xl px-2 text-sm transition hover:bg-[var(--soft)]">
                              <span className="w-6 text-xs font-semibold text-[var(--muted)]">{String(chapter).padStart(2, "0")}</span>
                              <span className="flex-1 text-[var(--text)] group-hover:text-[var(--accent)]">{a.title}</span>
                              <span className="text-xs text-[var(--muted)]">{a.minutes} min</span>
                            </Link>
                          </li>
                        );
                      })}
                    </ul>
                  </div>
                </Reveal>
              </li>
            );
          })}
          <li className="relative pl-16 md:pl-0">
            <div aria-hidden className="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-full border-4 border-[var(--bg)] bg-emerald-500 text-white shadow-lg md:left-1/2 md:-translate-x-1/2"><Flag size={17} /></div>
            <p className="pt-2 text-[var(--muted)] md:pt-14 md:text-center">At the end of the path: you can build the fitness site yourself.</p>
          </li>
        </ol>
      </section>
    </main>
  );
}
