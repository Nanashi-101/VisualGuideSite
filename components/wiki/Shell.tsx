"use client";
// Interactive parts of the wiki frame. All state lives in small hooks from lib/hooks.ts.
import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { AnimatePresence, MotionConfig, motion } from "framer-motion";
import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import { createPortal } from "react-dom";
import * as Icons from "lucide-react";
import { Moon, Sun, Search as SearchIcon, Menu, X, CheckCircle2, Circle, ArrowRight, CornerDownLeft } from "lucide-react";
import { sections, type Section } from "@/lib/wiki";
import type { Heading, SearchItem } from "@/lib/content";
import { LEARNED_KEY, useHotkey, useLocalStorage, useScrollProgress, useScrollSpy } from "@/lib/hooks";

/** Framer animations follow the "reduce motion" setting of her device. */
export const Motion = ({ children }: { children: React.ReactNode }) => <MotionConfig reducedMotion="user">{children}</MotionConfig>;

/** Overlays render into <body> (portal): the header's backdrop blur would otherwise trap fixed elements inside it. */
function useMounted() {
  const [m, setM] = useState(false);
  useEffect(() => setM(true), []);
  return m;
}

/* ---------- Theme ---------- */
export function ThemeToggle() {
  const [dark, setDark] = useState(false);
  useEffect(() => setDark(document.documentElement.classList.contains("dark")), []);
  const flip = () => {
    const next = !dark; setDark(next);
    document.documentElement.classList.toggle("dark", next);
    try { localStorage.setItem("theme", next ? "dark" : "light"); } catch { /* storage blocked */ }
  };
  return (
    <button onClick={flip} className="icon-btn" aria-label={dark ? "Switch to light mode" : "Switch to dark mode"}>
      {dark ? <Sun size={18} /> : <Moon size={18} />}
    </button>
  );
}

/* ---------- Search (Ctrl + K) ---------- */
export function Search({ index }: { index: SearchItem[] }) {
  const [open, setOpen] = useState(false);
  const [q, setQ] = useState("");
  const [sel, setSel] = useState(0);
  const router = useRouter();
  const input = useRef<HTMLInputElement>(null);
  useHotkey("k", useCallback(() => setOpen(true), []));

  const results = useMemo(() => {
    const words = q.toLowerCase().split(/\s+/).filter(Boolean);
    if (!words.length) return index.filter((i) => !i.heading).slice(0, 8);
    return index.filter((i) => words.every((w) => `${i.title} ${i.heading ?? ""} ${i.section}`.toLowerCase().includes(w))).slice(0, 12);
  }, [q, index]);

  useEffect(() => { setSel(0); }, [q]);
  useEffect(() => { if (open) setTimeout(() => input.current?.focus(), 10); else setQ(""); }, [open]);
  const go = (r?: SearchItem) => { if (!r) return; setOpen(false); router.push(`/wiki/${r.slug}${r.id ? `#${r.id}` : ""}`); };
  const mounted = useMounted();

  return (
    <>
      <button onClick={() => setOpen(true)} className="flex min-h-10 w-full max-w-xs items-center gap-2 glass rounded-xl px-3 text-sm text-[var(--muted)] transition hover:border-[var(--accent)]/50" aria-label="Search the wiki">
        <SearchIcon size={16} /><span className="hidden sm:inline">Search the wiki…</span>
        <kbd className="ml-auto hidden rounded border border-[var(--line)] px-1.5 text-[11px] sm:inline">Ctrl K</kbd>
      </button>
      {mounted && createPortal(<AnimatePresence>
        {open && (
          <motion.div className="fixed inset-0 z-50 flex items-start justify-center bg-slate-900/40 p-4 pt-[12vh] backdrop-blur-sm" initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} onClick={() => setOpen(false)}>
            <motion.div role="dialog" aria-modal="true" aria-label="Search" className="w-full max-w-xl overflow-hidden glass glass-strong rounded-2xl shadow-2xl"
              initial={{ y: -12, scale: 0.98 }} animate={{ y: 0, scale: 1 }} exit={{ y: -8, opacity: 0 }} transition={{ duration: 0.18 }} onClick={(e) => e.stopPropagation()}>
              <div className="flex items-center gap-3 border-b border-[var(--line)] px-4">
                <SearchIcon size={18} className="text-[var(--muted)]" />
                <input ref={input} value={q} onChange={(e) => setQ(e.target.value)} placeholder="Search topics, e.g. prepared statement"
                  className="h-14 flex-1 bg-transparent text-[var(--ink)] outline-none placeholder:text-[var(--muted)]"
                  onKeyDown={(e) => {
                    if (e.key === "ArrowDown") { e.preventDefault(); setSel((s) => Math.min(s + 1, results.length - 1)); }
                    if (e.key === "ArrowUp") { e.preventDefault(); setSel((s) => Math.max(s - 1, 0)); }
                    if (e.key === "Enter") go(results[sel]);
                    if (e.key === "Escape") setOpen(false);
                  }} />
                <button onClick={() => setOpen(false)} className="icon-btn" aria-label="Close search"><X size={18} /></button>
              </div>
              <ul className="max-h-[55vh] overflow-y-auto overflow-x-hidden p-2">
                {results.length === 0 && <li className="p-6 text-center text-sm text-[var(--muted)]">Nothing found. Try another word.</li>}
                {results.map((r, i) => (
                  <li key={`${r.slug}-${r.id ?? ""}`}>
                    <button onMouseEnter={() => setSel(i)} onClick={() => go(r)} className={`flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left ${i === sel ? "bg-[var(--soft)]" : ""}`}>
                      <div className="min-w-0 flex-1">
                        <div className="truncate font-medium text-[var(--ink)]">{r.heading ?? r.title}</div>
                        <div className="truncate text-xs text-[var(--muted)]">{r.section}{r.heading ? ` › ${r.title}` : ""}</div>
                      </div>
                      {i === sel && <CornerDownLeft size={15} className="text-[var(--muted)]" />}
                    </button>
                  </li>
                ))}
              </ul>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>, document.body)}
    </>
  );
}

/* ---------- Sidebar ---------- */
function SectionIcon({ name }: { name: string }) {
  const I = (Icons as unknown as Record<string, React.ComponentType<{ size?: number; className?: string }>>)[name] ?? Icons.Circle;
  return <I size={16} className="text-[var(--accent)]" />;
}

function SideNav({ onPick }: { onPick?: () => void }) {
  const path = usePathname();
  const [learned] = useLocalStorage<string[]>(LEARNED_KEY, []);
  return (
    <nav aria-label="Wiki" className="space-y-6 text-sm">
      {sections.map((s: Section) => (
        <div key={s.id}>
          <div className="mb-2 flex items-center gap-2 px-2 text-xs font-semibold uppercase tracking-wider text-[var(--muted)]"><SectionIcon name={s.icon} />{s.title}</div>
          <ul className="space-y-0.5 border-l border-[var(--line)]">
            {s.articles.map((a) => {
              const on = path === `/wiki/${a.slug}`;
              return (
                <li key={a.slug}>
                  <Link href={`/wiki/${a.slug}`} onClick={onPick} aria-current={on ? "page" : undefined}
                    className={`-ml-px flex min-h-9 items-center gap-2 border-l-2 py-1.5 pl-3 pr-2 transition ${on ? "border-[var(--accent)] font-semibold text-[var(--accent)]" : "border-transparent text-[var(--text)] hover:border-[var(--line-strong)] hover:text-[var(--ink)]"}`}>
                    <span className="flex-1">{a.title}</span>
                    {learned.includes(a.slug) && <CheckCircle2 size={14} className="text-emerald-500" aria-label="Learned" />}
                  </Link>
                </li>
              );
            })}
          </ul>
        </div>
      ))}
    </nav>
  );
}


export function MobileNav() {
  const [open, setOpen] = useState(false);
  const mounted = useMounted();
  return (
    <>
      <button onClick={() => setOpen(true)} className="flex min-h-10 items-center gap-2 rounded-xl px-2.5 text-sm font-medium text-[var(--ink)] transition hover:bg-[var(--soft)]" aria-label="Open chapters"><Menu size={18} /><span className="hidden md:inline">Chapters</span></button>
      {mounted && createPortal(<AnimatePresence>
        {open && (
          <motion.div className="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm" initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} onClick={() => setOpen(false)}>
            <motion.div className="h-full w-[85%] max-w-xs overflow-y-auto glass glass-strong rounded-r-3xl p-5" initial={{ x: "-100%" }} animate={{ x: 0 }} exit={{ x: "-100%" }} transition={{ type: "tween", duration: 0.22 }} onClick={(e) => e.stopPropagation()}>
              <div className="mb-6 flex items-center justify-between"><span className="brand text-lg">Chapters</span><button onClick={() => setOpen(false)} className="icon-btn" aria-label="Close chapters"><X size={20} /></button></div>
              <SideNav onPick={() => setOpen(false)} />
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>, document.body)}
    </>
  );
}

/* ---------- On this page ---------- */
export function Toc({ headings }: { headings: Heading[] }) {
  const ids = useMemo(() => headings.map((h) => h.id), [headings]);
  const active = useScrollSpy(ids);
  if (!headings.length) return null;
  return (
    <aside className="sticky top-28 hidden max-h-[calc(100vh-8rem)] w-56 shrink-0 self-start overflow-y-auto glass rounded-2xl p-4 xl:block">
      <div className="mb-3 text-xs font-semibold uppercase tracking-wider text-[var(--muted)]">In this chapter</div>
      <ul className="space-y-1 border-l border-[var(--line)] text-[13px]">
        {headings.map((h) => (
          <li key={h.id}>
            <a href={`#${h.id}`} className={`-ml-px block border-l-2 py-1 transition ${h.depth === 3 ? "pl-6" : "pl-3"} ${active === h.id ? "border-[var(--accent)] font-medium text-[var(--accent)]" : "border-transparent text-[var(--muted)] hover:text-[var(--ink)]"}`}>{h.text}</a>
          </li>
        ))}
      </ul>
    </aside>
  );
}

/* ---------- Reading progress + "I learned this" ---------- */
export function ReadingProgress() {
  const p = useScrollProgress();
  return <div className="fixed left-0 top-0 z-[60] h-[3px] bg-gradient-to-r from-sky-400 to-[var(--accent)]" style={{ width: `${p * 100}%` }} aria-hidden />;
}

export function MarkLearned({ slug }: { slug: string }) {
  const [learned, setLearned] = useLocalStorage<string[]>(LEARNED_KEY, []);
  const done = learned.includes(slug);
  return (
    <button onClick={() => setLearned(done ? learned.filter((s) => s !== slug) : [...learned, slug])}
      className={`inline-flex min-h-11 items-center gap-2 rounded-xl border px-4 font-medium transition ${done ? "border-emerald-500/40 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400" : "border-[var(--line)] bg-[var(--surface)] text-[var(--ink)] hover:border-[var(--accent)]/50"}`}>
      {done ? <CheckCircle2 size={18} /> : <Circle size={18} />}{done ? "Learned. Nice work, Ishika!" : "Mark as learned"}
    </button>
  );
}

/** Progress across the whole wiki, for the home page. */
export function Progress({ total }: { total: number }) {
  const [learned] = useLocalStorage<string[]>(LEARNED_KEY, []);
  const pct = Math.round((learned.length / total) * 100);
  const next = sections.flatMap((s) => s.articles).find((a) => !learned.includes(a.slug));
  return (
    <div className="glass rounded-2xl p-5">
      <div className="flex items-center justify-between text-sm"><span className="font-semibold text-[var(--ink)]">Your progress</span><span className="text-[var(--muted)]">{learned.length} / {total} articles</span></div>
      <div className="mt-3 h-2 overflow-hidden rounded-full bg-[var(--soft)]"><motion.div className="h-full rounded-full bg-gradient-to-r from-sky-400 to-[var(--accent)]" initial={{ width: 0 }} animate={{ width: `${pct}%` }} transition={{ duration: 0.8 }} /></div>
      {next && <Link href={`/wiki/${next.slug}`} className="mt-4 flex items-center gap-2 text-sm font-medium text-[var(--accent)] hover:underline">Continue: {next.title}<ArrowRight size={15} /></Link>}
    </div>
  );
}
