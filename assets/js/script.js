/* ==============================================================
   Mbuya Parents' School — script.js
   Handles: mobile nav toggle, gallery filtering, lightbox
   ============================================================== */

document.addEventListener('DOMContentLoaded', function () {

  /* ---- Mobile nav toggle ---- */
  var navToggle = document.getElementById('navToggle');
  var mainNav = document.getElementById('mainNav');
  if (navToggle && mainNav) {
    navToggle.addEventListener('click', function () {
      var isOpen = mainNav.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
    // Close menu when a link is tapped (mobile)
    mainNav.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        mainNav.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  /* ---- Gallery category filter ---- */
  var filterButtons = document.querySelectorAll('.filter-btn');
  var galleryItems = document.querySelectorAll('.gallery-item');
  filterButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      filterButtons.forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      var cat = btn.getAttribute('data-filter');
      galleryItems.forEach(function (item) {
        if (cat === 'all' || item.getAttribute('data-category') === cat) {
          item.style.display = '';
        } else {
          item.style.display = 'none';
        }
      });
    });
  });

  /* ---- Lightbox ---- */
  var lightbox = document.getElementById('lightbox');
  if (lightbox) {
    var lightboxImg = document.getElementById('lightboxImg');
    var lightboxCap = document.getElementById('lightboxCap');
    var closeBtn = lightbox.querySelector('.lightbox-close');

    galleryItems.forEach(function (item) {
      item.addEventListener('click', function () {
        var img = item.querySelector('img');
        lightboxImg.src = img.src;
        lightboxImg.alt = img.alt;
        lightboxCap.textContent = img.alt;
        lightbox.classList.add('open');
      });
    });

    function closeLightbox() { lightbox.classList.remove('open'); }
    closeBtn.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', function (e) {
      if (e.target === lightbox) closeLightbox();
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeLightbox();
    });
  }

});
