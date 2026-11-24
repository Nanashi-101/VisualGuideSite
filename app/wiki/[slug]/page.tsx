import Link from "next/link";
import { notFound } from "next/navigation";
import { ArrowLeft, ArrowRight, Clock, BarChart3 } from "lucide-react";
import { articles, findArticle } from "@/lib/wiki";
import { headings } from "@/lib/content";
import { MarkLearned, Toc } from "@/components/wiki/Shell";
import { ProseReveal } from "@/components/wiki/Immersive";
import { Mascot } from "@/components/Mascot";

export const dynamicParams = false;
export const generateStaticParams = () => articles.map((a) => ({ slug: a.slug }));

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }) {
  const a = findArticle((await params).slug);
  return a ? { title: a.title, description: a.summary } : {};
}

// One soft tint per section, so each part of the journey feels like its own place (all within the blue theme).
const tints: Record<string, string> = {
  start: "from-sky-200/70 via-blue-100/60", html: "from-cyan-200/70 via-sky-100/60", php: "from-blue-200/70 via-indigo-100/60",
  db: "from-teal-200/60 via-cyan-100/60", auth: "from-indigo-200/70 via-violet-100/50", projects: "from-sky-200/70 via-cyan-100/60", ref: "from-slate-200/70 via-blue-100/50",
};

export default async function WikiPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const a = findArticle(slug);
  if (!a) notFound();
  const { default: Body } = await import(`@/content/${slug}.mdx`);
  const i = articles.indexOf(a);
  const prev = articles[i - 1], next = articles[i + 1];

  return (
    <div className="mx-auto max-w-6xl px-4 pb-10 pt-8 sm:px-6">
      {/* Chapter banner */}
      <header className={`glass relative overflow-hidden rounded-3xl bg-gradient-to-br ${tints[a.sectionId]} to-transparent px-6 py-10 sm:px-10 sm:py-14 dark:from-blue-500/10 dark:via-indigo-500/5`}>
        <div className="relative z-10 max-w-2xl">
          <div className="flex flex-wrap items-center gap-2 text-sm font-medium text-[var(--accent)]">
            <span>Chapter {String(i + 1).padStart(2, "0")}</span><span className="text-[var(--muted)]">·</span><span className="text-[var(--muted)]">{a.section}</span>
          </div>
          <h1 className="mt-3 text-3xl font-bold tracking-tight text-[var(--ink)] sm:text-5xl">{a.title}</h1>
          <p className="mt-4 text-lg text-[var(--text)]">{a.summary}</p>
          <div className="mt-6 flex flex-wrap gap-2 text-sm">
            <span className="chip glass cursor-default"><Clock size={13} />{a.minutes} min read</span>
            <span className="chip glass cursor-default"><BarChart3 size={13} />{a.level}</span>
          </div>
        </div>
        <div className="pointer-events-none absolute -bottom-4 right-6 hidden opacity-90 sm:block" aria-hidden><Mascot size={130} /></div>
      </header>

      <div className="mt-12 flex gap-12">
        <main className="min-w-0 flex-1">
          <article className="mx-auto max-w-3xl">
            <div className="prose"><Body /></div>
            <ProseReveal />

            <div className="mt-16 flex justify-center"><MarkLearned slug={slug} /></div>

            {next && (
              <Link href={`/wiki/${next.slug}`} className="group mt-10 flex items-center gap-5 overflow-hidden rounded-3xl bg-gradient-to-r from-blue-600 to-sky-500 p-6 text-white shadow-xl shadow-blue-600/20 transition hover:-translate-y-1 sm:p-8">
                <div className="min-w-0 flex-1">
                  <div className="text-sm font-medium text-blue-100">Up next · Chapter {String(i + 2).padStart(2, "0")}</div>
                  <div className="mt-1 text-xl font-bold sm:text-2xl">{next.title}</div>
                  <div className="mt-1 hidden text-blue-100 sm:block">{next.summary}</div>
                </div>
                <span className="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white/20 transition group-hover:translate-x-1"><ArrowRight size={22} /></span>
              </Link>
            )}
            {prev && (
              <Link href={`/wiki/${prev.slug}`} className="mt-4 inline-flex min-h-11 items-center gap-2 text-sm text-[var(--muted)] hover:text-[var(--ink)]">
                <ArrowLeft size={15} />Back to {prev.title}
              </Link>
            )}
          </article>
        </main>
        <Toc headings={headings(slug)} />
      </div>
    </div>
  );
}
