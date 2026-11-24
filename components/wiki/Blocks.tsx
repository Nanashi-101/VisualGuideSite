// Content blocks used inside articles: callouts, exercises, file trees, key terms.
import { AlertTriangle, Info, Lightbulb, PencilLine, Folder, FileText, CheckCircle2 } from "lucide-react";
import { Mascot } from "../Mascot";

const tones = {
  note: { icon: Info, label: "Note", cls: "border-sky-400/40 bg-sky-500/5 [&_.ic]:text-sky-500" },
  tip: { icon: Lightbulb, label: "Tip", cls: "border-emerald-400/40 bg-emerald-500/5 [&_.ic]:text-emerald-500" },
  warning: { icon: AlertTriangle, label: "Careful", cls: "border-amber-400/50 bg-amber-500/5 [&_.ic]:text-amber-500" },
};

function Callout({ tone, title, children }: { tone: keyof typeof tones; title?: string; children: React.ReactNode }) {
  const t = tones[tone]; const Icon = t.icon;
  return (
    <aside className={`callout my-6 rounded-xl border-l-4 border p-4 ${t.cls}`}>
      <div className="mb-1 flex items-center gap-2 font-semibold text-[var(--ink)]"><Icon size={18} className="ic" />{title ?? t.label}</div>
      <div className="[&>*:first-child]:mt-0 [&>*:last-child]:mb-0">{children}</div>
    </aside>
  );
}
export const Note = (p: { title?: string; children: React.ReactNode }) => <Callout tone="note" {...p} />;
export const Tip = (p: { title?: string; children: React.ReactNode }) => <Callout tone="tip" {...p} />;
export const Warning = (p: { title?: string; children: React.ReactNode }) => <Callout tone="warning" {...p} />;

/** Mochi explains something in plain words. */
export function Mochi({ children }: { children: React.ReactNode }) {
  return (
    <aside className="my-6 flex items-start gap-3 rounded-2xl border border-[var(--line)] bg-[var(--soft)] p-4">
      <Mascot size={52} still />
      <div><div className="mb-1 text-sm font-semibold text-[var(--accent)]">Mochi explains</div><div className="[&>*:first-child]:mt-0 [&>*:last-child]:mb-0">{children}</div></div>
    </aside>
  );
}

/** Practice task. Put the answer inside <Solution>; it stays hidden until she opens it. */
export function Exercise({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <section className="exercise my-8 rounded-2xl border border-[var(--accent)]/30 bg-[var(--surface)] p-5 shadow-sm">
      <div className="mb-2 flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-[var(--accent)]"><PencilLine size={16} />Try it yourself</div>
      <div className="mb-2 text-lg font-semibold text-[var(--ink)]">{title}</div>
      <div className="[&>*:last-child]:mb-0">{children}</div>
    </section>
  );
}
export function Solution({ children }: { children: React.ReactNode }) {
  return (
    <details className="group mt-4 rounded-xl border border-[var(--line)] bg-[var(--bg)]">
      <summary className="flex min-h-11 cursor-pointer list-none items-center gap-2 px-4 font-medium text-[var(--ink)] marker:hidden">
        <CheckCircle2 size={16} className="text-emerald-500" />Show a solution<span className="ml-auto text-xs text-[var(--muted)] group-open:hidden">Try first!</span>
      </summary>
      <div className="border-t border-[var(--line)] px-4 pb-1 [&>*:first-child]:mt-3">{children}</div>
    </details>
  );
}

/** Folder tree from an indented list. Lines ending in "/" are folders; text after "#" is a comment. */
export function FileTree({ children }: { children: string }) {
  const lines = String(children).replace(/^\n|\n$/g, "").split("\n");
  return (
    <div className="not-prose my-6 overflow-x-auto rounded-xl border border-[var(--line)] bg-[var(--surface)] p-4 font-mono text-[13px] leading-7">
      {lines.map((l, i) => {
        const depth = (l.match(/^ */)?.[0].length ?? 0) / 2;
        const [name, comment] = l.trim().split(/\s+#\s*/);
        const folder = name.endsWith("/");
        return (
          <div key={i} className="flex items-center gap-2 whitespace-nowrap" style={{ paddingLeft: depth * 20 }}>
            {folder ? <Folder size={15} className="shrink-0 text-amber-500" /> : <FileText size={15} className="shrink-0 text-sky-500" />}
            <span className={folder ? "font-semibold text-[var(--ink)]" : "text-[var(--ink)]"}>{name}</span>
            {comment && <span className="text-[var(--muted)]">— {comment}</span>}
          </div>
        );
      })}
    </div>
  );
}

/** "In this article you will learn" box at the top of a page. */
export function Goals({ children }: { children: React.ReactNode }) {
  return (
    <div className="my-6 rounded-2xl border border-[var(--line)] bg-gradient-to-br from-[var(--soft)] to-transparent p-5">
      <div className="mb-2 text-sm font-semibold uppercase tracking-wide text-[var(--accent)]">What you will learn</div>
      <div className="[&_ul]:my-0 [&>*:last-child]:mb-0">{children}</div>
    </div>
  );
}
