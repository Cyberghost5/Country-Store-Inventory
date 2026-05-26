{{-- Shared sidebar JS --}}
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
<script>
  (function() {
    var sidebar  = document.getElementById('sidebar');
    var backdrop = document.getElementById('sidebarBackdrop');
    var toggle   = document.getElementById('sidebarToggle');
    var close    = document.getElementById('sidebarClose');
    function openSidebar()  { sidebar.classList.add('is-open'); backdrop.classList.add('is-open'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebar.classList.remove('is-open'); backdrop.classList.remove('is-open'); document.body.style.overflow = ''; }
    if (toggle)   toggle.addEventListener('click', openSidebar);
    if (close)    close.addEventListener('click', closeSidebar);
    if (backdrop) backdrop.addEventListener('click', closeSidebar);
  })();
</script>
