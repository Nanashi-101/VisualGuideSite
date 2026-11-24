import Link from "next/link";
import { notFound } from "next/navigation";
import { ChevronLeft, ChevronRight, Clock, BarChart3 } from "lucide-react";
import { articles, findArticle } from "@/lib/wiki";
import { headings } from "@/lib/content";
import { MarkLearned, Sidebar, Toc } from "@/components/wiki/Shell";

export const dynamicParams = false;
export const generateStaticParams = () => articles.map((a) => ({ slug: a.slug }));

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }) {
  const a = findArticle((await params).slug);
  return a ? { title: a.title, description: a.summary } : {};
}

export default async function WikiPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const a = findArticle(slug);
  if (!a) notFound();
  const { default: Body } = await import(`@/content/${slug}.mdx`);
  const i = articles.indexOf(a);
  const prev = articles[i - 1], next = articles[i + 1];

  return (
    <div className="mx-auto flex max-w-[90rem] gap-10 px-4 sm:px-6">
      <Sidebar />
      <main className="min-w-0 flex-1 py-10">
        <article className="mx-auto max-w-3xl">
          <nav aria-label="Breadcrumb" className="mb-4 flex items-center gap-1.5 text-sm text-[var(--muted)]">
            <Link href="/" className="hover:text-[var(--ink)]">Home</Link><ChevronRight size={14} /><span>{a.section}</span>
          </nav>
          <h1 className="text-3xl font-bold tracking-tight text-[var(--ink)] sm:text-4xl">{a.title}</h1>
          <p className="mt-3 text-lg text-[var(--muted)]">{a.summary}</p>
          <div className="mt-4 flex flex-wrap gap-2 text-sm">
            <span className="chip cursor-default"><Clock size={13} />{a.minutes} min read</span>
            <span className="chip cursor-default"><BarChart3 size={13} />{a.level}</span>
          </div>
          <div className="prose mt-8"><Body /></div>
          <div className="mt-14 flex flex-wrap items-center justify-between gap-4 border-t border-[var(--line)] pt-8">
            <MarkLearned slug={slug} />
          </div>
          <nav className="mt-8 grid gap-4 sm:grid-cols-2" aria-label="Next and previous article">
            {prev ? (
              <Link href={`/wiki/${prev.slug}`} className="group rounded-2xl border border-[var(--line)] bg-[var(--surface)] p-4 transition hover:border-[var(--accent)]/50 hover:shadow-md">
                <div className="flex items-center gap-1 text-xs text-[var(--muted)]"><ChevronLeft size={14} />Previous</div>
                <div className="mt-1 font-semibold text-[var(--ink)] group-hover:text-[var(--accent)]">{prev.title}</div>
              </Link>
            ) : <span />}
            {next && (
              <Link href={`/wiki/${next.slug}`} className="group rounded-2xl border border-[var(--line)] bg-[var(--surface)] p-4 text-right transition hover:border-[var(--accent)]/50 hover:shadow-md">
                <div className="flex items-center justify-end gap-1 text-xs text-[var(--muted)]">Next<ChevronRight size={14} /></div>
                <div className="mt-1 font-semibold text-[var(--ink)] group-hover:text-[var(--accent)]">{next.title}</div>
              </Link>
            )}
          </nav>
        </article>
      </main>
      <Toc headings={headings(slug)} />
    </div>
  );
}
