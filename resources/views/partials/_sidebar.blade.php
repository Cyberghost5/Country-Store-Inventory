{{--
  Shared sidebar partial.
  Requires: $user (authenticated User)
--}}
@php $_u = auth()->user(); @endphp

{{-- Sidebar-specific styles injected here so all pages get them --}}
<style>
  .nav-user-card { display:flex; align-items:center; gap:10px; padding:10px 14px; background:#f4f0e8; border-radius:10px; margin-bottom:4px; }
  .nav-user-avatar { width:36px; height:36px; border-radius:50%; background:#1d086c; color:#ffd900; display:grid; place-items:center; font-size:.95rem; font-weight:700; flex-shrink:0; }
  .nav-user-info { display:flex; flex-direction:column; min-width:0; }
  .nav-user-name { display:block; font-size:.82rem; font-weight:600; color:#2e342b; line-height:1.2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .nav-user-role { display:block; font-size:.7rem; color:#9a9488; text-transform:capitalize; }
  .nav-link-btn { background:transparent; border:none; cursor:pointer; font-family:inherit; font-size:.83rem; color:#4f574c; }
  .nav-soon { font-size:.6rem; font-weight:600; background:#ffd900; color:#1d086c; padding:2px 7px; border-radius:999px; margin-left:auto; }
  .disabled-link { opacity:.55; cursor:default; pointer-events:none; }
</style>

<button class="sidebar-close" id="sidebarClose" aria-label="Close navigation">
  <i class="bi bi-x-lg"></i>
</button>

<div class="brand-block">
  <img src="{{ asset('assets/img/logo.png') }}" alt="Country Store logo"
       style="height: 48px; width: 48px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);" />
  <div>
    <h1>Country Store</h1>
    <p>Point of Sale</p>
  </div>
</div>

{{-- Main Menu --}}
<p class="menu-label">Main Menu</p>
<nav class="nav-links">

  <a href="{{ route('dashboard') }}"
     class="nav-link nav-link-anchor {{ request()->routeIs('dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid-1x2 nav-icon"></i>Dashboard
  </a>

  {{-- Products - admin & super_admin only --}}
  @if ($_u->isAdmin())
    <a href="{{ route('products.index') }}"
       class="nav-link nav-link-anchor {{ request()->routeIs('products.*') ? 'active' : '' }}">
      <i class="bi bi-box-seam nav-icon"></i>Products
    </a>
  @endif

  {{-- Opening Stock --}}
  @if ($_u->isAdminOrStaff())
    <a href="{{ route('opening_stock.index') }}"
       class="nav-link nav-link-anchor {{ request()->routeIs('opening_stock.*') ? 'active' : '' }}">
      <i class="bi bi-archive nav-icon"></i>Opening Stock
    </a>
  @endif

  {{-- Closing Stock --}}
  @if ($_u->isAdminOrStaff())
    <a href="{{ route('closing_stock.index') }}"
       class="nav-link nav-link-anchor {{ request()->routeIs('closing_stock.*') ? 'active' : '' }}">
      <i class="bi bi-archive-fill nav-icon"></i>Closing Stock
    </a>
  @endif

  {{-- Sales --}}
  @if ($_u->isAdminOrStaff())
    <a href="{{ route('sales.index') }}"
       class="nav-link nav-link-anchor {{ request()->routeIs('sales.*') ? 'active' : '' }}">
      <i class="bi bi-bag-check nav-icon"></i>Sales
    </a>
  @endif

  {{-- Expenses --}}
  @if ($_u->isAdminOrStaff())
    <a href="{{ route('expenses.index') }}"
       class="nav-link nav-link-anchor {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
      <i class="bi bi-cash-stack nav-icon"></i>Expenses
    </a>
  @endif

  {{-- Purchases --}}
  @if ($_u->isAdminOrStaff())
    <a href="{{ route('purchases.index') }}"
       class="nav-link nav-link-anchor {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
      <i class="bi bi-box-arrow-in-down nav-icon"></i>Purchases
    </a>
  @endif

  {{-- Reports --}}
  @if ($_u->isAdmin())
    <a href="{{ route('reports.index') }}"
       class="nav-link nav-link-anchor {{ request()->routeIs('reports.*') ? 'active' : '' }}">
      <i class="bi bi-bar-chart-line nav-icon"></i>Reports
    </a>
  @endif  

  {{-- Notifications - admin & super_admin only --}}
  @if ($_u->isAdmin())
    @php $_notifCount = $_u->unreadNotifications()->count(); @endphp
    <a href="{{ route('notifications.index') }}"
       class="nav-link nav-link-anchor {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
      <i class="bi bi-bell nav-icon"></i>Notifications
      @if ($_notifCount > 0)
        <span class="notif-nav-badge">{{ $_notifCount > 99 ? '99+' : $_notifCount }}</span>
      @endif
    </a>
  @endif

  {{-- People - admin & super_admin only --}}
  @if ($_u->isAdmin())
    <a href="{{ route('people.index') }}"
       class="nav-link nav-link-anchor {{ request()->routeIs('people.*') ? 'active' : '' }}">
      <i class="bi bi-people nav-icon"></i>People
    </a>
  @endif


</nav>

{{-- Account section --}}
<p class="menu-label" style="margin-top: auto; padding-top: 20px;">Account</p>
<nav class="nav-links">
  <div class="nav-user-card">
    <div class="nav-user-avatar">
      {{ strtoupper(substr($_u->name, 0, 1)) }}
    </div>
    <div class="nav-user-info">
      <span class="nav-user-name">{{ $_u->name }}</span>
      <span class="nav-user-role">{{ ucfirst(str_replace('_', ' ', $_u->role)) }}</span>
    </div>
  </div>

  <form method="POST" action="{{ route('logout') }}" style="margin-top: 6px;">
    @csrf
    <button type="submit" class="nav-link nav-link-btn" style="width:100%;text-align:left;">
      <i class="bi bi-box-arrow-left nav-icon"></i>Sign Out
    </button>
  </form>
</nav>
