import fs from "node:fs";
import path from "node:path";

// Real source files are shown in the guide, so the snippets can never drift from the code.
const ROOT = path.join(process.cwd(), "..");
export function src(rel: string, from = 1, to = Infinity) {
  const lines = fs.readFileSync(path.join(ROOT, rel), "utf8").replace(/\r/g, "").split("\n");
  return lines.slice(from - 1, to).join("\n").trimEnd();
}
