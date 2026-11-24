// The wiki's table of contents. To add an article: create content/<slug>.mdx and list it here.
export type Article = { slug: string; title: string; summary: string; minutes: number; level: "Beginner" | "Intermediate" | "Project" | "Reference" };
export type Section = { id: string; title: string; icon: string; articles: Article[] };

export const sections: Section[] = [
  {
    id: "start", title: "Getting started", icon: "Compass", articles: [
      { slug: "welcome", title: "Welcome & learning path", summary: "How this wiki is organised and the order to read it in.", minutes: 5, level: "Beginner" },
      { slug: "how-the-web-works", title: "How the web works", summary: "Clients, servers, HTTP requests and responses, and where PHP fits in.", minutes: 15, level: "Beginner" },
      { slug: "setup-xampp", title: "Setting up XAMPP & VS Code", summary: "Install the tools, understand htdocs, and run your first PHP page.", minutes: 15, level: "Beginner" },
    ],
  },
  {
    id: "html", title: "HTML & forms", icon: "FileCode2", articles: [
      { slug: "html-basics", title: "HTML fundamentals", summary: "Document structure, elements, attributes and semantic tags.", minutes: 15, level: "Beginner" },
      { slug: "html-forms", title: "HTML forms", summary: "form, input types, labels, name, action and method: GET vs POST.", minutes: 20, level: "Beginner" },
    ],
  },
  {
    id: "php", title: "PHP fundamentals", icon: "Code2", articles: [
      { slug: "php-basics", title: "PHP syntax, variables & types", summary: "Tags, echo, comments, variables, data types and strings.", minutes: 20, level: "Beginner" },
      { slug: "php-control-flow", title: "Operators & decisions", summary: "Comparison, logic, if / elseif / else, switch and match.", minutes: 20, level: "Beginner" },
      { slug: "php-loops", title: "Loops", summary: "while, do…while, for and foreach, with break and continue.", minutes: 20, level: "Beginner" },
      { slug: "php-arrays", title: "Arrays", summary: "Indexed, associative and nested arrays plus the functions you will use daily.", minutes: 20, level: "Beginner" },
      { slug: "php-functions", title: "Functions & includes", summary: "Write reusable functions, understand scope, split code with include/require.", minutes: 20, level: "Intermediate" },
      { slug: "php-forms", title: "Handling form data", summary: "Superglobals, validation, escaping output and the Post/Redirect/Get pattern.", minutes: 25, level: "Intermediate" },
    ],
  },
  {
    id: "db", title: "Databases", icon: "Database", articles: [
      { slug: "databases-sql", title: "Relational databases & SQL", summary: "Tables, keys, relationships and every SQL statement the course uses.", minutes: 30, level: "Intermediate" },
      { slug: "php-mysql", title: "PHP + MySQL", summary: "mysqli and PDO, fetching rows, prepared statements and SQL injection.", minutes: 30, level: "Intermediate" },
    ],
  },
  {
    id: "auth", title: "Login & security", icon: "ShieldCheck", articles: [
      { slug: "sessions-login", title: "Sessions & login systems", summary: "Cookies, $_SESSION, login, logout and protecting pages.", minutes: 25, level: "Intermediate" },
      { slug: "passwords-2fa", title: "Password hashing & 2FA", summary: "password_hash, password_verify and how Google Authenticator codes work.", minutes: 30, level: "Intermediate" },
      { slug: "web-security", title: "Web security essentials", summary: "XSS, SQL injection, CSRF, brute force and a checklist for every project.", minutes: 25, level: "Intermediate" },
    ],
  },
  {
    id: "projects", title: "Projects", icon: "FolderKanban", articles: [
      { slug: "smw-project", title: "SMW: the professor's project", summary: "The Student Management Web app, file by file, with what to learn from it.", minutes: 30, level: "Project" },
      { slug: "fitness-overview", title: "Kraftwerk: architecture", summary: "Features, folder structure, database design and how a request flows.", minutes: 25, level: "Project" },
      { slug: "fitness-walkthrough", title: "Kraftwerk: code walkthrough", summary: "Every file explained line by line: config, auth, 2FA and admin CRUD.", minutes: 45, level: "Project" },
      { slug: "fitness-frontend", title: "Kraftwerk: frontend & design", summary: "Bootstrap, the custom CSS, dark mode and the scroll animations.", minutes: 20, level: "Project" },
      { slug: "build-it-yourself", title: "Build it yourself", summary: "A step-by-step tutorial to rebuild the fitness site from an empty folder.", minutes: 90, level: "Project" },
    ],
  },
  {
    id: "ref", title: "Reference", icon: "BookMarked", articles: [
      { slug: "debugging", title: "Debugging & testing", summary: "Read errors, inspect variables, use phpMyAdmin and test like a pro.", minutes: 20, level: "Reference" },
      { slug: "cheatsheet", title: "PHP & SQL cheat sheet", summary: "The syntax you reach for most, on one page.", minutes: 10, level: "Reference" },
      { slug: "glossary", title: "Glossary", summary: "Every term from the course, explained in one or two sentences.", minutes: 10, level: "Reference" },
    ],
  },
];

export const articles = sections.flatMap((s) => s.articles.map((a) => ({ ...a, section: s.title, sectionId: s.id })));
export const findArticle = (slug: string) => articles.find((a) => a.slug === slug);
