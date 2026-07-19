    </div>
  </div>
</div>

<script>
  var menuToggle = document.getElementById('menuToggle');
  var sidebar = document.getElementById('sidebar');
  var backdrop = document.getElementById('sidebarBackdrop');
  if (menuToggle) {
    menuToggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
      backdrop.classList.toggle('open');
    });
    backdrop.addEventListener('click', function () {
      sidebar.classList.remove('open');
      backdrop.classList.remove('open');
    });
  }

  // Confirm before any delete action
  document.querySelectorAll('.js-confirm-delete').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var msg = form.getAttribute('data-confirm') || 'Are you sure you want to delete this? This cannot be undone.';
      if (!confirm(msg)) { e.preventDefault(); }
    });
  });
</script>
</body>
</html>
