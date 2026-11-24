"use client";
import { motion } from "framer-motion";
import { useEffect, useState } from "react";
import { Flow, Player } from "./ui";

const STACK = [{ icon: "🖥️", name: "Browser" }, { icon: "🪶", name: "Apache" }, { icon: "🐘", name: "PHP" }, { icon: "🗄️", name: "MySQL" }];

/** Week 1: one request through the XAMPP stack. */
export function RequestVideo() {
  return (
    <Player title="One page request, step by step" scenes={[
      { caption: "1. You type http://localhost/smw/index.php. The browser sends an HTTP request to Apache.", visual: <Flow nodes={STACK} from={0} to={1} label="GET /smw/index.php" /> },
      { caption: "2. Apache sees the .php ending and hands the file to the PHP interpreter.", visual: <Flow nodes={STACK} from={1} to={2} label="run index.php" /> },
      { caption: "3. PHP runs mysqli_query(). It asks MySQL for faculties and students.", visual: <Flow nodes={STACK} from={2} to={3} label="SELECT ... FROM faculties" /> },
      { caption: "4. MySQL answers with rows of data.", visual: <Flow nodes={STACK} from={3} to={2} label="rows" /> },
      { caption: "5. PHP mixes the rows into HTML. Only HTML leaves the server, never your PHP code.", visual: <Flow nodes={STACK} from={2} to={1} label="<html>…</html>" /> },
      { caption: "6. Apache sends the page back and the browser draws it.", visual: <Flow nodes={STACK} from={1} to={0} label="200 OK" note="Page rendered ✔" /> },
    ]} />
  );
}

/** Week 1: what $_POST / $_GET contain. */
export function FormDemo() {
  const [name, setName] = useState("Anna");
  const [email, setEmail] = useState("anna@example.de");
  const [method, setMethod] = useState<"POST" | "GET">("POST");
  const q = `name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}`;
  return (
    <div className="card my-6 grid gap-4 md:grid-cols-2">
      <div>
        <div className="mb-2 flex gap-2 text-xs">
          {(["POST", "GET"] as const).map((m) => (
            <button key={m} onClick={() => setMethod(m)} className={`chip ${method === m ? "!bg-[var(--accent)]" : ""}`}>method="{m.toLowerCase()}"</button>
          ))}
        </div>
        <label className="block text-sm">name<input value={name} onChange={(e) => setName(e.target.value)} className="mt-1 w-full rounded-xl border border-[var(--line)] bg-[var(--surface)] p-2" /></label>
        <label className="mt-2 block text-sm">email<input value={email} onChange={(e) => setEmail(e.target.value)} className="mt-1 w-full rounded-xl border border-[var(--line)] bg-[var(--surface)] p-2" /></label>
      </div>
      <div className="font-mono text-xs">
        <div className="text-[var(--muted)]">URL the browser requests:</div>
        <div className="mb-3 break-all rounded bg-[var(--code)] p-2 text-amber-300">/register.php{method === "GET" ? `?${q}` : ""}</div>
        <div className="text-[var(--muted)]">PHP receives:</div>
        <motion.pre key={q + method} initial={{ opacity: 0.3 }} animate={{ opacity: 1 }} className="rounded bg-[var(--code)] p-2 text-emerald-300">
{`$_${method} = [\n  "name"  => "${name}",\n  "email" => "${email}"\n];`}
        </motion.pre>
        {method === "GET" && <div className="mt-2 text-red-600">⚠ Data is visible in the URL. Never send passwords with GET.</div>}
      </div>
    </div>
  );
}

/** Week 2: if / elseif / else with a live grade. */
export function GradeDemo() {
  const [g, setG] = useState(72);
  const rungs = [[90, "A (Excellent)"], [80, "B (Very Good)"], [70, "C (Good)"], [50, "D (Satisfactory)"], [0, "F (Fail)"]] as const;
  const hit = rungs.findIndex(([min]) => g >= min);
  return (
    <div className="card my-6">
      <label className="text-sm">grade = <b>{g}</b><input type="range" min={0} max={100} value={g} onChange={(e) => setG(+e.target.value)} className="ml-3 w-64 align-middle" /></label>
      <div className="mt-3 space-y-1 font-mono text-sm">
        {rungs.map(([min, label], k) => (
          <motion.div key={label} animate={{ opacity: k <= hit ? 1 : 0.3, backgroundColor: k === hit ? "rgba(16,185,129,.18)" : "rgba(100,116,139,.06)" }} className="rounded px-3 py-1">
            {k === 0 ? "if" : k < 4 ? "elseif" : "else"} {k < 4 ? `($g >= ${min})` : ""} → {label}
            {k === hit && <span className="ml-2 text-emerald-600">✔ runs, the rest is skipped</span>}
            {k < hit && <span className="ml-2 text-[var(--muted)]">✗ false</span>}
          </motion.div>
        ))}
      </div>
    </div>
  );
}

/** Week 2: a for loop running. */
export function LoopDemo() {
  const [n, setN] = useState(0);
  useEffect(() => { const t = setInterval(() => setN((v) => (v >= 5 ? 0 : v + 1)), 900); return () => clearInterval(t); }, []);
  return (
    <div className="card my-6">
      <div className="font-mono text-sm">for ($i = 1; $i &lt;= 5; $i++) {"{"} echo $i; {"}"}</div>
      <div className="mt-3 flex items-center gap-2">
        {[1, 2, 3, 4, 5].map((i) => (
          <motion.div key={i} animate={{ scale: i === n ? 1.25 : 1, opacity: i <= n ? 1 : 0.25 }} className="flex h-10 w-10 items-center justify-center rounded-lg bg-[var(--accent)] font-bold">{i}</motion.div>
        ))}
        <span className="ml-3 font-mono text-xs text-[var(--muted)]">{n === 0 ? "$i = 1 (start)" : n === 5 ? "$i = 6 → condition false → stop" : `$i = ${n}, echo, then $i++`}</span>
      </div>
    </div>
  );
}

/** Week 2/3: why prepared statements matter. */
export function InjectionDemo() {
  const inputs = ["alice", "' OR '1'='1"];
  const [v, setV] = useState(inputs[1]);
  const bad = v.includes("'");
  return (
    <div className="card my-6">
      <div className="mb-3 flex flex-wrap gap-2 text-sm">
        <span className="text-[var(--muted)]">Attacker types as username:</span>
        {inputs.map((x) => <button key={x} onClick={() => setV(x)} className={`chip font-mono ${v === x ? "!bg-[var(--accent)]" : ""}`}>{x}</button>)}
      </div>
      <div className="grid gap-3 md:grid-cols-2 font-mono text-xs">
        <div className="rounded-lg border border-red-400 p-3">
          <b className="text-red-600">String concatenation (professor's Login/login.php)</b>
          <pre className="mt-2 whitespace-pre-wrap text-[var(--ink)]">{`SELECT uid FROM users\nWHERE username='${v}'\nAND password='anything'`}</pre>
          <div className={bad ? "text-red-600" : "text-emerald-600"}>{bad ? "💥 WHERE is always true → logged in without a password" : "✔ normal lookup, wrong password fails"}</div>
        </div>
        <div className="rounded-lg border border-emerald-500 p-3">
          <b className="text-emerald-600">Prepared statement (fitness site)</b>
          <pre className="mt-2 whitespace-pre-wrap text-[var(--ink)]">{`SELECT ... WHERE email = ?\nbound value: "${v}"`}</pre>
          <div className="text-emerald-600">✔ the value is data, never SQL. No match → login fails.</div>
        </div>
      </div>
    </div>
  );
}

// RFC 6238 test secret, so you can compare with any TOTP tool. Real accounts use random_bytes().
async function totp(counter: number) {
  const key = await crypto.subtle.importKey("raw", new TextEncoder().encode("12345678901234567890"), { name: "HMAC", hash: "SHA-1" }, false, ["sign"]);
  const buf = new ArrayBuffer(8);
  new DataView(buf).setUint32(4, counter);
  const h = new Uint8Array(await crypto.subtle.sign("HMAC", key, buf));
  const o = h[19] & 15;
  const n = ((h[o] & 0x7f) << 24) | (h[o + 1] << 16) | (h[o + 2] << 8) | h[o + 3];
  return String(n % 1_000_000).padStart(6, "0");
}

/** Week 3: a real, live TOTP code, same algorithm as Google Authenticator and totp.php. */
export function TotpDemo() {
  const [now, setNow] = useState(() => Math.floor(Date.now() / 1000));
  const [code, setCode] = useState("······");
  useEffect(() => { const t = setInterval(() => setNow(Math.floor(Date.now() / 1000)), 250); return () => clearInterval(t); }, []);
  const counter = Math.floor(now / 30);
  useEffect(() => { totp(counter).then(setCode); }, [counter]);
  const left = 30 - (now % 30);
  return (
    <div className="card my-6 flex flex-wrap items-center gap-6">
      <svg width="110" height="110" viewBox="0 0 110 110" aria-label={`${left} seconds left`}>
        <circle cx="55" cy="55" r="48" fill="none" stroke="#d4e6fb" strokeWidth="8" />
        <motion.circle cx="55" cy="55" r="48" fill="none" stroke="#3b82d6" strokeWidth="8" strokeLinecap="round" strokeDasharray={301.6}
          animate={{ strokeDashoffset: 301.6 * (1 - left / 30) }} transition={{ duration: 0.25 }} transform="rotate(-90 55 55)" />
        <text x="55" y="62" textAnchor="middle" fill="currentColor" fontSize="22" fontWeight="700">{left}s</text>
      </svg>
      <div>
        <motion.div key={code} initial={{ y: -8, opacity: 0 }} animate={{ y: 0, opacity: 1 }} className="font-mono text-4xl font-extrabold tracking-[.25em]">{code}</motion.div>
        <p className="!mt-1 text-xs">counter = floor(unix time / 30) = <b>{counter}</b>. Code = HMAC-SHA1(secret, counter), cut down to 6 digits. The server repeats the same maths and compares.</p>
      </div>
    </div>
  );
}

const LOGIN = [{ icon: "🧑", name: "Member" }, { icon: "🪶", name: "login.php" }, { icon: "🗄️", name: "MySQL" }, { icon: "📱", name: "Authenticator" }];

/** Week 3 style login, extended with the fitness site's mandatory 2FA at login and logout. */
export function LoginVideo() {
  return (
    <Player title="Login and logout with Google Authenticator" scenes={[
      { caption: "1. The member sends email and password.", visual: <Flow nodes={LOGIN} from={0} to={1} label="email + password" /> },
      { caption: "2. PHP looks the user up with a prepared statement and checks the password with password_verify().", visual: <Flow nodes={LOGIN} from={1} to={2} label="SELECT … WHERE email=?" note="stored: $2y$10$… (hash, never plain text)" /> },
      { caption: "3. Password is right, but the session only holds 'pre' (half logged in). No admin page opens yet.", visual: <Flow nodes={LOGIN} from={2} to={1} label="hash matches" note="$_SESSION['pre'] = id, email, role" /> },
      { caption: "4. The member opens the app and reads the current 6-digit code.", visual: <Flow nodes={LOGIN} from={3} to={0} label="482 913" /> },
      { caption: "5. The member types the code. PHP recomputes it from the stored secret.", visual: <Flow nodes={LOGIN} from={0} to={1} label="code 482913" note="verify_totp(secret, code) ✔" /> },
      { caption: "6. Now the session becomes 'user' with a new session id. The dashboard opens.", visual: <Flow nodes={LOGIN} from={1} to={0} label="302 → dashboard.php" note="session_regenerate_id(true)" /> },
      { caption: "7. Logout asks for a fresh code too. A wrong code keeps you logged in; a right one destroys the session.", visual: <Flow nodes={LOGIN} from={0} to={1} label="logout + code" note="session_destroy() → login.php" /> },
    ]} />
  );
}

/** Database picture for the fitness site. */
export function ErDiagram() {
  const T = ({ x, y, title, cols }: { x: number; y: number; title: string; cols: string[] }) => (
    <g transform={`translate(${x},${y})`}>
      <rect width="190" height={34 + cols.length * 20} rx="8" fill="#fff" stroke="#1e3a5f" />
      <rect width="190" height="26" rx="8" fill="#3b82d6" />
      <text x="10" y="18" fill="#fff" fontWeight="700" fontSize="13">{title}</text>
      {cols.map((c, i) => <text key={c} x="10" y={46 + i * 20} fill="#1e3a5f" fontSize="12" fontFamily="monospace">{c}</text>)}
    </g>
  );
  return (
    <svg viewBox="0 0 760 330" className="my-6 w-full rounded-2xl border border-[var(--line)] bg-[var(--surface)]" role="img" aria-label="Database diagram">
      <T x={20} y={20} title="users" cols={["id  PK", "email  UNIQUE", "password_hash", "role  member|admin", "created_at"]} />
      <T x={20} y={190} title="user_2fa" cols={["user_id  PK, FK", "secret_base32", "enabled"]} />
      <T x={290} y={20} title="plans" cols={["id  PK", "name", "price_eur"]} />
      <T x={290} y={150} title="members" cols={["id  PK", "first_name, last_name", "birthdate, address", "notes", "plan_id  FK"]} />
      <T x={550} y={20} title="contacts" cols={["id  PK", "subject", "message", "email", "created_at"]} />
      <path d="M115 150 V190" stroke="#6b8bb0" strokeWidth="2" /><text x="125" y="176" fill="#6b8bb0" fontSize="11">1 : 1</text>
      <path d="M385 88 V150" stroke="#6b8bb0" strokeWidth="2" /><text x="395" y="124" fill="#6b8bb0" fontSize="11">1 : many</text>
    </svg>
  );
}
