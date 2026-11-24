"use client";
// Small React hooks shared by the wiki UI.
import { useCallback, useEffect, useState } from "react";

/** State that survives reloads. Falls back to memory when storage is blocked (private mode). */
export function useLocalStorage<T>(key: string, initial: T) {
  const [value, setValue] = useState<T>(initial);
  useEffect(() => {
    try { const v = localStorage.getItem(key); if (v !== null) setValue(JSON.parse(v)); } catch { /* storage blocked */ }
    const sync = (e: StorageEvent) => { if (e.key === key && e.newValue) setValue(JSON.parse(e.newValue)); };
    addEventListener("storage", sync);
    return () => removeEventListener("storage", sync);
  }, [key]);
  const set = useCallback((v: T) => {
    setValue(v);
    try { localStorage.setItem(key, JSON.stringify(v)); } catch { /* storage blocked */ }
  }, [key]);
  return [value, set] as const;
}

/** Id of the heading currently at the top of the screen (for the "On this page" list). */
export function useScrollSpy(ids: string[]) {
  const [active, setActive] = useState(ids[0] ?? "");
  useEffect(() => {
    // The last heading that has passed under the header wins; at the very bottom, the last heading (short final sections never reach the top).
    const f = () => {
      let cur = ids[0];
      for (const id of ids) { const el = document.getElementById(id); if (el && el.getBoundingClientRect().top <= 120) cur = id; }
      if (innerHeight + scrollY >= document.documentElement.scrollHeight - 4) cur = ids[ids.length - 1];
      setActive(cur ?? "");
    };
    f();
    addEventListener("scroll", f, { passive: true });
    return () => removeEventListener("scroll", f);
  }, [ids]);
  return active;
}

/** Run `fn` when the user presses Ctrl/Cmd + key. */
export function useHotkey(key: string, fn: () => void) {
  useEffect(() => {
    const h = (e: KeyboardEvent) => { if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === key) { e.preventDefault(); fn(); } };
    addEventListener("keydown", h);
    return () => removeEventListener("keydown", h);
  }, [key, fn]);
}

/** 0–1: how far the page has been scrolled. */
export function useScrollProgress() {
  const [p, setP] = useState(0);
  useEffect(() => {
    const f = () => { const h = document.documentElement.scrollHeight - innerHeight; setP(h > 0 ? scrollY / h : 0); };
    f(); addEventListener("scroll", f, { passive: true }); addEventListener("resize", f);
    return () => { removeEventListener("scroll", f); removeEventListener("resize", f); };
  }, []);
  return p;
}

export const LEARNED_KEY = "ishika-learned";
