// Page motion: navbar on scroll, scroll reveals, counting numbers, hero parallax.
(function () {
  const still = matchMedia("(prefers-reduced-motion: reduce)").matches;
  const nav = document.querySelector(".site-nav");
  const hero = document.querySelector(".hero-bg");

  // Count from 0 to data-to once the number scrolls into view.
  function count(el) {
    const to = +el.dataset.to, suffix = el.dataset.suffix || "";
    if (still) { el.textContent = to.toLocaleString("de-DE") + suffix; return; }
    const t0 = performance.now(), dur = 1600;
    (function step(t) {
      const p = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.round(to * e).toLocaleString("de-DE") + suffix;
      if (p < 1) requestAnimationFrame(step);
    })(t0);
  }

  const io = new IntersectionObserver((entries) => entries.forEach((e) => {
    if (!e.isIntersecting) return;
    e.target.classList.add("show");
    if (e.target.dataset.to) count(e.target);
    io.unobserve(e.target);
  }), { threshold: 0.15, rootMargin: "0px 0px -40px 0px" });
  document.querySelectorAll("[data-reveal], [data-to]").forEach((el) => io.observe(el));

  let ticking = false;
  function onScroll() {
    nav && nav.classList.toggle("scrolled", scrollY > 30);
    if (hero && !still && scrollY < innerHeight) hero.style.transform = "translateY(" + scrollY * 0.3 + "px)";
    ticking = false;
  }
  addEventListener("scroll", () => { if (!ticking) { ticking = true; requestAnimationFrame(onScroll); } }, { passive: true });
  onScroll();

  // Close the mobile menu after tapping a link that stays on this page.
  document.querySelectorAll("#nav .nav-link").forEach((a) => a.addEventListener("click", () => {
    const menu = document.getElementById("nav");
    if (menu.classList.contains("show")) bootstrap.Collapse.getOrCreateInstance(menu).hide();
  }));
})();
