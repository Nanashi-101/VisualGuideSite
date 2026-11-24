// Copies every course file quoted with <Source file="..."> from the parent folder into sources/,
// so the site builds without the rest of the course folder (e.g. on Vercel).
// Run after editing the PHP project or the course files: npm run sync-sources
import fs from "node:fs";
import path from "node:path";

const files = new Set();
for (const f of fs.readdirSync("content")) {
  for (const m of fs.readFileSync(path.join("content", f), "utf8").matchAll(/file="([^"]+)"/g)) files.add(m[1]);
}
for (const rel of files) {
  const from = path.join("..", rel), to = path.join("sources", rel);
  if (!fs.existsSync(from)) throw new Error(`Missing source file: ${from}`);
  fs.mkdirSync(path.dirname(to), { recursive: true });
  fs.copyFileSync(from, to);
}
console.log(`Copied ${files.size} files into sources/`);
