<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Opening Stock - Country Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}" />
    <style>
      .os-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:14px; margin-bottom:24px; }
      .os-card { background:#fff; border-radius:14px; border:1.5px solid #e8e2d9; padding:18px 20px; display:flex; flex-direction:column; gap:10px; }
      .os-card-head { display:flex; align-items:center; justify-content:space-between; gap:8px; }
      .os-card-name { font-size:.88rem; font-weight:600; color:#2e342b; }
      .os-card-unit { font-size:.72rem; color:#9a9488; background:#f4f0e8; padding:2px 8px; border-radius:999px; }
      .os-card-price { font-size:.78rem; color:#6b7280; }
      .os-qty-wrap { display:flex; align-items:center; gap:8px; }
      .os-qty-input { flex:1; border:1.5px solid #ddd7c8; border-radius:8px; padding:9px 12px; font-family:inherit; font-size:.85rem; color:#2e342b; transition:border-color .18s; }
      .os-qty-input:focus { outline:none; border-color:#1d086c; }
      .os-qty-input[readonly], .os-notes-input[readonly] { background:#f4f0e8; color:#6b7280; cursor:default; }
      .os-save-btn { background:#1d086c; color:#fff; border:none; border-radius:8px; padding:9px 16px; font-family:inherit; font-size:.78rem; font-weight:600; cursor:pointer; white-space:nowrap; transition:background .18s; }
      .os-save-btn:hover { background:#2f1295; }
      .os-saved-badge { display:inline-flex; align-items:center; gap:4px; font-size:.72rem; font-weight:600; color:#246b3a; background:#eaf6ee; padding:3px 9px; border-radius:999px; }
      .os-notes-input { width:100%; border:1.5px solid #ddd7c8; border-radius:8px; padding:7px 10px; font-family:inherit; font-size:.78rem; color:#2e342b; resize:none; margin-top:10px; transition:border-color .18s; }
      .os-notes-input:focus { outline:none; border-color:#1d086c; }
      .history-table th, .history-table td { font-size:.8rem; }
      .os-date-bar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:18px; }
      .os-date-label { font-size:.82rem; font-weight:500; color:#4f574c; }
      .os-date-input { border:1.5px solid #ddd7c8; border-radius:8px; padding:8px 12px; font-family:inherit; font-size:.83rem; color:#2e342b; }
      .os-date-input:focus { outline:none; border-color:#1d086c; }
      .os-apply-btn { background:#ffd900; border:none; border-radius:8px; padding:8px 16px; font-family:inherit; font-size:.82rem; font-weight:600; color:#1d086c; cursor:pointer; }
      .os-today-btn { background:#f4f0e8; border:none; border-radius:8px; padding:8px 14px; font-family:inherit; font-size:.78rem; font-weight:500; color:#4f574c; cursor:pointer; text-decoration:none; display:inline-block; }
      .section-title { font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#9a9488; }
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
            <h2>Opening Stock</h2>
            <p>Record how much of each product is on the shelf at the start of each day.</p>
          </div>
          @if (!$products->isEmpty() && !$alreadySaved)
            <div class="top-actions">
              <button type="submit" form="stockForm" class="primary-btn">
                <i class="bi bi-check-lg"></i> Save All
              </button>
            </div>
          @endif
        </header>

        {{-- Flash --}}
        @if (session('status'))
          <div class="lp-success" style="margin-bottom:14px;">
            <i class="bi bi-check-circle"></i> {{ session('status') }}
          </div>
        @endif

        {{-- KPI --}}
        <section class="kpi-grid" style="margin-bottom:16px;">
          <article class="stat-card success">
            <div class="stat-top">
              <span class="mini-icon" style="background:#eaf6ee;"><i class="bi bi-check-circle" style="color:#246b3a;"></i></span>
            </div>
            <h4 class="stat-value">{{ $stats['today_recorded'] }}</h4>
            <p class="stat-unit">of {{ $stats['total_products'] }} products</p>
            <small class="stat-label">Recorded Today</small>
          </article>
          <article class="stat-card">
            <div class="stat-top">
              <span class="mini-icon"><i class="bi bi-box-seam"></i></span>
            </div>
            <h4 class="stat-value">{{ $stats['total_products'] }}</h4>
            <p class="stat-unit">products</p>
            <small class="stat-label">Total Products</small>
          </article>
        </section>

        {{-- Date selector --}}
        <form method="GET" action="{{ route('opening_stock.index') }}" class="os-date-bar">
          <span class="os-date-label"><i class="bi bi-calendar3"></i> Date:</span>
          <input type="date" name="date" class="os-date-input" value="{{ $date }}" max="{{ today()->toDateString() }}" />
          <button type="submit" class="os-apply-btn">Apply</button>
          @if ($date !== today()->toDateString())
            <a href="{{ route('opening_stock.index') }}" class="os-today-btn">Back to Today</a>
          @endif
        </form>

        @if ($products->isEmpty())
          <div class="card" style="padding:32px;text-align:center;color:#9a9488;">
            <i class="bi bi-box-seam" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
            No products found. Ask an admin to add products first.
          </div>
        @else
          <h3 class="section-title" style="margin-bottom:14px;">
            Stock for {{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}
          </h3>

          @if ($alreadySaved)
            <div class="lp-success" style="margin-bottom:14px;">
              <i class="bi bi-lock-fill"></i> Opening stock has already been recorded for this day.
            </div>
          @endif

          <form method="POST" action="{{ route('opening_stock.store') }}" id="stockForm">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}" />
            <div class="os-grid">
              @foreach ($products as $product)
                <div class="os-card">
                  <div class="os-card-head">
                    <span class="os-card-name">{{ $product->name }}</span>
                    <span class="os-card-unit">{{ ucfirst($product->unit) }}</span>
                  </div>
                  <span class="os-card-price">₦{{ number_format($product->selling_price, 2) }} / {{ $product->unit }}</span>

                  @if ($product->qty_for_date !== null)
                    <span class="os-saved-badge">
                      <i class="bi bi-check-circle-fill"></i>
                      {{ number_format($product->qty_for_date) }} recorded
                    </span>
                  @endif

                  <div class="os-qty-wrap">
                    <input type="number" name="stocks[{{ $product->id }}][quantity]" class="os-qty-input"
                           placeholder="Qty"
                           value="{{ $product->qty_for_date ?? '' }}"
                           min="0"
                           {{ $alreadySaved ? 'readonly' : '' }} />
                  </div>
                  <textarea name="stocks[{{ $product->id }}][notes]" class="os-notes-input" rows="1"
                            placeholder="Notes (optional)"
                            {{ $alreadySaved ? 'readonly' : '' }}>{{ $product->notes_for_date ?? '' }}</textarea>
                </div>
              @endforeach
            </div>
            @if (!$alreadySaved)
              <div style="display:flex;justify-content:flex-end;margin-top:16px;margin-bottom:8px;">
                <button type="submit" class="primary-btn" style="padding:11px 32px;">
                  <i class="bi bi-check-lg"></i> Save All
                </button>
              </div>
            @endif
          </form>
        @endif

        {{-- History --}}
        @if ($history->isNotEmpty())
          <section class="card table-card" style="margin-top:8px;">
            <div style="padding:16px 20px 8px;display:flex;align-items:center;justify-content:space-between;">
              <h3 style="font-size:.95rem;font-weight:600;color:#2e342b;margin:0;">Recent Entries</h3>
            </div>
            <div class="table-scroll">
              <table class="inv-table history-table">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Recorded By</th>
                    <th>Notes</th>
                    @if ($user->isAdmin())
                      <th></th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                  @foreach ($history as $entry)
                    <tr>
                      <td>{{ $entry->date->format('d M Y') }}</td>
                      <td><span class="inv-name">{{ $entry->product->name ?? '—' }}</span></td>
                      <td>{{ number_format($entry->quantity) }} {{ $entry->product?->unit }}</td>
                      <td>{{ $entry->recorder?->name ?? '—' }}</td>
                      <td>{{ $entry->notes ?? '—' }}</td>
                      @if ($user->isAdmin())
                        <td>
                          <form method="POST" action="{{ route('opening_stock.destroy', $entry) }}"
                                onsubmit="return confirm('Delete this entry?')">
                            @csrf @method('DELETE')
                            <button class="inv-action-btn danger" type="submit" title="Delete">
                              <i class="bi bi-trash"></i>
                            </button>
                          </form>
                        </td>
                      @endif
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </section>
        @endif

      </main>
    </div>

    @include('partials._sidebar_js')
  </body>
</html>
