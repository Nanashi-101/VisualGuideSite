"use client";
import { Check, Copy } from "lucide-react";
import { useState } from "react";

export function CopyButton({ text }: { text: string }) {
  const [done, setDone] = useState(false);
  return (
    <button
      type="button"
      onClick={() => navigator.clipboard.writeText(text).then(() => { setDone(true); setTimeout(() => setDone(false), 1500); })}
      className="flex min-h-8 items-center gap-1.5 rounded-md px-2 py-1 text-slate-400 transition hover:bg-white/10 hover:text-white"
      aria-label={done ? "Copied" : "Copy code"}
    >
      {done ? <Check size={14} /> : <Copy size={14} />}<span>{done ? "Copied" : "Copy"}</span>
    </button>
  );
}
