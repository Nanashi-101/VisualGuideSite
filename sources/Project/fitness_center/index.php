<?php
require 'config.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // contact form (the professor's addContact.php)
    $subject = trim($_POST['subject'] ?? ''); $message = trim($_POST['message'] ?? ''); $email = trim($_POST['email'] ?? '');
    if ($subject === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $msg = 'Please fill in every field with a valid email.';
    else {
        $pdo->prepare('INSERT INTO contacts (subject,message,email) VALUES (?,?,?)')->execute([mb_substr($subject, 0, 150), mb_substr($message, 0, 500), $email]);
        $msg = 'ok';
    }
}
$plans = $pdo->query('SELECT name, price_eur FROM plans ORDER BY price_eur')->fetchAll();
$perks = ['Basic' => ['Gym floor access', 'Locker room', 'Free intro session'], 'Premium' => ['Everything in Basic', 'All group classes', 'Sauna and recovery zone', 'Monthly trainer check-in'], 'Student' => ['Gym floor access', 'Off-peak group classes', 'Valid student ID required']];

// Unsplash CDN photo, resized and served as WebP/AVIF where the browser supports it.
function photo($id, $w) { return "https://images.unsplash.com/photo-$id?w=$w&q=70&auto=format&fit=crop"; }

$programs = [
    ['1541534741688-6078c6bfb5c5', 'Strength', 'Barbell technique, progressive overload and coaching on every lift.'],
    ['1599058917212-d750089bc07e', 'HIIT & Conditioning', 'Ropes, sleds and intervals that push your heart rate and your limits.'],
    ['1544367567-0f2fcb009e0b', 'Yoga & Mobility', 'Move better, recover faster and build a calm, strong core.'],
    ['1584466977773-e625c37cdd50', 'Personal Training', 'One-to-one sessions with a plan built around your goals.'],
];
$gallery = [
    ['1583454110551-21f2fa2afe61', 'Free-weight zone'], ['1549060279-7e168fcee0c2', 'Olympic platforms'], ['1571019613454-1cb2f99b2d8b', 'Studio classes'],
    ['1581009146145-b5ef050c2e1e', 'Strength floor'], ['1605296867304-46d5465a13f1', 'Lifting area'], ['1576678927484-cc907957088c', 'Dumbbells to 50 kg'],
];
page_head('Home'); ?>

<!-- Hero -->
<section class="hero">
  <img class="hero-bg" src="<?= photo('1517963879433-6ad2b056d712', 1920) ?>" alt="" fetchpriority="high">
  <div class="container"><div class="row"><div class="col-xl-7 col-lg-9 hero-in">
    <span class="eyebrow">Fitness studio · München</span>
    <h1 class="mb-4">Stronger every<br><span class="text-primary">single day.</span></h1>
    <p class="lead text-white-75 mb-4">Modern equipment, certified coaches and more than 60 classes a week. Train on your schedule, 24/7.</p>
    <div class="d-flex flex-wrap gap-3 mb-5">
      <a href="register.php" class="btn btn-primary btn-lg px-4">Start your membership <i class="bi bi-arrow-right"></i></a>
      <a href="#programs" class="btn btn-outline-light btn-lg px-4">Explore programs</a>
    </div>
    <div class="row g-3" style="max-width:560px">
      <div class="col-4"><div class="glass"><b>24/7</b><small>Access</small></div></div>
      <div class="col-4"><div class="glass"><b>60+</b><small>Classes / week</small></div></div>
      <div class="col-4"><div class="glass"><b>4.9★</b><small>Member rating</small></div></div>
    </div>
  </div></div></div>
  <a href="#about" class="scroll-hint" aria-label="Scroll down"><i class="bi bi-chevron-double-down"></i></a>
</section>

<!-- Marquee -->
<div class="marquee" aria-hidden="true"><div class="marquee-track">
  <?php for ($k = 0; $k < 2; $k++) foreach (['Strength', 'HIIT', 'Yoga', 'Boxing', 'Spinning', 'Mobility', 'Personal training', 'Recovery'] as $w) echo "<span>$w</span>"; ?>
</div></div>

<!-- About -->
<section id="about" class="section">
  <div class="container"><div class="row align-items-center g-4 g-lg-5">
    <div class="col-lg-6" data-reveal="left">
      <div class="about-media">
        <img class="main" src="<?= photo('1540497077202-7c8a3999166f', 900) ?>" alt="Bright training floor with cardio machines" loading="lazy" width="900" height="1125">
        <img class="inset" src="<?= photo('1518611012118-696072aa579a', 600) ?>" alt="Group class doing floor exercises" loading="lazy" width="600" height="600">
        <div class="about-badge"><b>12</b><small>years in<br>Munich</small></div>
      </div>
    </div>
    <div class="col-lg-6" data-reveal="right">
      <span class="eyebrow">About Kraftwerk</span>
      <h2 class="display-5 fw-bold mb-4">More than a gym. A place to grow.</h2>
      <p class="text-secondary mb-4">Since 2014 we have helped Munich train smarter. Our 2,000 m² studio combines a full strength floor, conditioning zone and calm studio spaces, with coaches who know your name.</p>
      <ul class="list-unstyled check mb-4">
        <li>Certified coaches on the floor every day</li>
        <li>Free intro session and training plan</li>
        <li>Sauna, showers and recovery area</li>
        <li>No contract lock-in: cancel monthly</li>
      </ul>
      <a href="#plans" class="btn btn-primary px-4">See memberships</a>
    </div>
  </div></div>
</section>

<!-- Programs: photo cards with overlay -->
<section id="programs" class="section pt-0">
  <div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-5" data-reveal="up">
      <div><span class="eyebrow">Programs</span><h2 class="display-5 fw-bold mb-0">Find your training</h2></div>
      <p class="text-secondary mb-0" style="max-width:380px">From your first squat to competition prep, there is a program for every level.</p>
    </div>
    <div class="row g-4">
      <?php foreach ($programs as $k => [$img, $title, $text]): ?>
      <div class="col-sm-6 col-lg-3" data-reveal="up" style="--d:<?= $k * .1 ?>s">
        <a href="register.php" class="tile program">
          <img src="<?= photo($img, 700) ?>" alt="<?= h($title) ?> training" loading="lazy" width="700" height="933">
          <span class="tile-num">0<?= $k + 1 ?></span>
          <div class="tile-body"><h3 class="h4 mb-1"><?= h($title) ?></h3>
            <div class="tile-more"><p class="text-white-75 small mb-2 mt-2"><?= h($text) ?></p><span class="text-primary fw-semibold">Join now <i class="bi bi-arrow-right"></i></span></div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Stats band -->
<section class="band" style="background-image:url('<?= photo('1534438327276-14e5300c3a48', 1920) ?>')">
  <div class="container"><div class="row g-4 text-center">
    <?php foreach ([[1500, '+', 'Active members'], [60, '+', 'Classes every week'], [25, '', 'Certified coaches'], [2000, ' m²', 'Training space']] as [$n, $suf, $label]): ?>
    <div class="col-6 col-lg-3"><div class="num" data-to="<?= $n ?>" data-suffix="<?= $suf ?>">0</div><div class="text-white-75 mt-2"><?= $label ?></div></div>
    <?php endforeach; ?>
  </div></div>
</section>

<!-- Plans (from the database) -->
<section id="plans" class="section plans">
  <div class="container">
    <div class="text-center mb-5" data-reveal="up"><span class="eyebrow">Memberships</span><h2 class="display-5 fw-bold">Simple, fair pricing</h2><p class="text-secondary">No joining fee. No lock-in. Cancel monthly.</p></div>
    <div class="row g-4 justify-content-center align-items-center">
      <?php foreach ($plans as $k => $p): $pop = $p['name'] === 'Premium'; ?>
      <div class="col-md-6 col-lg-4" data-reveal="up" style="--d:<?= $k * .1 ?>s">
        <div class="plan p-4 p-xl-5 <?= $pop ? 'popular py-xl-5' : '' ?>">
          <?php if ($pop): ?><span class="badge bg-primary badge-top px-3 py-2">Most popular</span><?php endif; ?>
          <h3 class="h4"><?= h($p['name']) ?></h3>
          <div class="my-4"><span class="price">€<?= number_format($p['price_eur'], 2, ',', '.') ?></span><span class="text-muted"> / month</span></div>
          <ul class="list-unstyled check mb-4">
            <?php foreach ($perks[$p['name']] ?? ['Gym floor access'] as $li): ?><li><?= h($li) ?></li><?php endforeach; ?>
          </ul>
          <a href="register.php" class="btn <?= $pop ? 'btn-primary' : 'btn-outline-primary' ?> w-100">Choose <?= h($p['name']) ?></a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Gallery with hover overlay -->
<section class="section">
  <div class="container">
    <div class="text-center mb-5" data-reveal="up"><span class="eyebrow">Inside Kraftwerk</span><h2 class="display-5 fw-bold">Take a look around</h2></div>
    <div class="gallery">
      <?php foreach ($gallery as $k => [$img, $cap]): ?>
      <a href="#contact" class="tile" data-reveal="zoom" style="--d:<?= ($k % 3) * .1 ?>s" aria-label="<?= h($cap) ?>: book a tour">
        <img src="<?= photo($img, $k === 0 ? 1000 : 600) ?>" alt="<?= h($cap) ?>" loading="lazy">
        <div class="tile-body"><i class="bi bi-camera text-primary"></i> <span class="fw-semibold"><?= h($cap) ?></span></div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="pb-5">
  <div class="container" data-reveal="zoom">
    <div class="cta">
      <img src="<?= photo('1594737625785-a6cbdabd333c', 1600) ?>" alt="" loading="lazy">
      <div class="row align-items-center g-4">
        <div class="col-lg-8"><h2 class="display-6 fw-bold mb-2">Your first session is on us.</h2><p class="mb-0 text-white">Book a free intro with a coach and get your personal training plan.</p></div>
        <div class="col-lg-4 text-lg-end"><a href="register.php" class="btn btn-light btn-lg px-4 fw-bold">Claim free session</a></div>
      </div>
    </div>
  </div>
</section>

<!-- Contact -->
<section id="contact" class="section">
  <div class="container"><div class="row g-4 g-lg-5">
    <div class="col-lg-5" data-reveal="left">
      <span class="eyebrow">Contact</span>
      <h2 class="display-6 fw-bold mb-3">Questions? Let's talk.</h2>
      <p class="text-secondary mb-4">Ask about memberships, classes or a guided tour. We answer within one working day.</p>
      <div class="d-flex gap-3 mb-3"><span class="info-icon"><i class="bi bi-geo-alt"></i></span><div><b>Address</b><div class="text-secondary">Musterstraße 12, 80331 München</div></div></div>
      <div class="d-flex gap-3 mb-3"><span class="info-icon"><i class="bi bi-clock"></i></span><div><b>Opening hours</b><div class="text-secondary">Members 24/7 · Staffed Mon–Fri 7:00–22:00</div></div></div>
      <div class="d-flex gap-3"><span class="info-icon"><i class="bi bi-envelope"></i></span><div><b>Email</b><div class="text-secondary">hallo@kraftwerk-fitness.example</div></div></div>
    </div>
    <div class="col-lg-7" data-reveal="right">
      <div class="panel p-4 p-md-5">
        <?php if ($msg === 'ok'): ?><div class="alert alert-success"><i class="bi bi-check-circle"></i> Thank you, we got your message.</div><?php else: echo alert($msg); endif; ?>
        <form method="post" action="#contact">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-semibold" for="c-email">Your email</label><input id="c-email" type="email" name="email" class="form-control" autocomplete="email" required></div>
            <div class="col-md-6"><label class="form-label fw-semibold" for="c-subj">Subject</label><input id="c-subj" name="subject" class="form-control" maxlength="150" required></div>
            <div class="col-12"><label class="form-label fw-semibold" for="c-msg">Message</label><textarea id="c-msg" name="message" class="form-control" rows="5" maxlength="500" required></textarea></div>
            <div class="col-12"><button class="btn btn-primary btn-lg px-4"><i class="bi bi-send"></i> Send message</button></div>
          </div>
        </form>
      </div>
    </div>
  </div></div>
</section>
<?php page_foot();
