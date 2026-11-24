"use client";
// Small effects that make reading feel like a journey: the home preloader and scroll reveals in articles.
import { useEffect, useState } from "react";
import { Mascot } from "../Mascot";

/**
 * Mochi greets her while the home page finishes loading. It is rendered on the server so it covers the page
 * from the first paint; CSS hides it after 3 s even if JavaScript never runs, so it can never get stuck.
 */
export function Preloader() {
  const [gone, setGone] = useState(false);
  useEffect(() => {
    const still = matchMedia("(prefers-reduced-motion: reduce)").matches;
    const start = performance.now();
    const hide = () => setTimeout(() => setGone(true), Math.max(0, (still ? 0 : 1100) - (performance.now() - start)));
    if (document.readyState === "complete") hide(); else addEventListener("load", hide, { once: true });
  }, []);
  return (
    <div className={`preloader ${gone ? "preloader-out" : ""}`} aria-hidden>
      <div className="flex flex-col items-center gap-4">
        <Mascot size={96} />
        <div className="brand text-lg text-[var(--ink)]">Planting your garden</div>
        <div className="flex gap-1.5">{[0, 1, 2].map((i) => <span key={i} className="preloader-dot" style={{ animationDelay: `${i * 0.15}s` }} />)}</div>
      </div>
    </div>
  );
}

/** Fades the article's sections in as they scroll into view. Without JavaScript everything is simply visible. */
export function ProseReveal() {
  useEffect(() => {
    if (matchMedia("(prefers-reduced-motion: reduce)").matches) return;
    const els = [...document.querySelectorAll<HTMLElement>(".prose > *")];
    const io = new IntersectionObserver((entries) => entries.forEach((e) => {
      if (e.isIntersecting) { e.target.classList.add("in"); io.unobserve(e.target); }
    }), { rootMargin: "0px 0px -8% 0px" });
    els.forEach((el) => { if (el.getBoundingClientRect().top > innerHeight) { el.classList.add("rv"); io.observe(el); } });
    return () => io.disconnect();
  }, []);
  return null;
}
