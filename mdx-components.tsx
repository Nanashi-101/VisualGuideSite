import type { MDXComponents } from "mdx/types";
import Link from "next/link";
import { Pre, Source } from "@/components/wiki/Code";
import { Note, Tip, Warning, Mochi, Exercise, Solution, FileTree, Goals } from "@/components/wiki/Blocks";
import { ErDiagram, FormDemo, GradeDemo, InjectionDemo, LoginVideo, LoopDemo, RequestVideo, TotpDemo } from "@/components/demos";

// Every component an article can use without importing it.
const components: MDXComponents = {
  pre: Pre,
  a: ({ href = "", ...p }) => (href.startsWith("/") || href.startsWith("#") ? <Link href={href} {...p} /> : <a href={href} target="_blank" rel="noopener" {...p} />),
  table: (p) => <div className="table-wrap"><table {...p} /></div>,
  Source, Note, Tip, Warning, Mochi, Exercise, Solution, FileTree, Goals,
  ErDiagram, FormDemo, GradeDemo, InjectionDemo, LoginVideo, LoopDemo, RequestVideo, TotpDemo,
};

export function useMDXComponents(): MDXComponents {
  return components;
}
