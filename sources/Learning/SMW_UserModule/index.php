<?php
// database connection
$host     = "localhost";
$user     = "root";
$password = "";
$database = "smw"; // change according to your database name

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    die("Error connection: " . mysqli_connect_error());
}

// fetch Faculties with their corresponding Students
$sql = "
    SELECT 
        f.ID_Faculty, 
        f.Faculty, 
        s.SName, 
        s.Surname, 
        s.Description, 
        s.image 
    FROM Faculties f
    LEFT JOIN Students s 
        ON f.ID_Faculty = s.ID_Faculty
    ORDER BY f.Faculty, s.SName
";
$result = mysqli_query($conn, $sql);

// group by faculty
$faculty_students = [];
while ($row = mysqli_fetch_assoc($result)) {
    $faculty = $row['Faculty'];
    if (!isset($faculty_students[$faculty])) {
        $faculty_students[$faculty] = [];
    }
    $faculty_students[$faculty][] = $row;
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Management Webapplication (SMW)</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <style>
    :root{
      --primary-color:#2c3e50;
      --secondary-color:#CE5327;
      --bg-light:#f4f4f4;
      --bg-dark:#2c3e50;
      --text-light:#ffffff;
      --text-dark:#2c3e50;
    }
    *{box-sizing:border-box}
    body{margin:0;font-family:Arial,Helvetica,sans-serif;background:var(--bg-light);color:var(--text-dark)}
    .dark-mode{background:var(--bg-dark);color:var(--text-light)}

    /* ===== Header ===== */
    .wms-header{
      background:#CE5327;color:#fff;text-align:center;padding:80px 20px 60px;position:relative;overflow:hidden
    }
    .wms-header::before{
      content:"";position:absolute;top:-40%;left:-20%;width:140%;height:140%;
      background:radial-gradient(circle, rgba(255,255,255,.25) 0%, transparent 70%);
      opacity:.18;transform:rotate(8deg)
    }
    .wms-header-inner{max-width:900px;margin:auto;position:relative;z-index:2;display:flex;flex-direction:column;align-items:center;gap:18px}
    .wms-title{margin:0;font-size:2.2rem;font-weight:800;letter-spacing:.5px}
    .wms-subtitle{margin:0;font-size:1.05rem;color:#fdeee5;max-width:600px}
    .dark-mode .wms-header{background:#a33f1f}

    /* ===== NAV (sticky) ===== */
    .wms-nav{position:sticky;top:0;z-index:1000;background:linear-gradient(90deg,var(--primary-color),#31465c);color:#fff;border-bottom:1px solid rgba(255,255,255,.08);box-shadow:0 6px 18px rgba(0,0,0,.12)}
    .dark-mode .wms-nav{background:linear-gradient(90deg,#223140,var(--primary-color));border-bottom-color:rgba(255,255,255,.12)}
    .nav-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 16px}
    .nav-brand{display:flex;align-items:center;gap:10px;color:#fff;text-decoration:none;font-weight:800}
    .nav-menu{display:flex;align-items:center;gap:14px}
    .nav-link{position:relative;color:#fff;text-decoration:none;font-weight:700;padding:10px 12px;border-radius:10px}
    .nav-link::after{content:"";position:absolute;left:12px;right:12px;bottom:6px;height:2px;background:var(--secondary-color);transform:scaleX(0);transform-origin:left;transition:transform .18s}
    .nav-link:hover::after,.nav-link.is-active::after{transform:scaleX(1)}
    .wms-actions{display:flex;gap:10px}
    .wms-icon-btn{display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;border:1px solid rgba(255,255,255,.25);background:rgba(255,255,255,.2);color:#fff;cursor:pointer;transition:.2s}
    .wms-icon-btn:hover{background:rgba(255,255,255,.3);transform:scale(1.06)}
    .wms-logout{background:#fff;color:#CE5327;border-color:rgba(255,255,255,.3)}
    .wms-logout:hover{background:#f8f1ef}
    .nav-toggle{display:none;position:relative;width:40px;height:40px;border:0;background:transparent;cursor:pointer}
    .nav-toggle span{position:absolute;left:10px;right:10px;height:2px;background:#fff;border-radius:2px;transition:.2s}
    .nav-toggle span:nth-child(1){top:12px}
    .nav-toggle span:nth-child(2){top:19px}
    .nav-toggle span:nth-child(3){top:26px}
    .nav-toggle.open span:nth-child(1){top:19px;transform:rotate(45deg)}
    .nav-toggle.open span:nth-child(2){opacity:0}
    .nav-toggle.open span:nth-child(3){top:19px;transform:rotate(-45deg)}

    @media(max-width:900px){
      .nav-toggle{display:block}
      .nav-menu{
        position:absolute;inset:56px 10px auto 10px;display:grid;gap:8px;padding:10px;
        background:rgba(0,0,0,.25);backdrop-filter:blur(10px);
        border:1px solid rgba(255,255,255,.12);border-radius:14px;
        transform:translateY(-12px);opacity:0;pointer-events:none;transition:.18s
      }
      .dark-mode .nav-menu{background:rgba(0,0,0,.35)}
      .nav-menu.open{transform:translateY(0);opacity:1;pointer-events:auto}
      .nav-link{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12)}
      .wms-actions{gap:6px}
    }

    section{padding:2rem}

    /* ===== Students (cards) ===== */
    #Students .wms-faculty{margin:0 0 28px}
    #Students .wms-faculty-head{display:flex;justify-content:space-between;align-items:center;gap:12px;margin:0 2px 14px}
    #Students .wms-faculty-title{display:flex;align-items:center;gap:10px;margin:0;font-size:1.1rem;font-weight:800}
    #Students .wms-dot{width:10px;height:10px;border-radius:999px;background:#2c3e50;box-shadow:0 0 0 5px rgba(44,62,80,.18)}
    #Students .wms-count{color:#64748b;font-weight:700;font-size:.95rem}
    #Students .wms-grid{display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr))}
    #Students .wms-card{position:relative;overflow:hidden;border-radius:16px;background:#fff;border:1px solid #eef2f7;box-shadow:0 10px 28px rgba(2,6,23,.08);transition:.15s}
    #Students .wms-card:hover{transform:translateY(-3px);box-shadow:0 16px 40px rgba(2,6,23,.14)}
    #Students .wms-cover{height:84px;background: radial-gradient(600px 240px at 80% -20%, rgba(44,62,80,.25), transparent 60%), linear-gradient(135deg,#2c3e50 0%,#3e5872 50%,#2c3e50 100%)}
    #Students .wms-avatar{position:absolute;left:16px;top:52px;width:72px;height:72px;border-radius:999px;object-fit:cover;border:3px solid #fff;box-shadow:0 6px 18px rgba(2,6,23,.25);background:#f3f4f6}
    #Students .wms-body{padding:52px 16px 16px;min-height:120px}
    #Students .wms-name{margin:0 0 6px;font-size:1rem;font-weight:900;color:#0f172a}
    #Students .wms-desc{margin:0;font-size:.92rem;color:#4b5563;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
    #Students .wms-badge{position:absolute;right:12px;top:12px;font-size:.78rem;padding:6px 10px;border-radius:999px;background:rgba(44,62,80,.15);color:#2c3e50;border:1px solid rgba(44,62,80,.25);font-weight:700;display:inline-flex;align-items:center;gap:6px}
    .dark-mode #Students .wms-card{background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.10);box-shadow:0 12px 34px rgba(0,0,0,.35)}
    .dark-mode #Students .wms-cover{background: radial-gradient(600px 240px at 80% -20%, rgba(44,62,80,.35), transparent 60%), linear-gradient(135deg,#1b2632 0%,#2c3e50 50%,#1b2632 100%)}
    .dark-mode #Students .wms-avatar{border-color:rgba(255,255,255,.85)}
    .dark-mode #Students .wms-name{color:#e5e7eb}
    .dark-mode #Students .wms-desc{color:#cbd5e1}
    .dark-mode #Students .wms-badge{background:rgba(44,62,80,.25);border-color:rgba(44,62,80,.40);color:#e4edf5}

    /* ===== Contact – modern section ===== */
    .contact-section{position:relative;padding:64px 16px;background: radial-gradient(600px 240px at 80% -20%, rgba(206,83,39,.08), transparent 60%), linear-gradient(180deg, rgba(206,83,39,.06), transparent 40%)}
    .contact-wrap{max-width:1200px;margin:0 auto;display:grid;gap:20px;grid-template-columns:1.1fr 1fr}
    @media (max-width:980px){.contact-wrap{grid-template-columns:1fr}}
    .card{position:relative;border-radius:16px;border:1px solid rgba(2,6,23,.06);background:rgba(255,255,255,.9);box-shadow:0 10px 30px rgba(2,6,23,.08);overflow:hidden;backdrop-filter:blur(6px)}
    .dark-mode .card{background:rgba(20,26,34,.55);border-color:rgba(255,255,255,.1);box-shadow:0 10px 34px rgba(0,0,0,.35)}
    .card-head{padding:14px 18px;display:flex;align-items:center;gap:10px;border-bottom:1px solid rgba(2,6,23,.06);background:linear-gradient(90deg, rgba(44,62,80,.06), transparent)}
    .dark-mode .card-head{border-bottom-color:rgba(255,255,255,.08)}
    .card-head h3{margin:0;font-size:1.05rem;font-weight:800;display:flex;align-items:center;gap:8px}
    .card-body{padding:18px}

    /* Modern form (floating labels) */
    .modern-form{display:grid;gap:18px}
    .input-group{position:relative;display:flex;align-items:center}
    .input-group i{position:absolute;left:12px;color:#9ca3af;font-size:1rem;pointer-events:none}
    .input-group input,.input-group textarea{width:100%;padding:14px 14px 14px 36px;border-radius:10px;border:1px solid #d1d5db;background:#fff;font-size:.95rem;outline:none;transition:.2s}
    .input-group textarea{min-height:120px;resize:vertical}
    .input-group label{position:absolute;left:36px;top:14px;color:#9ca3af;font-size:.95rem;background:transparent;pointer-events:none;transition:.15s}
    .input-group input:focus,.input-group textarea:focus{border-color:var(--secondary-color);box-shadow:0 0 0 4px rgba(206,83,39,.15)}
    .input-group input:not(:placeholder-shown)+label,
    .input-group textarea:not(:placeholder-shown)+label,
    .input-group input:focus+label,
    .input-group textarea:focus+label{
      top:-8px;font-size:.75rem;background:#fff;padding:0 6px;color:var(--secondary-color)
    }
    .send-btn{background:var(--secondary-color);color:#fff;border:none;padding:14px 18px;font-size:.95rem;font-weight:700;border-radius:10px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;justify-content:center;transition:.2s}
    .send-btn:hover{transform:translateY(-1px);box-shadow:0 8px 18px rgba(206,83,39,.25)}
    .send-btn:active{transform:scale(.97)}
    .dark-mode .input-group input,.dark-mode .input-group textarea{background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.2);color:#e5e7eb}
    .dark-mode .input-group label{background:transparent;color:#cbd5e1}

    /* Responsive map */
    .map-shell{padding:0}
    .map-frame{aspect-ratio:16/9;width:100%;border:0;display:block;background:#eef2f7}
    .map-meta{padding:12px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px;border-top:1px solid rgba(2,6,23,.06)}
    .dark-mode .map-meta{border-top-color:rgba(255,255,255,.1)}
    .map-badge{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border-radius:999px;background:rgba(44,62,80,.05);color:#334155;font-weight:700;font-size:.85rem;border:1px solid rgba(44,62,80,.12)}
    .dark-mode .map-badge{background:rgba(255,255,255,.06);color:#e5e7eb;border-color:rgba(255,255,255,.12)}

    /* ===== Modern footer ===== */
    .wms-footer{
      margin-top:48px;color:#e5e7eb;
      background: radial-gradient(1000px 280px at 80% -20%, rgba(206,83,39,.15), transparent 60%),
                  linear-gradient(180deg, rgba(44,62,80,1) 0%, rgba(34,49,64,1) 100%);
      border-top:1px solid rgba(255,255,255,.08);
    }
    .dark-mode .wms-footer{
      background: radial-gradient(1000px 280px at 80% -20%, rgba(206,83,39,.18), transparent 60%),
                  linear-gradient(180deg, #1e2a36 0%, #18222c 100%);
      border-top-color:rgba(255,255,255,.14);
    }
    .wms-footer-inner{
      max-width:1200px;margin:0 auto;padding:22px 16px;display:grid;
      grid-template-columns:1fr auto auto;gap:16px;align-items:center;
    }
    .wms-footer-brand{display:inline-flex;align-items:center;gap:10px;color:#fff;text-decoration:none;font-weight:900;letter-spacing:.2px}
    .wms-footer-brand i{color:#cbd5e1}
    .wms-footer-links{display:inline-flex;gap:14px;flex-wrap:wrap}
    .wms-footer-links a{color:#e5e7eb;text-decoration:none;font-weight:700;opacity:.9;padding:6px 8px;border-radius:10px;transition:.15s}
    .wms-footer-links a:hover{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12)}
    .wms-footer-actions{display:flex;align-items:center;gap:10px}
    .wms-social,.wms-top{width:38px;height:38px;display:inline-flex;align-items:center;justify-content:center;border-radius:999px;text-decoration:none;color:#fff;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.18);transition:.15s}
    .wms-social:hover,.wms-top:hover{transform:translateY(-2px);background:rgba(255,255,255,.2)}
    .wms-footer-meta{display:flex;align-items:center;justify-content:center;gap:10px;padding:12px 16px;font-size:.92rem;color:#cbd5e1;border-top:1px solid rgba(255,255,255,.08)}
    .wms-footer-meta .dot{width:6px;height:6px;border-radius:999px;background:rgba(255,255,255,.4)}

    /* Responsive Footer */
    @media (max-width:900px){
      .wms-footer-inner{grid-template-columns:1fr;gap:10px;text-align:center}
      .wms-footer-actions{justify-content:center}
      .wms-footer-links{justify-content:center}
    }

    /* Smooth scroll */
    html{scroll-behavior:smooth}
  </style>
</head>
<body>

  <!-- Header -->
  <header id="Home" class="wms-header">
    <div class="wms-header-inner">
      <h1 class="wms-title">Student Management Webapplication (SMW)</h1>
   
    </div>
  </header>

  <!-- NAV -->
  <nav class="wms-nav" role="navigation" aria-label="Kryenavigimi">
    <div class="nav-inner">
      <a class="nav-brand" href="#Home" aria-label="Home">
        <i class="fas fa-graduation-cap"></i> 
      </a>

      <button class="nav-toggle" aria-expanded="false" aria-controls="navMenu" aria-label="Hap / mbyll menunë">
        <span></span><span></span><span></span>
      </button>

      <div id="navMenu" class="nav-menu" role="menu">
        <a class="nav-link" href="#Home" role="menuitem"><i class="fas fa-home"></i> Home</a>
        <a class="nav-link" href="#Students" role="menuitem"><i class="fas fa-users"></i> Students</a>
        <a class="nav-link" href="#Contact" role="menuitem"><i class="fas fa-envelope"></i> Contact</a>

        <div class="wms-actions" role="group" aria-label="Veprime">
          <button id="darkBtn" class="wms-icon-btn" title="Ndrysho modalitetin" aria-label="Ndrysho modalitetin">
            <i class="fa-solid fa-moon" aria-hidden="true"></i>
          </button>
          
        </div>
      </div>
    </div>
  </nav>

  <!-- Students -->
  <section id="Students">
    <?php foreach ($faculty_students as $faculty => $students): ?>
      <div class="wms-faculty">

        <div class="wms-faculty-head">
          <h2 class="wms-faculty-title">
            <span class="wms-dot"></span>
            <?= htmlspecialchars($faculty) ?>
          </h2>
          <span class="wms-count"></span>
        </div>

        <div class="wms-grid">
          <?php foreach ($students as $student): ?>
            <?php if (!empty($student['SName'])): 
              $full = trim(($student['SName'] ?? '') . ' ' . ($student['Surname'] ?? ''));
              $desc = $student['Description'] ?? '';
              $img  = !empty($student['image'])
                      ? 'data:image/jpeg;base64,' . base64_encode($student["image"])
                      : '';
            ?>
              <article class="wms-card" title="<?= htmlspecialchars($full) ?>">
                <div class="wms-cover"></div>

                <img class="wms-avatar"
                     src="<?= htmlspecialchars($img) ?>"
                     alt="Foto e <?= htmlspecialchars($full) ?>"
                     loading="lazy"
                     onerror="this.style.display='none'"/>

                <div class="wms-body">
                  <h3 class="wms-name"><?= htmlspecialchars($full) ?></h3>
                  <p class="wms-desc"><?= htmlspecialchars($desc) ?></p>
                </div>

                <span class="wms-badge">
                  <i class="fa-solid fa-building-columns"></i>
                  <?= htmlspecialchars($faculty) ?>
                </span>
              </article>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>

      </div>
    <?php endforeach; ?>
  </section>

  <!-- Contact -->
  <section id="Contact" class="contact-section" aria-labelledby="kont-title">
    <div class="contact-wrap">

      <!-- Card: Form -->
      <div class="card">
        <div class="card-head">
          <h3 id="kont-title"><i class="fa-solid fa-envelope"></i> Contact</h3>
        </div>
        <div class="card-body">
          <form action="addContact.php" method="post" class="modern-form" novalidate>
            <div class="input-group">
              <i class="fa-solid fa-heading"></i>
              <input type="text" name="Subject" required placeholder=" "/>
              <label>Subject</label>
            </div>

            <div class="input-group textarea-group">
              <i class="fa-solid fa-message"></i>
              <textarea name="Message" rows="6" required placeholder=" "></textarea>
              <label>Message</label>
            </div>

            <div class="input-group">
              <i class="fa-solid fa-at"></i>
              <input type="email" name="Email" required placeholder=" "/>
              <label>Email</label>
            </div>

            <button type="submit" name="addContact" class="send-btn">
              <i class="fa-solid fa-paper-plane"></i> Send
            </button>
          </form>
        </div>
      </div>

      <!-- Card: Map -->
      <div class="card map-shell">
        <div class="card-head">
          <h3><i class="fa-solid fa-map-location-dot"></i> Location</h3>
        </div>

        <iframe
          class="map-frame"
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2664.729952984261!2d11.508836711699947!3d48.09615295392454!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x479dd98aaf6b661f%3A0x3afeab762b633b34!2sSRH%20Campus%20Munich!5e0!3m2!1sen!2spl!4v1762108052219!5m2!1sen!2spl"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          allowfullscreen
          title="SRH Campus Munich">
        </iframe>

        <div class="map-meta">
          <span class="map-badge"><i class="fa-solid fa-location-dot"></i> SRH Campus Munich</span>
          <a class="send-btn" target="_blank" rel="noopener" href="https://maps.google.com/?q=SRH+Campus+Munich">
            <i class="fa-solid fa-route"></i> Open in Google Maps
          </a>
        </div>
      </div>

    </div>
  </section>

  <!-- Modern footer -->
  <footer class="wms-footer" role="contentinfo">
    <div class="wms-footer-inner">
      <a href="#Home" class="wms-footer-brand" aria-label="Back on top">
        <i class="fa-solid fa-graduation-cap" aria-hidden="true"></i>
        
      </a>

      <nav class="wms-footer-links" aria-label="Links">
        <a href="#Students">Students</a>
        <a href="#Contact">Contact</a>
        
      </nav>

      <div class="wms-footer-actions">
        <a href="https://facebook.com" aria-label="Facebook" class="wms-social"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="https://linkedin.com" aria-label="LinkedIn" class="wms-social"><i class="fa-brands fa-linkedin-in"></i></a>
        <a href="#Home" class="wms-top" aria-label="Kthehu lart"><i class="fa-solid fa-arrow-up"></i></a>
      </div>
    </div>

    <div class="wms-footer-meta">
      <span>&copy; <?php echo date('Y'); ?> LNH</span>
      <span class="dot" aria-hidden="true"></span>
      <span>Built for education</span>
    </div>
  </footer>

  <!-- JS -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const body   = document.body;
      const navTgl = document.querySelector('.nav-toggle');
      const navMenu = document.getElementById('navMenu');
      const darkBtn = document.getElementById('darkBtn');
      const darkIcon = darkBtn.querySelector('i');

      // Toggle NAV (mobile)
      navTgl.addEventListener('click', () => {
        const open = navMenu.classList.toggle('open');
        navTgl.classList.toggle('open', open);
        navTgl.setAttribute('aria-expanded', String(open));
      });

      // Dark mode
      function toggleDarkMode(){
        body.classList.toggle('dark-mode');
        darkIcon.classList.toggle('fa-moon');
        darkIcon.classList.toggle('fa-sun');
      }
      darkBtn.addEventListener('click', toggleDarkMode);

      // Close the menu when links are clicked (mobile UX)
      navMenu.querySelectorAll('a.nav-link').forEach(a => {
        a.addEventListener('click', () => {
          if (navMenu.classList.contains('open')) {
            navMenu.classList.remove('open');
            navTgl.classList.remove('open');
            navTgl.setAttribute('aria-expanded','false');
          }
        });
      });
    });
  </script>
</body>
</html>
