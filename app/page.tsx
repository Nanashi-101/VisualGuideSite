import Link from "next/link";
import * as Icons from "lucide-react";
import { ArrowRight, BookOpen, PencilLine, Search, FolderKanban } from "lucide-react";
import { Mascot } from "@/components/Mascot";
import { Progress } from "@/components/wiki/Shell";
import { articles, sections } from "@/lib/wiki";

const icon = (name: string) => (Icons as unknown as Record<string, React.ComponentType<{ size?: number; className?: string }>>)[name] ?? Icons.Circle;

export default function Home() {
  const hours = Math.round(articles.reduce((n, a) => n + a.minutes, 0) / 60);
  return (
    <main className="mx-auto max-w-6xl overflow-x-clip px-4 sm:px-6">
      {/* Hero */}
      <section className="relative py-16 sm:py-20">
        <div aria-hidden className="pointer-events-none absolute -right-16 top-0 h-80 w-80 rounded-full bg-sky-300/25 blur-3xl dark:bg-sky-500/10" />
        <div aria-hidden className="pointer-events-none absolute -left-20 top-32 h-64 w-64 rounded-full bg-indigo-300/20 blur-3xl dark:bg-indigo-500/10" />
        <div className="relative grid items-center gap-10 lg:grid-cols-[1.4fr_1fr]">
          <div>
            <span className="chip cursor-default"><BookOpen size={14} />Computer and Media Lab · SRH Munich</span>
            <h1 className="mt-5 text-4xl font-bold leading-tight tracking-tight text-[var(--ink)] sm:text-5xl">
              Hi Ishika. This is your <span className="brand text-[var(--accent)]">Code Garden</span>.
            </h1>
            <p className="mt-5 max-w-2xl text-lg leading-relaxed text-[var(--text)]">
              A wiki for everything taught in the course, from your first HTML form to a full PHP and MySQL website with Google Authenticator login.
              Every concept comes with explanations in plain words, real code, interactive demos and exercises, so you can write it yourself.
            </p>
            <div className="mt-8 flex flex-wrap gap-3">
              <Link href="/wiki/welcome" className="inline-flex min-h-12 items-center gap-2 rounded-xl bg-[var(--btn)] px-6 font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">Start learning<ArrowRight size={18} /></Link>
              <Link href="/wiki/fitness-overview" className="inline-flex min-h-12 items-center gap-2 rounded-xl border border-[var(--line)] bg-[var(--surface)] px-6 font-semibold text-[var(--ink)] transition hover:border-[var(--accent)]/50">See the project<FolderKanban size={18} /></Link>
            </div>
            <div className="mt-8 flex flex-wrap gap-x-8 gap-y-2 text-sm text-[var(--muted)]">
              <span><b className="text-[var(--ink)]">{articles.length}</b> articles</span>
              <span><b className="text-[var(--ink)]">~{hours} h</b> of reading</span>
              <span><b className="text-[var(--ink)]">2</b> real projects explained</span>
            </div>
          </div>
          <div className="flex flex-col items-center gap-6">
            <Mascot size={170} />
            <div className="w-full max-w-sm"><Progress total={articles.length} /></div>
          </div>
        </div>
      </section>

      {/* How to use */}
      <section className="grid gap-4 pb-14 sm:grid-cols-3">
        {[
          [BookOpen, "Read in order", "Each section builds on the one before. The sidebar shows the path; the next article is linked at the bottom of each page."],
          [PencilLine, "Do the exercises", "Every article ends with tasks. Try them in XAMPP before opening the solution. That is where learning happens."],
          [Search, "Look things up", "Press Ctrl + K anywhere to search every heading. The cheat sheet and glossary are there when you just need a reminder."],
        ].map(([I, t, d]) => {
          const C = I as typeof BookOpen;
          return (
            <div key={t as string} className="rounded-2xl border border-[var(--line)] bg-[var(--surface)] p-5">
              <div className="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--soft)] text-[var(--accent)]"><C size={20} /></div>
              <div className="font-semibold text-[var(--ink)]">{t as string}</div>
              <p className="mt-1 text-sm leading-relaxed text-[var(--muted)]">{d as string}</p>
            </div>
          );
        })}
      </section>

      {/* Learning path */}
      <section className="pb-20">
        <h2 className="text-2xl font-bold tracking-tight text-[var(--ink)]">The learning path</h2>
        <p className="mt-2 text-[var(--muted)]">Seven sections, from the basics of the web to building and securing a complete application.</p>
        <ol className="mt-8 grid gap-5 md:grid-cols-2">
          {sections.map((s, n) => {
            const I = icon(s.icon);
            return (
              <li key={s.id} className="rounded-2xl border border-[var(--line)] bg-[var(--surface)] p-5 transition hover:shadow-lg hover:shadow-blue-900/5">
                <div className="flex items-center gap-3">
                  <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--soft)] text-[var(--accent)]"><I size={20} /></div>
                  <div><div className="text-xs font-medium text-[var(--muted)]">Section {n + 1}</div><div className="font-semibold text-[var(--ink)]">{s.title}</div></div>
                </div>
                <ul className="mt-4 divide-y divide-[var(--line)]">
                  {s.articles.map((a) => (
                    <li key={a.slug}>
                      <Link href={`/wiki/${a.slug}`} className="group flex min-h-11 items-center gap-3 py-2 text-sm">
                        <span className="flex-1 text-[var(--text)] group-hover:text-[var(--accent)]">{a.title}</span>
                        <span className="text-xs text-[var(--muted)]">{a.minutes} min</span>
                        <ArrowRight size={14} className="text-[var(--muted)] transition group-hover:translate-x-0.5 group-hover:text-[var(--accent)]" />
                      </Link>
                    </li>
                  ))}
                </ul>
              </li>
            );
          })}
        </ol>
      </section>
    </main>
  );
}
