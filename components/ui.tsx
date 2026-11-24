"use client";
import { AnimatePresence, motion } from "framer-motion";
import { ReactNode, useEffect, useState } from "react";

/** Fades a block in when it scrolls into view. */
export function Reveal({ children, delay = 0 }: { children: ReactNode; delay?: number }) {
  return (
    <motion.div initial={{ opacity: 0, y: 24 }} whileInView={{ opacity: 1, y: 0 }} viewport={{ once: true, margin: "-60px" }} transition={{ duration: 0.5, delay }}>
      {children}
    </motion.div>
  );
}

/** Code snippet with a title and a copy button. */
export function Code({ code, title, lang = "php" }: { code: string; title?: string; lang?: string }) {
  const [copied, setCopied] = useState(false);
  return (
    <div className="my-4 overflow-hidden rounded-xl border border-[var(--line)] bg-[var(--code)]">
      <div className="flex items-center justify-between border-b border-[var(--line)] px-3 py-1.5 text-xs text-slate-400">
        <span>{title ?? lang}</span>
        <button
          onClick={() => navigator.clipboard.writeText(code).then(() => { setCopied(true); setTimeout(() => setCopied(false), 1200); })}
          className="rounded px-2 py-0.5 hover:bg-[#2a4a7a]"
        >{copied ? "Copied ✓" : "Copy"}</button>
      </div>
      <pre className="overflow-x-auto p-4 text-[13px] leading-relaxed text-slate-200"><code>{code}</code></pre>
    </div>
  );
}

export type Scene = { caption: string; visual: ReactNode; ms?: number };

/** A small "video player": plays scenes one after another with a caption, progress bar and controls. */
export function Player({ title, scenes }: { title: string; scenes: Scene[] }) {
  const [i, setI] = useState(0);
  const [playing, setPlaying] = useState(false);
  useEffect(() => {
    if (!playing) return;
    const t = setTimeout(() => (i < scenes.length - 1 ? setI(i + 1) : setPlaying(false)), scenes[i].ms ?? 3200);
    return () => clearTimeout(t);
  }, [playing, i, scenes]);
  const toggle = () => { if (!playing && i === scenes.length - 1) setI(0); setPlaying(!playing); };
  return (
    <div className="my-6 overflow-hidden rounded-2xl border border-[var(--line)] bg-[var(--surface)] shadow-sm">
      <div className="flex items-center gap-2 border-b border-[var(--line)] px-4 py-2 text-sm">
        <span className="h-3 w-3 rounded-full bg-red-500" /><span className="h-3 w-3 rounded-full bg-yellow-400" /><span className="h-3 w-3 rounded-full bg-green-500" />
        <span className="ml-2 text-[var(--muted)]">▶ {title}</span>
      </div>
      <div className="relative flex min-h-[230px] items-center justify-center p-5">
        <AnimatePresence mode="wait">
          <motion.div key={i} className="w-full" initial={{ opacity: 0, scale: 0.97 }} animate={{ opacity: 1, scale: 1 }} exit={{ opacity: 0 }} transition={{ duration: 0.3 }}>
            {scenes[i].visual}
          </motion.div>
        </AnimatePresence>
      </div>
      <div className="border-t border-[var(--line)] px-4 py-3">
        <p className="min-h-[3rem] text-sm text-[var(--ink)]">{scenes[i].caption}</p>
        <div className="mt-2 flex items-center gap-3">
          <button onClick={() => setI(Math.max(0, i - 1))} aria-label="Previous scene" className="text-lg">⏮</button>
          <button onClick={toggle} aria-label={playing ? "Pause" : "Play"} className="rounded-full bg-[var(--btn)] px-4 py-1.5 text-sm font-semibold text-white">{playing ? "⏸ Pause" : "▶ Play"}</button>
          <button onClick={() => setI(Math.min(scenes.length - 1, i + 1))} aria-label="Next scene" className="text-lg">⏭</button>
          <div className="flex flex-1 gap-1">
            {scenes.map((_, k) => (
              <button key={k} aria-label={`Scene ${k + 1}`} onClick={() => setI(k)} className={`h-1.5 flex-1 rounded ${k <= i ? "bg-[var(--accent)]" : "bg-[#d4e6fb]"}`} />
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

/** Boxes in a row. A packet travels from node `from` to node `to`. Used inside Player scenes. */
export function Flow({ nodes, from, to, label, note }: { nodes: { icon: string; name: string }[]; from: number; to: number; label: string; note?: string }) {
  const pos = (k: number) => `${(k + 0.5) * (100 / nodes.length)}%`;
  return (
    <div className="relative">
      <div className="flex">
        {nodes.map((n, k) => (
          <div key={n.name} className="flex-1 text-center">
            <motion.div animate={{ scale: k === from || k === to ? 1.12 : 1, borderColor: k === to ? "#3b82d6" : "#1e3a5f" }}
              className="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border-2 bg-[var(--soft)] text-3xl">{n.icon}</motion.div>
            <div className="mt-1 text-xs text-[var(--muted)]">{n.name}</div>
          </div>
        ))}
      </div>
      <motion.div key={`${from}-${to}-${label}`} initial={{ left: pos(from) }} animate={{ left: pos(to) }} transition={{ duration: 1.2, ease: "easeInOut" }}
        className="absolute top-[100px] -translate-x-1/2 whitespace-nowrap rounded-full bg-[var(--btn)] px-3 py-1 text-xs font-semibold text-white shadow-md">{label}</motion.div>
      {note && <div className="mt-20 text-center font-mono text-xs text-emerald-700">{note}</div>}
      {!note && <div className="mt-20" />}
    </div>
  );
}
