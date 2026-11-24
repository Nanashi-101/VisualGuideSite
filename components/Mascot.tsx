"use client";
import { motion } from "framer-motion";

/** Mochi the cat. Blinks and floats. */
export function Mascot({ size = 120, still = false }: { size?: number; still?: boolean }) {
  const ink = "#1e3a5f";
  const eye = (cx: number) => (
    <motion.ellipse cx={cx} cy={66} rx={5} ry={7} fill={ink} style={{ transformBox: "fill-box", transformOrigin: "center" }}
      animate={{ scaleY: [1, 1, 0.1, 1] }} transition={{ repeat: Infinity, duration: 3.6, times: [0, 0.92, 0.96, 1] }} />
  );
  return (
    <motion.svg viewBox="0 0 120 120" width={size} height={size} role="img" aria-label="Mochi the cat" animate={still ? undefined : { y: [0, -5, 0] }} transition={{ repeat: Infinity, duration: 3, ease: "easeInOut" }} className="shrink-0">
      <path d="M22 48 L28 10 L54 30Z" fill="#dbeeff" stroke={ink} strokeWidth="4" strokeLinejoin="round" />
      <path d="M98 48 L92 10 L66 30Z" fill="#dbeeff" stroke={ink} strokeWidth="4" strokeLinejoin="round" />
      <ellipse cx="60" cy="70" rx="44" ry="38" fill="#fff" stroke={ink} strokeWidth="4" />
      {eye(44)}{eye(76)}
      <circle cx="33" cy="80" r="6" fill="#a9d1ff" opacity=".85" /><circle cx="87" cy="80" r="6" fill="#a9d1ff" opacity=".85" />
      <path d="M57 76 h6 l-3 4z" fill="#3b82d6" stroke={ink} strokeWidth="2" strokeLinejoin="round" />
      <path d="M60 80 Q55 88 49 83 M60 80 Q65 88 71 83" fill="none" stroke={ink} strokeWidth="2.5" strokeLinecap="round" />
      <path d="M12 70 h16 M12 80 l15 -3 M108 70 h-16 M108 80 l-15 -3" stroke={ink} strokeWidth="2.5" strokeLinecap="round" />
      <path d="M84 24 l12 -6 v14z M108 24 l-12 -6 v14z" fill="#3b82d6" stroke={ink} strokeWidth="2.5" strokeLinejoin="round" transform="translate(-6 4)" />
      <circle cx="90" cy="25" r="3.5" fill="#ffffff" stroke={ink} strokeWidth="2" />
    </motion.svg>
  );
}
