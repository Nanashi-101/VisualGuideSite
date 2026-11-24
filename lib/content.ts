// Server-only helpers that read the MDX source: headings for the table of contents and the search index.
import fs from "node:fs";
import path from "node:path";
import GithubSlugger from "github-slugger";
import { articles } from "./wiki";

export type Heading = { id: string; text: string; depth: 2 | 3 };

export function headings(slug: string): Heading[] {
  const raw = fs.readFileSync(path.join(process.cwd(), "content", `${slug}.mdx`), "utf8").replace(/```[\s\S]*?```/g, "");
  const slugger = new GithubSlugger(); // same algorithm as rehype-slug, so ids match the rendered headings
  return [...raw.matchAll(/^(##|###) (.+)$/gm)].map((m) => {
    const text = m[2].replace(/`/g, "").trim();
    return { id: slugger.slug(text), text, depth: m[1].length as 2 | 3 };
  });
}

export type SearchItem = { slug: string; title: string; section: string; heading?: string; id?: string };

export function searchIndex(): SearchItem[] {
  return articles.flatMap((a) => [
    { slug: a.slug, title: a.title, section: a.section },
    ...headings(a.slug).map((h) => ({ slug: a.slug, title: a.title, section: a.section, heading: h.text, id: h.id })),
  ]);
}
