<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - Country Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}" />
    <style>
      /* ── Extra store-specific tweaks ── */
      .welcome-banner {
        background: linear-gradient(135deg, #1d086c 0%, #2f1295 100%);
        border-radius: 16px;
        padding: 28px 32px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
      }
      .welcome-banner h3 {
        font-size: 1.35rem;
        font-weight: 700;
        margin: 0 0 4px;
      }
      .welcome-banner p {
        font-size: 0.82rem;
        opacity: 0.75;
        margin: 0;
      }
      .welcome-banner .banner-icon {
        font-size: 3.2rem;
        opacity: 0.2;
        flex-shrink: 0;
      }
      .coming-soon-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
        margin-top: 24px;
      }
      .coming-soon-card {
        background: #fff;
        border-radius: 14px;
        padding: 24px 20px;
        border: 1.5px dashed #ddd7c8;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        opacity: 0.7;
      }
      .coming-soon-card .cs-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: #f4f0e8;
        display: grid;
        place-items: center;
        font-size: 1.2rem;
        color: #8a7e6a;
      }
      .coming-soon-card h4 {
        font-size: 0.9rem;
        font-weight: 600;
        color: #2e342b;
        margin: 0;
      }
      .coming-soon-card p {
        font-size: 0.75rem;
        color: #9a9488;
        margin: 0;
        line-height: 1.5;
      }
      .section-title {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #9a9488;
      }
      .stat-note {
        font-size: 0.7rem;
        color: #b0a898;
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 4px;
      }
      .cs-badge {
        font-size: 0.65rem;
        font-weight: 600;
        background: #ffd900;
        color: #1d086c;
        padding: 3px 9px;
        border-radius: 999px;
      }
      .nav-soon {
        font-size: 0.6rem;
        font-weight: 600;
        background: #ffd900;
        color: #1d086c;
        padding: 2px 7px;
        border-radius: 999px;
        margin-left: auto;
      }
      .disabled-link {
        opacity: 0.55;
        cursor: default;
        pointer-events: none;
      }
      .nav-user-card {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        background: #f4f0e8;
        border-radius: 10px;
        margin-bottom: 4px;
      }
      .nav-user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #1d086c;
        color: #ffd900;
        display: grid;
        place-items: center;
        font-size: 0.95rem;
        font-weight: 700;
        flex-shrink: 0;
      }
      .nav-user-name {
        display: block;
        font-size: 0.82rem;
        font-weight: 600;
        color: #2e342b;
        line-height: 1.2;
      }
      .nav-user-role {
        display: block;
        font-size: 0.7rem;
        color: #9a9488;
        text-transform: capitalize;
      }
      .nav-link-btn {
        background: transparent;
        border: none;
        cursor: pointer;
        font-family: inherit;
        font-size: 0.83rem;
        color: #4f574c;
      }
      .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        color: #ffd900;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 999px;
        margin-top: 8px;
      }
    </style>
  </head>
  <body>
    @include('partials._mobile_topbar')

    <div class="app-shell">
      <aside class="sidebar" id="sidebar">
        @include('partials._sidebar', ['user' => $user])
      </aside>

      <main class="main-content">

        {{-- Page header --}}
        <header class="dash-header">
          <div>
            <h2 class="dash-title">Dashboard</h2>
            <p class="dash-sub">{{ now()->format('l, d F Y') }} &middot; {{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
          </div>
        </header>

        {{-- Welcome banner --}}
        <div class="welcome-banner">
          <div>
            <h3>Welcome back, {{ explode(' ', $user->name)[0] }}! 👋</h3>
            <p>{{ now()->format('l, d F Y') }}</p>
            <span class="role-badge">
              <i class="bi bi-person-badge"></i>
              {{ ucfirst(str_replace('_', ' ', $user->role)) }}
            </span>
          </div>
          <i class="bi bi-shop banner-icon"></i>
        </div>

        {{-- ── Super Admin Dashboard ── --}}
        @if ($user->isSuperAdmin())
          <section>
            <h3 class="section-title" style="margin-bottom:16px;">Store Overview</h3>
            <div class="kpi-grid">
              <a href="{{ route('products.index') }}" class="stat-card" style="text-decoration:none;">
                <div class="stat-top">
                  <span class="mini-icon" style="background:#eef2ff;"><i class="bi bi-box-seam" style="color:#1d086c;"></i></span>
                </div>
                <h4 class="stat-value">{{ $stats['total_products'] }}</h4>
                <p class="stat-label">Total Products</p>
              </a>
              <a href="{{ route('opening_stock.index') }}" class="stat-card success" style="text-decoration:none;">
                <div class="stat-top">
                  <span class="mini-icon" style="background:#eaf6ee;"><i class="bi bi-archive" style="color:#246b3a;"></i></span>
                </div>
                <h4 class="stat-value">{{ $stats['today_stock'] }}</h4>
                <p class="stat-label">Opening Stock Today</p>
              </a>
              <a href="{{ route('sales.index') }}" class="stat-card warn" style="text-decoration:none;">
                <div class="stat-top">
                  <span class="mini-icon" style="background:#fff8e1;"><i class="bi bi-bag-check-fill" style="color:#b38b16;"></i></span>
                </div>
                <h4 class="stat-value">&#8358;{{ number_format($stats['today_sales'], 2) }}</h4>
                <p class="stat-label">Today's Sales</p>
              </a>
              <a href="{{ route('expenses.index') }}" class="stat-card danger" style="text-decoration:none;">
                <div class="stat-top">
                  <span class="mini-icon" style="background:#fdecea;"><i class="bi bi-cash-stack" style="color:#b33a36;"></i></span>
                </div>
                <h4 class="stat-value">&#8358;{{ number_format($stats['today_expenses'], 2) }}</h4>
                <p class="stat-label">Today's Expenses</p>
              </a>
            </div>
          </section>
        @endif

        {{-- ── Admin Dashboard ── --}}
        @if ($user->role === 'admin')
          <section>
            <h3 class="section-title" style="margin-bottom:16px;">Store Overview</h3>
            <div class="kpi-grid">
              <a href="{{ route('products.index') }}" class="stat-card" style="text-decoration:none;">
                <div class="stat-top">
                  <span class="mini-icon" style="background:#eef2ff;"><i class="bi bi-box-seam" style="color:#1d086c;"></i></span>
                </div>
                <h4 class="stat-value">{{ $stats['total_products'] }}</h4>
                <p class="stat-label">Total Products</p>
              </a>
              <a href="{{ route('opening_stock.index') }}" class="stat-card success" style="text-decoration:none;">
                <div class="stat-top">
                  <span class="mini-icon" style="background:#eaf6ee;"><i class="bi bi-archive" style="color:#246b3a;"></i></span>
                </div>
                <h4 class="stat-value">{{ $stats['today_stock'] }}</h4>
                <p class="stat-label">Opening Stock Today</p>
              </a>
              <a href="{{ route('sales.index') }}" class="stat-card warn" style="text-decoration:none;">
                <div class="stat-top">
                  <span class="mini-icon" style="background:#fff8e1;"><i class="bi bi-bag-check-fill" style="color:#b38b16;"></i></span>
                </div>
                <h4 class="stat-value">&#8358;{{ number_format($stats['today_sales'], 2) }}</h4>
                <p class="stat-label">Today's Sales</p>
              </a>
              <a href="{{ route('expenses.index') }}" class="stat-card danger" style="text-decoration:none;">
                <div class="stat-top">
                  <span class="mini-icon" style="background:#fdecea;"><i class="bi bi-cash-stack" style="color:#b33a36;"></i></span>
                </div>
                <h4 class="stat-value">&#8358;{{ number_format($stats['today_expenses'], 2) }}</h4>
                <p class="stat-label">Today's Expenses</p>
              </a>
            </div>
          </section>
        @endif

        {{-- ── Staff Dashboard ── --}}
        @if ($user->role === 'staff')
          <section>
            <h3 class="section-title" style="margin-bottom:16px;">My Summary</h3>
            <div class="kpi-grid">
              <a href="{{ route('opening_stock.index') }}" class="stat-card success" style="text-decoration:none;">
                <div class="stat-top">
                  <span class="mini-icon" style="background:#eaf6ee;"><i class="bi bi-archive" style="color:#246b3a;"></i></span>
                </div>
                <h4 class="stat-value">{{ $stats['today_stock'] }}</h4>
                <p class="stat-label">Opening Stock Recorded Today</p>
              </a>
              <a href="{{ route('sales.index') }}" class="stat-card warn" style="text-decoration:none;">
                <div class="stat-top">
                  <span class="mini-icon" style="background:#fff8e1;"><i class="bi bi-bag-check-fill" style="color:#b38b16;"></i></span>
                </div>
                <h4 class="stat-value">&#8358;{{ number_format($stats['today_sales'], 2) }}</h4>
                <p class="stat-label">My Sales Today</p>
              </a>
              <a href="{{ route('expenses.index') }}" class="stat-card danger" style="text-decoration:none;">
                <div class="stat-top">
                  <span class="mini-icon" style="background:#fdecea;"><i class="bi bi-cash-stack" style="color:#b33a36;"></i></span>
                </div>
                <h4 class="stat-value">&#8358;{{ number_format($stats['today_expenses'], 2) }}</h4>
                <p class="stat-label">My Expenses Today</p>
              </a>
            </div>
          </section>
        @endif

        {{-- Coming soon modules --}}
        <section style="margin-top: 32px;">
          <h3 class="section-title" style="margin-bottom:16px;">What's Active</h3>
          <div class="coming-soon-grid">
            <a href="{{ route('products.index') }}" class="coming-soon-card" style="opacity:1;text-decoration:none;border-style:solid;border-color:#cdecd8;">
              <div class="cs-icon" style="background:#eaf6ee;color:#246b3a;"><i class="bi bi-box-seam"></i></div>
              <h4>Products</h4>
              <p>Manage store products and prices.</p>
            </a>
            <a href="{{ route('opening_stock.index') }}" class="coming-soon-card" style="opacity:1;text-decoration:none;border-style:solid;border-color:#cdecd8;">
              <div class="cs-icon" style="background:#eef2ff;color:#1d086c;"><i class="bi bi-archive"></i></div>
              <h4>Opening Stock</h4>
              <p>Record daily stock per product.</p>
            </a>
            <a href="{{ route('sales.index') }}" class="coming-soon-card" style="opacity:1;text-decoration:none;border-style:solid;border-color:#cdecd8;">
              <div class="cs-icon" style="background:#fff8e1;color:#b38b16;"><i class="bi bi-bag-check"></i></div>
              <h4>Sales</h4>
              <p>Log daily sales transactions.</p>
            </a>
            <a href="{{ route('expenses.index') }}" class="coming-soon-card" style="opacity:1;text-decoration:none;border-style:solid;border-color:#cdecd8;">
              <div class="cs-icon" style="background:#fdecea;color:#b33a36;"><i class="bi bi-cash-stack"></i></div>
              <h4>Expenses</h4>
              <p>Track store expenses.</p>
            </a>
            @if ($user->isAdmin())
            <a href="{{ route('reports.index') }}" class="coming-soon-card" style="opacity:1;text-decoration:none;border-style:solid;border-color:#cdecd8;">
              <div class="cs-icon" style="background:#f3e8ff;color:#7c3aed;"><i class="bi bi-bar-chart-line"></i></div>
              <h4>Reports</h4>
              <p>Daily & weekly summaries.</p>
            </a>
            @endif
          </div>
        </section>

      </main>
    </div>

    @include('partials._sidebar_js')
  </body>
</html>
