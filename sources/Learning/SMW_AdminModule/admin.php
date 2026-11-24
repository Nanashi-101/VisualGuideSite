<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Management Webapplication (SMW)</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
/* ---------- Base layout hygiene ---------- */
* { box-sizing: border-box; }
html, body { margin: 0; overflow-x: hidden; }
img, svg, video { max-width: 100%; height: auto; }

/* ---------- Theme variables ---------- */
:root{
  /* Light theme */
  --bg: #f4f4f4;
  --text: #1f2937;              /* charcoal */
  --surface: #ffffff;
  --muted: #eef2f7;
  --primary: #2c3e50;           /* SRH orange */
  --primary-contrast: #ffffff;  /* text on primary */
  --icon: #2c3e50;              /* icon color in light */
  --link: #ffffff;              /* links on primary bars */
  --link-hover: #ffd7c8;        /* softer hover on primary */
  --border: #d6d6d6;
}



/* When .dark-mode is on body, swap variables */
.dark-mode{
  --bg: #0f172a;                /* slate-900 */
  --text: #e5e7eb;              /* slate-200 */
  --surface: #111827;           /* slate-800 */
  --muted: #0b1220;             /* darker alt row */
  --primary: #2c3e50;           /* keep brand color */
  --primary-contrast: #ffffff;
  --icon: #e5e7eb;              /* icons stay bright */
  --link: #e5e7eb;
  --link-hover: #ffd7c8;
  --border: #2b3547;
}

/* ---------- Global ---------- */
body{
  font-family: Arial, Helvetica, sans-serif;
  background: var(--bg);
  color: var(--text);
  transition: background .25s ease, color .25s ease;
}

/* ---------- Header ---------- */
header{
  background: var(--primary);
  color: var(--primary-contrast);
  text-align: center;
  padding: 2rem 1rem;
  position: relative;
}
header .dark-toggle,
header .logout-button{
  position: absolute;
  top: 1rem;
  background: transparent;
  border: none;
  font-size: 1.5rem;
  color: var(--primary-contrast);
  cursor: pointer;
}
header .dark-toggle{ right: 3.5rem; }
header .logout-button{ right: 1rem; }
header .dark-toggle:hover,
header .logout-button:hover{ opacity: .85; }

/* ---------- Nav ---------- */
nav{
  background: var(--primary);
  color: var(--primary-contrast);
  width: 100%;
  display: flex;
  justify-content: center;
  gap: 2rem;
  padding: 1rem;
  flex-wrap: wrap;
}
nav a{
  color: var(--link);
  text-decoration: none;
  font-weight: bold;
  display: flex;
  align-items: center;
  gap: .5rem;
  cursor: pointer;
}
nav a:hover{ color: var(--link-hover); }

/* Icons inherit current text color -> always visible */
i{ color: currentColor; }

/* ---------- Sections ---------- */
section{ padding: 2rem; display: none; }
#home-management{ display: block; }

.management-section{
  flex: 1;
  min-width: 250px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  text-align: center;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 1.25rem;
}
.management-section a{ color: var(--text); } /* icon/link adopts readable color */
.management-section a:hover{ color: var(--primary); }

/* Simple grid container if you add more cards later */
.management-sections{
  display: flex;
  gap: 2rem;
  justify-content: space-around;
  flex-wrap: wrap;
}

/* ---------- Tables ---------- */
#contact-table{
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
  background: var(--surface);
  color: var(--text);
  border: 1px solid var(--border);
}
#contact-table th,
#contact-table td{
  border: 1px solid var(--border);
  padding: 10px;
  text-align: left;
}
#contact-table th{
  background: var(--primary);
  color: var(--primary-contrast);
}
#contact-table tr:nth-child(even){
  background: var(--muted);
}

/* ---------- Iframe ---------- */
/* Give iframes a surface background so inside pages don't look 'cut out' in dark */
iframe{
  width: 100%;
  height: 600px;
  border: none;
  background: var(--surface);
}

/* ---------- Footer ---------- */
footer{
  background: var(--primary);
  color: var(--primary-contrast);
  text-align: center;
  padding: 1rem;
}
footer a{ color: var(--link); }
footer a:hover{ color: var(--link-hover); }
  </style>
</head>

<body>
  <header>
    <h1>Student Management Webapplication (SMW)</h1>

    <button class="dark-toggle" onclick="toggleDarkMode()" title="Change modality (Dark Mode)">
      <i class="fas fa-moon"></i>
    </button>
    <button class="logout-button" onclick="location.href='logout.php'" title="Logout">
      <i class="fas fa-right-from-bracket"></i>
    </button>
  </header>

  <nav>
    <a id="nav-home"><i class="fas fa-home"></i> Home</a>
    <a id="nav-users"><i class="fas fa-users"></i> Users</a>
    <a id="nav-contact"><i class="fas fa-envelope"></i> Contact</a>
  </nav>

  <!-- Section: Home -->
  <section id="home-management">
    <div class="management-sections">
      <div class="management-section">
        <a href="http://localhost/smw/Admin/Faculties/admin.php" title="Manage Faculties">
          <i class="fas fa-university" style="font-size:3rem;"></i>
        </a>
        <h3>Manage Faculties</h3>
      </div>

      <div class="management-section">
        <a href="Students/adminn.php" title="Manage Students">
          <i class="fas fa-user-graduate" style="font-size:3rem;"></i>
        </a>
        <h3>Manage Students</h3>
      </div>
    </div>
  </section>

  <!-- Section: Users -->
  <section id="users-section">
    <iframe src="Users/admin.php" title="Users"></iframe>
  </section>

  <!-- Section: Contacts -->
  <section id="contact-section">
    <iframe src="delete__contact.php" title="Contacts"></iframe>
  </section>

  <footer>
    Copyright &copy; LNH <br>
    References:
    <a href="https://www.srh-university.de/en/">SRH</a>
  </footer>

  <script>
    // Persist dark-mode preference across refresh (optional but handy)
    const THEME_KEY = 'smw-theme-dark';
    (function initTheme(){
      const isDark = localStorage.getItem(THEME_KEY) === '1';
      if(isDark) document.body.classList.add('dark-mode');
    })();

    function toggleDarkMode() {
      document.body.classList.toggle('dark-mode');
      const isDark = document.body.classList.contains('dark-mode') ? '1' : '0';
      localStorage.setItem(THEME_KEY, isDark);
    }

    function showOnly(sectionId) {
      const sections = ['home-management', 'users-section', 'contact-section'];
      sections.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = (id === sectionId) ? 'block' : 'none';
      });
    }

    document.getElementById('nav-home').addEventListener('click', function (e) {
      e.preventDefault(); showOnly('home-management');
    });
    document.getElementById('nav-users').addEventListener('click', function (e) {
      e.preventDefault(); showOnly('users-section');
    });
    document.getElementById('nav-contact').addEventListener('click', function (e) {
      e.preventDefault(); showOnly('contact-section');
    });
  </script>
</body>
</html>
