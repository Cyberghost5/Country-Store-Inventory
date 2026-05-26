<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reports - Country Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}" />
    <style>
      .section-title { font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#9a9488; margin-bottom:14px; }
      /* Type toggle */
      .rpt-toggle { display:inline-flex; background:#f4f0e8; border-radius:10px; padding:3px; gap:2px; margin-bottom:20px; }
      .rpt-toggle a { padding:8px 20px; border-radius:8px; font-size:.82rem; font-weight:600; color:#6b7280; text-decoration:none; transition:all .18s; }
      .rpt-toggle a.active { background:#1d086c; color:#fff; box-shadow:0 2px 8px rgba(29,8,108,.18); }
      /* Date bar */
      .rpt-date-bar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:20px; }
      .rpt-date-input { border:1.5px solid #ddd7c8; border-radius:8px; padding:8px 12px; font-family:inherit; font-size:.83rem; color:#2e342b; }
      .rpt-date-input:focus { outline:none; border-color:#1d086c; }
      .rpt-apply-btn { background:#ffd900; border:none; border-radius:8px; padding:8px 16px; font-family:inherit; font-size:.82rem; font-weight:600; color:#1d086c; cursor:pointer; }
      /* Net pill */
      .net-positive { background:#eaf6ee; color:#246b3a; font-size:.78rem; font-weight:700; padding:5px 14px; border-radius:999px; border:1px solid #cdecd8; }
      .net-negative { background:#fdecea; color:#b33a36; font-size:.78rem; font-weight:700; padding:5px 14px; border-radius:999px; border:1px solid #f1d3d3; }
      /* Chart containers */
      .chart-card { background:#fff; border-radius:14px; border:1.5px solid #e8e2d9; padding:20px; margin-bottom:16px; }
      .chart-card-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
      .chart-wrap { position:relative; width:100%; }
      /* Top products list */
      .top-prod-list { list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:10px; }
      .top-prod-item { display:flex; align-items:center; gap:10px; }
      .top-prod-rank { width:22px; height:22px; border-radius:50%; background:#1d086c; color:#ffd900; font-size:.65rem; font-weight:700; display:grid; place-items:center; flex-shrink:0; }
      .top-prod-name { flex:1; font-size:.83rem; font-weight:500; color:#2e342b; }
      .top-prod-bar-wrap { flex:2; background:#f4f0e8; border-radius:999px; height:7px; overflow:hidden; }
      .top-prod-bar { height:100%; border-radius:999px; background:#1d086c; }
      .top-prod-total { font-size:.78rem; font-weight:600; color:#1d086c; white-space:nowrap; }
      /* Category badges */
      .cat-badge { display:inline-flex; align-items:center; font-size:.7rem; font-weight:600; padding:3px 9px; border-radius:999px; }
      .cat-food        { background:#fff8e1; color:#b38b16; }
      .cat-transport   { background:#e8f4fd; color:#1565a8; }
      .cat-utilities   { background:#f3e8ff; color:#7c3aed; }
      .cat-supplies    { background:#eaf6ee; color:#246b3a; }
      .cat-maintenance { background:#fff0e6; color:#c2540a; }
      .cat-other       { background:#f4f0e8; color:#6b7280; }
      /* Two-column grid on desktop */
      .two-col-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
      @media (max-width:768px) { .two-col-grid { grid-template-columns:1fr; } }
      /* Absolute Sales breakdown */
      .abs-breakdown { display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-top:4px; }
      .abs-pill { background:#f4f0e8; border:1.5px solid #e8e2d9; border-radius:10px; padding:10px 16px; display:flex; flex-direction:column; gap:2px; }
      .abs-pill small { font-size:.68rem; text-transform:uppercase; letter-spacing:.06em; color:#9a9488; font-weight:600; }
      .abs-pill strong { font-size:.9rem; color:#2e342b; font-weight:700; }
      .abs-pill.abs-result { background:#fff8dc; border-color:#ffd900; }
      .abs-pill.abs-result strong { font-size:1.05rem; color:#1d086c; }
      .abs-op { font-size:1.1rem; font-weight:700; color:#9a9488; flex-shrink:0; }
    </style>
  </head>
  <body>
    @include('partials._mobile_topbar')
    <div class="app-shell">

      <aside class="sidebar" id="sidebar">
        @include('partials._sidebar', ['user' => $user])
      </aside>

      <main class="main-content">

        {{-- Header --}}
        <header class="topbar">
          <div class="title-block">
            <h2>Reports</h2>
            <p>Daily and weekly performance summaries.</p>
          </div>
        </header>

        {{-- Type toggle --}}
        <div class="rpt-toggle">
          <a href="{{ route('reports.index', ['type' => 'daily',  'date' => $date]) }}"
             class="{{ $type === 'daily'  ? 'active' : '' }}">
            <i class="bi bi-calendar-day"></i> Daily
          </a>
          <a href="{{ route('reports.index', ['type' => 'weekly', 'date' => $date]) }}"
             class="{{ $type === 'weekly' ? 'active' : '' }}">
            <i class="bi bi-calendar-week"></i> Weekly
          </a>
        </div>

        {{-- Date picker --}}
        <form method="GET" action="{{ route('reports.index') }}" class="rpt-date-bar">
          <input type="hidden" name="type" value="{{ $type }}" />
          @if ($type === 'daily')
            <i class="bi bi-calendar3" style="color:#9a9488;"></i>
            <input type="date" name="date" value="{{ $date }}"
                   max="{{ today()->toDateString() }}" class="rpt-date-input" />
            <button type="submit" class="rpt-apply-btn">View</button>
            @if ($date !== today()->toDateString())
              <a href="{{ route('reports.index', ['type' => 'daily']) }}"
                 style="font-size:.78rem;color:#9a9488;text-decoration:none;">Back to today</a>
            @endif
          @else
            <i class="bi bi-calendar-week" style="color:#9a9488;"></i>
            <span style="font-size:.82rem;font-weight:500;color:#4f574c;">
              Week of {{ $weekStart->format('d M') }} – {{ $weekEnd->format('d M Y') }}
            </span>
            <input type="date" name="date" value="{{ $date }}"
                   max="{{ today()->toDateString() }}" class="rpt-date-input" />
            <button type="submit" class="rpt-apply-btn">View Week</button>
          @endif
        </form>

        {{-- ═══ KPI Summary ═══ --}}
        @php
          $sStats = $type === 'daily' ? $dailyStats : $weeklyStats;
          $netClass = $sStats['net'] >= 0 ? 'net-positive' : 'net-negative';
          $netIcon  = $sStats['net'] >= 0 ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow';
        @endphp

        <section class="kpi-grid" style="margin-bottom:20px;">
          <article class="stat-card success">
            <div class="stat-top">
              <span class="mini-icon" style="background:#eaf6ee;"><i class="bi bi-bag-check-fill" style="color:#246b3a;"></i></span>
            </div>
            <h4 class="stat-value">₦{{ number_format($sStats['total_sales'], 2) }}</h4>
            <p class="stat-unit">{{ $type === 'daily' ? 'today' : 'this week' }}</p>
            <small class="stat-label">Total Sales</small>
          </article>
          <article class="stat-card danger">
            <div class="stat-top">
              <span class="mini-icon" style="background:#fdecea;"><i class="bi bi-cash-stack" style="color:#b33a36;"></i></span>
            </div>
            <h4 class="stat-value">₦{{ number_format($sStats['total_expenses'], 2) }}</h4>
            <p class="stat-unit">{{ $type === 'daily' ? 'today' : 'this week' }}</p>
            <small class="stat-label">Total Expenses</small>
          </article>
          <article class="stat-card">
            <div class="stat-top">
              <span class="mini-icon" style="background:#eef2ff;"><i class="bi bi-box-arrow-in-down" style="color:#1d086c;"></i></span>
            </div>
            <h4 class="stat-value" style="color:#1d086c;">₦{{ number_format($sStats['total_purchases'], 2) }}</h4>
            <p class="stat-unit">{{ $type === 'daily' ? 'today' : 'this week' }}</p>
            <small class="stat-label">Total Purchases</small>
          </article>
          <article class="stat-card {{ $sStats['net'] >= 0 ? '' : 'danger' }}">
            <div class="stat-top">
              <span class="mini-icon" style="background:{{ $sStats['net'] >= 0 ? '#eaf6ee' : '#fdecea' }};"><i class="bi {{ $netIcon }}" style="color:{{ $sStats['net'] >= 0 ? '#246b3a' : '#b33a36' }};"></i></span>
            </div>
            <h4 class="stat-value" style="color:{{ $sStats['net'] >= 0 ? '#246b3a' : '#b33a36' }};">
              {{ $sStats['net'] < 0 ? '-' : '' }}₦{{ number_format(abs($sStats['net']), 2) }}
            </h4>
            <p class="stat-unit">net</p>
            <small class="stat-label">Net (Sales − Expenses − Purchases)</small>
          </article>
        </section>

        {{-- ═══ Absolute Sales Card ═══ --}}
        <div class="chart-card" style="margin-bottom:20px;">
          <div class="chart-card-head">
            <h3 class="section-title" style="margin:0;">Absolute Sales</h3>
            <span style="font-size:.75rem;color:#9a9488;">
              Closing stock {{ $absStats['start_label'] }} + Purchases − Closing stock {{ $absStats['end_label'] }}
            </span>
          </div>
          @if (!$absStats['has_data'])
            <p style="font-size:.82rem;color:#9a9488;margin:0;">
              <i class="bi bi-info-circle"></i>
              No opening stock entries found for this period. Enter opening stock to enable this calculation.
            </p>
          @else
            <div class="abs-breakdown">
              <div class="abs-pill">
                <small>Closing Inv. ({{ $absStats['start_label'] }})</small>
                <strong>₦{{ number_format($absStats['closing_start'], 2) }}</strong>
              </div>
              <span class="abs-op">+</span>
              <div class="abs-pill">
                <small>Purchases</small>
                <strong>₦{{ number_format($absStats['purchases'], 2) }}</strong>
              </div>
              <span class="abs-op">−</span>
              <div class="abs-pill" style="{{ $absStats['end_pending'] ? 'opacity:.55;' : '' }}">
                <small>
                  Closing Inv. ({{ $absStats['end_label'] }})
                  @if ($absStats['end_pending'])
                    <span style="color:#b33a36;"> · pending</span>
                  @endif
                </small>
                <strong>₦{{ number_format($absStats['closing_end'], 2) }}</strong>
              </div>
              <span class="abs-op">=</span>
              <div class="abs-pill abs-result">
                <small>Absolute Sales</small>
                <strong>₦{{ number_format($absStats['absolute_sales'], 2) }}</strong>
              </div>
            </div>
            @if ($absStats['end_pending'])
              <p style="font-size:.75rem;color:#9a9488;margin:10px 0 0;">
                <i class="bi bi-clock"></i>
                Closing inventory for {{ $absStats['end_label'] }} is not yet entered — figure will update once tomorrow's opening stock is saved.
              </p>
            @endif
          @endif
        </div>
        @if ($type === 'daily')

          <div class="two-col-grid">

            {{-- Top Products --}}
            <div class="chart-card">
              <div class="chart-card-head">
                <h3 class="section-title" style="margin:0;">Top Products</h3>
                <span style="font-size:.75rem;color:#9a9488;">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span>
              </div>
              @if ($topProducts->isEmpty())
                <p style="font-size:.82rem;color:#9a9488;text-align:center;padding:20px 0;">No sales recorded yet.</p>
              @else
                @php $maxTotal = $topProducts->max('total') ?: 1; @endphp
                <ul class="top-prod-list">
                  @foreach ($topProducts as $i => $prod)
                    <li class="top-prod-item">
                      <span class="top-prod-rank">{{ $i + 1 }}</span>
                      <span class="top-prod-name">{{ $prod['name'] }}</span>
                      <div class="top-prod-bar-wrap">
                        <div class="top-prod-bar" style="width:{{ round(($prod['total'] / $maxTotal) * 100) }}%;"></div>
                      </div>
                      <span class="top-prod-total">₦{{ number_format($prod['total'], 2) }}</span>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>

            {{-- Expense Breakdown --}}
            <div class="chart-card">
              <div class="chart-card-head">
                <h3 class="section-title" style="margin:0;">Expenses by Category</h3>
                <span style="font-size:.75rem;color:#9a9488;">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span>
              </div>
              @if ($expenseByCategory->isEmpty())
                <p style="font-size:.82rem;color:#9a9488;text-align:center;padding:20px 0;">No expenses recorded yet.</p>
              @else
                <div class="chart-wrap" style="max-height:200px;">
                  <canvas id="expensePieChart"></canvas>
                </div>
              @endif
            </div>

            {{-- Purchases by Supplier --}}
            <div class="chart-card">
              <div class="chart-card-head">
                <h3 class="section-title" style="margin:0;">Purchases by Supplier</h3>
                <span style="font-size:.75rem;color:#9a9488;">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span>
              </div>
              @if ($purchaseBySupplier->isEmpty())
                <p style="font-size:.82rem;color:#9a9488;text-align:center;padding:20px 0;">No purchases recorded yet.</p>
              @else
                @php $maxPurch = $purchaseBySupplier->max('total') ?: 1; @endphp
                <ul class="top-prod-list">
                  @foreach ($purchaseBySupplier as $i => $sup)
                    <li class="top-prod-item">
                      <span class="top-prod-rank" style="background:#eef2ff;color:#1d086c;">{{ $i + 1 }}</span>
                      <span class="top-prod-name">{{ $sup['name'] }}</span>
                      <div class="top-prod-bar-wrap">
                        <div class="top-prod-bar" style="width:{{ round(($sup['total'] / $maxPurch) * 100) }}%;background:#1d086c;"></div>
                      </div>
                      <span class="top-prod-total" style="color:#1d086c;">₦{{ number_format($sup['total'], 2) }}</span>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>

          </div>

          {{-- Sales detail table --}}
          @if ($sales->isNotEmpty())
            <section class="card table-card" style="margin-bottom:16px;">
              <div style="padding:14px 20px 6px;">
                <h3 class="section-title">Sales Detail</h3>
              </div>
              <div class="table-scroll">
                <table class="inv-table">
                  <thead>
                    <tr>
                      <th>#</th><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th><th>Sold By</th><th>Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($sales as $i => $sale)
                      <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td><span class="inv-name">{{ $sale->product?->name ?? '—' }}</span></td>
                        <td>{{ number_format($sale->quantity) }} {{ $sale->product?->unit }}</td>
                        <td>₦{{ number_format($sale->unit_price, 2) }}</td>
                        <td><strong>₦{{ number_format($sale->total_amount, 2) }}</strong></td>
                        <td>{{ $sale->seller?->name ?? '—' }}</td>
                        <td style="color:#9a9488;font-size:.75rem;">{{ $sale->created_at->format('g:i A') }}</td>
                      </tr>
                    @endforeach
                    <tr style="background:#f4f0e8;font-weight:700;">
                      <td colspan="4" style="text-align:right;padding-right:16px;">Total</td>
                      <td>₦{{ number_format($sales->sum('total_amount'), 2) }}</td>
                      <td colspan="2"></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>
          @endif

          {{-- Expenses detail table --}}
          @if ($expenses->isNotEmpty())
            <section class="card table-card" style="margin-bottom:16px;">
              <div style="padding:14px 20px 6px;">
                <h3 class="section-title">Expenses Detail</h3>
              </div>
              <div class="table-scroll">
                <table class="inv-table">
                  <thead>
                    <tr>
                      <th>#</th><th>Description</th><th>Category</th><th>Amount</th><th>Recorded By</th><th>Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($expenses as $i => $expense)
                      <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td><span class="inv-name">{{ $expense->description }}</span></td>
                        <td>
                          <span class="cat-badge cat-{{ $expense->category }}">
                            {{ ucfirst($expense->category) }}
                          </span>
                        </td>
                        <td><strong>₦{{ number_format($expense->amount, 2) }}</strong></td>
                        <td>{{ $expense->recorder?->name ?? '—' }}</td>
                        <td style="color:#9a9488;font-size:.75rem;">{{ $expense->created_at->format('g:i A') }}</td>
                      </tr>
                    @endforeach
                    <tr style="background:#fdecea;font-weight:700;">
                      <td colspan="3" style="text-align:right;padding-right:16px;">Total</td>
                      <td>₦{{ number_format($expenses->sum('amount'), 2) }}</td>
                      <td colspan="2"></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>
          @endif

          {{-- Purchases detail table --}}
          @if ($purchases->isNotEmpty())
            <section class="card table-card" style="margin-bottom:16px;">
              <div style="padding:14px 20px 6px;">
                <h3 class="section-title">Purchases Detail</h3>
              </div>
              <div class="table-scroll">
                <table class="inv-table">
                  <thead>
                    <tr>
                      <th>#</th><th>Product</th><th>Qty</th><th>Unit Cost</th><th>Total</th><th>Supplier</th><th>Recorded By</th><th>Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($purchases as $i => $purchase)
                      <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td><span class="inv-name">{{ $purchase->product?->name ?? '—' }}</span></td>
                        <td>{{ number_format($purchase->quantity) }} {{ $purchase->product?->unit }}</td>
                        <td>₦{{ number_format($purchase->unit_cost, 2) }}</td>
                        <td><strong>₦{{ number_format($purchase->total_cost, 2) }}</strong></td>
                        <td>{{ $purchase->supplier?->name ?? '—' }}</td>
                        <td>{{ $purchase->recorder?->name ?? '—' }}</td>
                        <td style="color:#9a9488;font-size:.75rem;">{{ $purchase->created_at->format('g:i A') }}</td>
                      </tr>
                    @endforeach
                    <tr style="background:#eef2ff;font-weight:700;">
                      <td colspan="4" style="text-align:right;padding-right:16px;">Total</td>
                      <td style="color:#1d086c;">₦{{ number_format($purchases->sum('total_cost'), 2) }}</td>
                      <td colspan="3"></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>
          @endif

          {{-- Opening Stock table --}}
          <section class="card table-card" style="margin-bottom:16px;">
            <div style="padding:14px 20px 6px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
              <h3 class="section-title" style="margin:0;">Opening Stock — {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h3>
              @if ($openingStocks->isEmpty())
                <span style="font-size:.75rem;color:#9a9488;">No entries for this date</span>
              @else
                <span style="font-size:.75rem;color:#9a9488;">{{ $openingStocks->count() }} product{{ $openingStocks->count() === 1 ? '' : 's' }}</span>
              @endif
            </div>
            @if ($openingStocks->isEmpty())
              <p style="padding:4px 20px 16px; font-size:.82rem; color:#9a9488; margin:0;">
                <i class="bi bi-info-circle"></i> No opening stock recorded for this date.
              </p>
            @else
              <div class="table-scroll">
                <table class="inv-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Product</th>
                      <th>Unit</th>
                      <th>Opening Qty</th>
                      <th>Selling Price</th>
                      <th>Stock Value</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($openingStocks as $i => $stock)
                      @php $stockValue = $stock->quantity * ($stock->product?->selling_price ?? 0); @endphp
                      <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td><span class="inv-name">{{ $stock->product?->name ?? '—' }}</span></td>
                        <td style="color:#9a9488;">{{ $stock->product?->unit ?? '—' }}</td>
                        <td><strong>{{ number_format($stock->quantity) }}</strong></td>
                        <td>₦{{ number_format($stock->product?->selling_price ?? 0, 2) }}</td>
                        <td><strong>₦{{ number_format($stockValue, 2) }}</strong></td>
                      </tr>
                    @endforeach
                    <tr style="background:#f4f0e8; font-weight:700;">
                      <td colspan="3" style="text-align:right; padding-right:16px;">Total</td>
                      <td>{{ number_format($openingStocks->sum('quantity')) }}</td>
                      <td></td>
                      <td>₦{{ number_format($openingStocks->sum(fn($s) => $s->quantity * ($s->product?->selling_price ?? 0)), 2) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            @endif
          </section>

        @endif
        {{-- ═══ end daily ═══ --}}


        {{-- ═══ Weekly View ═══ --}}
        @if ($type === 'weekly')

          {{-- Bar chart --}}
          <div class="chart-card" style="margin-bottom:16px;">
            <div class="chart-card-head">
              <h3 class="section-title" style="margin:0;">Sales vs Expenses vs Purchases</h3>
              <span style="font-size:.75rem;color:#9a9488;">
                {{ $weekStart->format('d M') }} – {{ $weekEnd->format('d M Y') }}
              </span>
            </div>
            <div class="chart-wrap" style="height:260px;">
              <canvas id="weeklyBarChart"></canvas>
            </div>
          </div>

          <!-- <div class="two-col-grid"> -->

            {{-- Daily totals table --}}
            <section class="card table-card" style="margin-bottom:16px;">
              <div style="padding:14px 20px 6px;">
                <h3 class="section-title">Daily Breakdown</h3>
              </div>
              <div class="table-scroll">
                <table class="inv-table" style="margin:0;">
                  <thead>
                    <tr><th>Day</th><th>Sales</th><th>Expenses</th><th>Purchases</th><th>Net</th></tr>
                  </thead>
                  <tbody>
                    @foreach ($weekDays as $day)
                      @php $net = $day['sales'] - $day['expenses'] - $day['purchases']; @endphp
                      <tr>
                        <td style="font-size:.78rem;">{{ $day['label'] }}</td>
                        <td style="color:#246b3a;font-size:.78rem;font-weight:600;">₦{{ number_format($day['sales'], 0) }}</td>
                        <td style="color:#b33a36;font-size:.78rem;font-weight:600;">₦{{ number_format($day['expenses'], 0) }}</td>
                        <td style="color:#1d086c;font-size:.78rem;font-weight:600;">₦{{ number_format($day['purchases'], 0) }}</td>
                        <td style="font-size:.78rem;font-weight:700;color:{{ $net >= 0 ? '#246b3a' : '#b33a36' }};">
                          {{ $net < 0 ? '-' : '' }}₦{{ number_format(abs($net), 0) }}
                        </td>
                      </tr>
                    @endforeach
                    <tr style="background:#f4f0e8;font-weight:700;">
                      <td>Total</td>
                      <td style="color:#246b3a;">₦{{ number_format($weeklyStats['total_sales'], 0) }}</td>
                      <td style="color:#b33a36;">₦{{ number_format($weeklyStats['total_expenses'], 0) }}</td>
                      <td style="color:#1d086c;">₦{{ number_format($weeklyStats['total_purchases'], 0) }}</td>
                      <td style="color:{{ $weeklyStats['net'] >= 0 ? '#246b3a' : '#b33a36' }};">
                        {{ $weeklyStats['net'] < 0 ? '-' : '' }}₦{{ number_format(abs($weeklyStats['net']), 0) }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>

            {{-- Top products for the week --}}
            <div class="chart-card">
              <div class="chart-card-head">
                <h3 class="section-title" style="margin:0;">Top Products (Week)</h3>
              </div>
              @if ($topProducts->isEmpty())
                <p style="font-size:.82rem;color:#9a9488;text-align:center;padding:20px 0;">No sales this week.</p>
              @else
                @php $maxTotal = $topProducts->max('total') ?: 1; @endphp
                <ul class="top-prod-list">
                  @foreach ($topProducts as $i => $prod)
                    <li class="top-prod-item">
                      <span class="top-prod-rank">{{ $i + 1 }}</span>
                      <span class="top-prod-name">{{ $prod['name'] }}</span>
                      <div class="top-prod-bar-wrap">
                        <div class="top-prod-bar" style="width:{{ round(($prod['total'] / $maxTotal) * 100) }}%;"></div>
                      </div>
                      <span class="top-prod-total">₦{{ number_format($prod['total'], 0) }}</span>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>

          <!-- </div> -->

          {{-- Expense by category --}}
          @if ($expenseByCategory->isNotEmpty())
            <div class="chart-card">
              <div class="chart-card-head">
                <h3 class="section-title" style="margin:0;">Expense Breakdown by Category (Week)</h3>
              </div>
              <div style="display:flex;gap:24px;align-items:center;flex-wrap:wrap;">
                <div class="chart-wrap" style="max-height:200px;max-width:200px;">
                  <canvas id="expensePieChart"></canvas>
                </div>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:8px;">
                  @foreach ($expenseByCategory as $cat => $amt)
                    <li style="display:flex;align-items:center;gap:10px;">
                      <span class="cat-badge cat-{{ $cat }}">{{ ucfirst($cat) }}</span>
                      <strong style="font-size:.82rem;color:#2e342b;">₦{{ number_format($amt, 2) }}</strong>
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>
          @endif

        @endif
        {{-- ═══ end weekly ═══ --}}

      </main>
    </div>

    @include('partials._sidebar_js')

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script>
      Chart.defaults.font.family = 'Poppins, sans-serif';
      Chart.defaults.color       = '#9a9488';

      @if ($type === 'daily' && isset($expenseByCategory) && $expenseByCategory->isNotEmpty())
        new Chart(document.getElementById('expensePieChart'), {
          type: 'doughnut',
          data: {
            labels: {!! json_encode($expenseByCategory->keys()->map(fn($k) => ucfirst($k))->values()) !!},
            datasets: [{
              data: {!! json_encode($expenseByCategory->values()) !!},
              backgroundColor: ['#fff8e1','#e8f4fd','#f3e8ff','#eaf6ee','#fff0e6','#f4f0e8'],
              borderColor:     ['#b38b16','#1565a8','#7c3aed','#246b3a','#c2540a','#6b7280'],
              borderWidth: 1.5,
            }]
          },
          options: {
            cutout: '65%',
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } },
          }
        });
      @endif

      @if ($type === 'weekly' && isset($weekDays))
        new Chart(document.getElementById('weeklyBarChart'), {
          type: 'bar',
          data: {
            labels: {!! json_encode($weekDays->pluck('label')->values()) !!},
            datasets: [
              {
                label: 'Sales (₦)',
                data: {!! json_encode($weekDays->pluck('sales')->values()) !!},
                backgroundColor: 'rgba(36,107,58,0.75)',
                borderColor: '#246b3a',
                borderWidth: 1.5,
                borderRadius: 6,
                order: 3,
              },
              {
                label: 'Purchases (₦)',
                data: {!! json_encode($weekDays->pluck('purchases')->values()) !!},
                backgroundColor: 'rgba(29,8,108,0.65)',
                borderColor: '#1d086c',
                borderWidth: 1.5,
                borderRadius: 6,
                order: 2,
              },
              {
                label: 'Expenses (₦)',
                data: {!! json_encode($weekDays->pluck('expenses')->values()) !!},
                backgroundColor: 'rgba(179,58,54,0.15)',
                borderColor: '#b33a36',
                borderWidth: 2,
                borderRadius: 6,
                type: 'line',
                tension: 0.35,
                pointRadius: 4,
                pointBackgroundColor: '#b33a36',
                fill: true,
                order: 1,
              }
            ]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              y: {
                beginAtZero: true,
                ticks: { callback: v => '₦' + v.toLocaleString() },
                grid: { color: '#f4f0e8' },
              },
              x: { grid: { display: false } }
            },
            plugins: {
              legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } },
              tooltip: {
                callbacks: {
                  label: ctx => ' ₦' + ctx.parsed.y.toLocaleString('en-NG', { minimumFractionDigits: 2 })
                }
              }
            }
          }
        });

        @if (isset($expenseByCategory) && $expenseByCategory->isNotEmpty())
          new Chart(document.getElementById('expensePieChart'), {
            type: 'doughnut',
            data: {
              labels: {!! json_encode($expenseByCategory->keys()->map(fn($k) => ucfirst($k))->values()) !!},
              datasets: [{
                data: {!! json_encode($expenseByCategory->values()) !!},
                backgroundColor: ['#fff8e1','#e8f4fd','#f3e8ff','#eaf6ee','#fff0e6','#f4f0e8'],
                borderColor:     ['#b38b16','#1565a8','#7c3aed','#246b3a','#c2540a','#6b7280'],
                borderWidth: 1.5,
              }]
            },
            options: {
              cutout: '65%',
              plugins: { legend: { display: false } },
            }
          });
        @endif
      @endif
    </script>
  </body>
</html>
