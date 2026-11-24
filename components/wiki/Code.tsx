// Server component: syntax-highlights code at build time with Shiki (the highlighter VS Code uses).
import { createHighlighter, type Highlighter } from "shiki";
import { CopyButton } from "./CopyButton";
import { src } from "@/lib/src";

const LANGS = ["php", "html", "sql", "javascript", "typescript", "css", "bash", "powershell", "json", "apache", "ini", "diff"];
let hl: Promise<Highlighter> | undefined;
const highlighter = () => (hl ??= createHighlighter({ themes: ["github-dark-default"], langs: LANGS }));

export async function CodeBlock({ code, lang = "text", title }: { code: string; lang?: string; title?: string }) {
  const h = await highlighter();
  const html = h.codeToHtml(code, { lang: LANGS.includes(lang) ? lang : "text", theme: "github-dark-default" });
  return (
    <figure className="code-block not-prose group my-6 overflow-hidden rounded-xl border border-[var(--code-line)] bg-[var(--code)]">
      <figcaption className="flex items-center justify-between gap-3 border-b border-[var(--code-line)] px-4 py-2 text-xs text-slate-400">
        <span className="truncate font-mono">{title ?? lang}</span>
        <CopyButton text={code} />
      </figcaption>
      <div className="overflow-x-auto p-4 text-[13.5px] leading-relaxed" dangerouslySetInnerHTML={{ __html: html }} />
    </figure>
  );
}

/** MDX turns ```php fences into <pre><code className="language-php">. This unwraps them for CodeBlock. */
export function Pre({ children }: { children?: React.ReactNode }) {
  const c = (children as React.ReactElement<{ className?: string; children?: string }>)?.props ?? {};
  const [lang, ...rest] = (c.className ?? "").replace("language-", "").split(":"); // ```php:login.php sets a title
  return <CodeBlock code={String(c.children ?? "").replace(/\n$/, "")} lang={lang || "text"} title={rest.join(":") || undefined} />;
}

/** Shows lines from a real file in this folder, so examples always match the actual code. */
export function Source({ file, from = 1, to, lang }: { file: string; from?: number; to?: number; lang?: string }) {
  const ext = file.split(".").pop() ?? "text";
  return <CodeBlock code={src(file, from, to)} lang={lang ?? (ext === "js" ? "javascript" : ext)} title={`${file}${to ? `  (lines ${from}–${to})` : ""}`} />;
}
