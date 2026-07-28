const navToggle = document.getElementById("navToggle");
const mobileNav = document.getElementById("mobileNav");
const mobileNavClose = document.getElementById("mobileNavClose");
function openNav() {
  mobileNav.classList.add("open");
  navToggle.setAttribute("aria-expanded", "true");
}
function closeNav() {
  mobileNav.classList.remove("open");
  navToggle.setAttribute("aria-expanded", "false");
}
navToggle &&
  navToggle.addEventListener("click", () => {
    mobileNav.classList.contains("open") ? closeNav() : openNav();
  });
mobileNavClose && mobileNavClose.addEventListener("click", closeNav);
mobileNav &&
  mobileNav
    .querySelectorAll("a")
    .forEach((a) => a.addEventListener("click", closeNav));

const revealEls = document.querySelectorAll(".reveal");
if ("IntersectionObserver" in window) {
  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("in");
          io.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12 },
  );
  revealEls.forEach((el) => io.observe(el));
} else {
  revealEls.forEach((el) => el.classList.add("in"));
}

// ---------- FAQ accordion ----------
document.querySelectorAll(".faq-question").forEach(function (btn) {
  btn.addEventListener("click", function () {
    var item = btn.closest(".faq-item");
    var answer = item.querySelector(".faq-answer");
    var isOpen = item.classList.contains("open");
    // close all
    document.querySelectorAll(".faq-item").forEach(function (i) {
      i.classList.remove("open");
      i.querySelector(".faq-answer").style.maxHeight = null;
    });
    // open clicked if it was closed
    if (!isOpen) {
      item.classList.add("open");
      answer.style.maxHeight = answer.scrollHeight + "px";
    }
  });
});

// ---------- Enquiry form ----------
// Real submission now goes to process_admissions.php (see admissions.php).
// This just disables the button and shows a sending state; it does NOT
// preventDefault, so the browser's normal POST still happens.
var enquiryForm = document.getElementById("enquiryForm");
if (enquiryForm) {
  enquiryForm.addEventListener("submit", function () {
    var btn = enquiryForm.querySelector('button[type="submit"]');
    if (btn) {
      btn.disabled = true;
      btn.textContent = "Sending…";
    }
  });
}

// ---------- Active nav link (current page) ----------
(function () {
  var page = window.location.pathname.split("/").pop() || "index.php";
  document.querySelectorAll(".main-nav a, .mobile-nav a").forEach(function (a) {
    var href = (a.getAttribute("href") || "").split("#")[0].split("/").pop();
    if (href && href === page) a.classList.add("current");
  });
})();

// ---------- Gallery filter ----------
document.querySelectorAll(".filter-btn").forEach(function (btn) {
  btn.addEventListener("click", function () {
    document.querySelectorAll(".filter-btn").forEach(function (b) {
      b.classList.remove("active");
    });
    btn.classList.add("active");
    var filter = btn.dataset.filter;
    document.querySelectorAll(".gallery-item").forEach(function (item) {
      item.style.display =
        filter === "all" || item.dataset.category === filter ? "" : "none";
    });
  });
});

// ---------- Lightbox ----------
var lightboxOverlay = document.getElementById("galleryLightbox");
var lightboxCaption = document.getElementById("lightboxCaption");
var lightboxSvgWrap = document.getElementById("lightboxSvgWrap");

document.querySelectorAll(".gallery-item").forEach(function (item) {
  item.addEventListener("click", function () {
    if (!lightboxOverlay) return;
    if (lightboxCaption)
      lightboxCaption.textContent = item.dataset.caption || "";
    if (lightboxSvgWrap) {
      var src = item.querySelector(".gallery-placeholder");
      lightboxSvgWrap.innerHTML = src ? src.outerHTML : "";
    }
    lightboxOverlay.classList.add("open");
    document.body.style.overflow = "hidden";
  });
});
function closeLightbox() {
  if (lightboxOverlay) {
    lightboxOverlay.classList.remove("open");
    document.body.style.overflow = "";
  }
}
if (lightboxOverlay) {
  lightboxOverlay.addEventListener("click", function (e) {
    if (e.target === lightboxOverlay) closeLightbox();
  });
}
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") closeLightbox();
});

// ---------- Newsletter form ----------
// Real submission now goes to process_newsletter.php (see news.php).
var newsletterForm = document.getElementById("newsletterForm");
if (newsletterForm) {
  newsletterForm.addEventListener("submit", function () {
    var btn = newsletterForm.querySelector('button[type="submit"]');
    if (btn) {
      btn.disabled = true;
      btn.textContent = "Subscribing…";
    }
  });
}

// ---------- Contact page form ----------
// Real submission now goes to process_contact.php (see contact.php).
var contactPageForm = document.getElementById("contactForm");
if (contactPageForm) {
  contactPageForm.addEventListener("submit", function () {
    var btn = contactPageForm.querySelector('button[type="submit"]');
    if (btn) {
      btn.disabled = true;
      btn.textContent = "Sending…";
    }
  });
}
