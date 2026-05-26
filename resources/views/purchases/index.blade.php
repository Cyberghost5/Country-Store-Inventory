<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Purchases - Country Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}" />
    <style>
      .section-title { font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#9a9488; }
      .date-bar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:18px; }
      .date-label { font-size:.82rem; font-weight:500; color:#4f574c; }
      .date-input { border:1.5px solid #ddd7c8; border-radius:8px; padding:8px 12px; font-family:inherit; font-size:.83rem; color:#2e342b; }
      .date-input:focus { outline:none; border-color:#1d086c; }
      .date-apply-btn { background:#ffd900; border:none; border-radius:8px; padding:8px 16px; font-family:inherit; font-size:.82rem; font-weight:600; color:#1d086c; cursor:pointer; }
      .date-today-btn { background:#f4f0e8; border:none; border-radius:8px; padding:8px 14px; font-family:inherit; font-size:.78rem; font-weight:500; color:#4f574c; cursor:pointer; text-decoration:none; display:inline-block; }
      .total-pill { background:#eef2ff; color:#1d086c; font-size:.78rem; font-weight:700; padding:5px 14px; border-radius:999px; border:1px solid #c7d2fe; }
      .supplier-badge { display:inline-flex; align-items:center; gap:4px; font-size:.72rem; font-weight:600; background:#f4f0e8; color:#4f574c; padding:3px 9px; border-radius:999px; }

      /* Unit cost input row */
      .cost-preview { font-size:.78rem; font-weight:600; color:#1d086c; background:#eef2ff; border:1.5px solid #c7d2fe; border-radius:8px; padding:9px 12px; }
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
            <h2>Purchases</h2>
            <p>Record stock purchases from suppliers.</p>
          </div>
          <div class="top-actions">
            @if ($user->isAdmin())
              <button class="ghost-btn" id="openSuppliersModal">
                <i class="bi bi-truck"></i> Manage Suppliers
              </button>
            @endif
            <button class="primary-btn" id="openAddModal">
              <i class="bi bi-plus-lg"></i> Add Purchase
            </button>
          </div>
        </header>

        {{-- Flash --}}
        @if (session('status'))
          <div class="lp-success" style="margin-bottom:14px;">
            <i class="bi bi-check-circle"></i> {{ session('status') }}
          </div>
        @endif
        @if ($errors->any())
          <div class="lp-error" style="margin-bottom:14px;">
            <i class="bi bi-exclamation-circle"></i> {{ $errors->first() }}
          </div>
        @endif

        {{-- KPI cards --}}
        <section class="kpi-grid" style="margin-bottom:20px;">
          <article class="stat-card">
            <div class="stat-top">
              <span class="mini-icon" style="background:#eef2ff;"><i class="bi bi-box-arrow-in-down" style="color:#1d086c;"></i></span>
            </div>
            <h4 class="stat-value">₦{{ number_format($stats['today_total'], 2) }}</h4>
            <p class="stat-unit">today</p>
            <small class="stat-label">Total Purchases Today</small>
          </article>
          <article class="stat-card">
            <div class="stat-top">
              <span class="mini-icon" style="background:#f4f0e8;"><i class="bi bi-list-check" style="color:#4f574c;"></i></span>
            </div>
            <h4 class="stat-value">{{ $stats['today_count'] }}</h4>
            <p class="stat-unit">entries</p>
            <small class="stat-label">Purchase Entries Today</small>
          </article>
        </section>

        {{-- Date bar --}}
        <form method="GET" action="{{ route('purchases.index') }}" class="date-bar">
          <span class="date-label"><i class="bi bi-calendar3"></i> Viewing:</span>
          <input type="date" name="date" value="{{ $date }}" max="{{ today()->toDateString() }}"
                 class="date-input" />
          <button type="submit" class="date-apply-btn">View</button>
          @if ($date !== today()->toDateString())
            <a href="{{ route('purchases.index') }}" class="date-today-btn">Back to Today</a>
          @endif
          @if ($date !== today()->toDateString())
            <span class="total-pill">
              {{ \Carbon\Carbon::parse($date)->format('d M') }}: ₦{{ number_format($stats['date_total'], 2) }}
            </span>
          @endif
        </form>

        {{-- Purchases table --}}
        <section class="card table-card">
          <div style="padding:14px 20px 6px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
            @if ($date === today()->toDateString())
              <h3 class="section-title">Today's Purchases</h3>
            @else
              <h3 class="section-title">{{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}</h3>
            @endif
            <span class="total-pill">Total: ₦{{ number_format($stats['date_total'], 2) }}</span>
          </div>
          <div class="table-scroll">
            <table class="inv-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Product</th>
                  <th>Qty</th>
                  <th>Unit Cost</th>
                  <th>Total</th>
                  <th>Supplier</th>
                  <th>Recorded By</th>
                  <th>Notes</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @forelse ($purchases as $i => $purchase)
                  <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td><span class="inv-name">{{ $purchase->product?->name ?? '—' }}</span></td>
                    <td>{{ number_format($purchase->quantity) }} {{ $purchase->product?->unit }}</td>
                    <td>₦{{ number_format($purchase->unit_cost, 2) }}</td>
                    <td><strong>₦{{ number_format($purchase->total_cost, 2) }}</strong></td>
                    <td>
                      <span class="supplier-badge">
                        <i class="bi bi-truck"></i>
                        {{ $purchase->supplier?->name ?? '—' }}
                      </span>
                    </td>
                    <td>{{ $purchase->recorder?->name ?? '—' }}</td>
                    <td>{{ $purchase->notes ?? '—' }}</td>
                    <td>
                      @if ($user->isAdmin() || $purchase->recorded_by === $user->id)
                        <form method="POST" action="{{ route('purchases.destroy', $purchase) }}"
                              onsubmit="return confirm('Delete this purchase record?')">
                          @csrf @method('DELETE')
                          <button class="inv-action-btn danger" type="submit" title="Delete">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" class="inv-empty-row">
                      <i class="bi bi-box-seam" style="font-size:1.4rem;"></i>
                      <p>No purchases recorded for this date.</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </section>

      </main>
    </div>

    {{-- ═══ MODAL: Add Purchase ═══ --}}
    <div class="inv-modal-overlay" id="addModal">
      <div class="inv-modal">
        <div class="inv-modal-head">
          <h3><i class="bi bi-box-arrow-in-down"></i> Add Purchase</h3>
          <button class="inv-modal-close" onclick="closeModal('addModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form method="POST" action="{{ route('purchases.store') }}" novalidate id="purchaseForm">
          @csrf
          <div class="inv-modal-body">
            <div class="form-grid two-cols">
              <label class="span-2">
                <span>Product <span class="inv-required">*</span></span>
                <select name="product_id" required>
                  <option value="">— Select product —</option>
                  @foreach ($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                      {{ $product->name }} ({{ ucfirst($product->unit) }})
                    </option>
                  @endforeach
                </select>
              </label>
              <label>
                <span>Quantity <span class="inv-required">*</span></span>
                <input type="number" name="quantity" id="qty" min="1"
                       value="{{ old('quantity') }}" placeholder="0" required />
              </label>
              <label>
                <span>Unit Cost (₦) <span class="inv-required">*</span></span>
                <input type="number" name="unit_cost" id="unitCost" step="0.01" min="0.01"
                       value="{{ old('unit_cost') }}" placeholder="0.00" required />
              </label>
              <label class="span-2">
                <span>Total Cost</span>
                <div class="cost-preview" id="totalPreview">₦ 0.00</div>
              </label>
              <label class="span-2">
                <span>Supplier <span class="inv-required">*</span></span>
                <select name="supplier_id" required {{ $suppliers->isEmpty() ? 'disabled' : '' }}>
                  <option value="">— Select supplier —</option>
                  @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                      {{ $supplier->name }}
                    </option>
                  @endforeach
                </select>
                @if ($suppliers->isEmpty())
                  <small style="color:#b33a36;font-size:.72rem;margin-top:4px;display:block;">
                    @if ($user->isAdmin())
                      No suppliers yet.
                      <a href="#" onclick="event.preventDefault();closeModal('addModal');openModal('suppliersModal')" style="color:#1d086c;font-weight:600;">Add one first →</a>
                    @else
                      No suppliers available. Contact an admin.
                    @endif
                  </small>
                @endif
              </label>
              <label>
                <span>Purchase Date <span class="inv-required">*</span></span>
                <input type="date" name="purchase_date" value="{{ $date }}"
                       max="{{ today()->toDateString() }}" required />
              </label>
              <label>
                <span>Notes</span>
                <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Optional…" />
              </label>
            </div>
          </div>
          <div class="inv-modal-footer">
            <button type="button" class="ghost-btn" onclick="closeModal('addModal')">Cancel</button>
            <button type="submit" class="primary-btn"><i class="bi bi-check-lg"></i> Save Purchase</button>
          </div>
        </form>
      </div>
    </div>

    {{-- ═══ MODAL: Manage Suppliers (admin only) ═══ --}}
    @if ($user->isAdmin())
    <div class="inv-modal-overlay" id="suppliersModal">
      <div class="inv-modal">
        <div class="inv-modal-head">
          <h3><i class="bi bi-truck"></i> Suppliers</h3>
          <button class="inv-modal-close" onclick="closeModal('suppliersModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="inv-modal-body">
          {{-- Add new supplier --}}
          <form method="POST" action="{{ route('suppliers.store') }}" style="margin-bottom:18px;">
            @csrf
            <div style="display:flex;gap:10px;">
              <input type="text" name="name" placeholder="e.g. Farm Direct Ltd" required
                     style="flex:1;min-width:0;border:1.5px solid #ddd7c8;border-radius:8px;padding:9px 12px;font-family:inherit;font-size:.85rem;color:#2e342b;" />
              <button type="submit" class="primary-btn" style="flex-shrink:0;white-space:nowrap;">
                <i class="bi bi-plus-lg"></i> Add
              </button>
            </div>
          </form>
          {{-- Existing suppliers --}}
          @forelse ($suppliers as $supplier)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 14px;border-radius:8px;background:#f4f0e8;margin-bottom:6px;">
              <span style="font-size:.85rem;color:#2e342b;font-weight:500;">
                <i class="bi bi-truck" style="color:#4f574c;margin-right:6px;"></i>{{ $supplier->name }}
              </span>
              <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}"
                    onsubmit="return confirm('Remove this supplier?')">
                @csrf @method('DELETE')
                <button type="submit" class="inv-action-btn danger" title="Remove">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
          @empty
            <p style="text-align:center;color:#9a9488;font-size:.82rem;padding:20px 0;">
              <i class="bi bi-truck" style="display:block;font-size:1.6rem;margin-bottom:6px;"></i>
              No suppliers yet. Add one above.
            </p>
          @endforelse
        </div>
        <div class="inv-modal-footer">
          <button type="button" class="ghost-btn" onclick="closeModal('suppliersModal')">Done</button>
        </div>
      </div>
    </div>
    @endif

    @include('partials._sidebar_js')
    <script>
      /* ── Modal helpers ── */
      function openModal(id)  { document.getElementById(id).classList.add('active'); }
      function closeModal(id) { document.getElementById(id).classList.remove('active'); }

      document.getElementById('openAddModal').addEventListener('click', () => openModal('addModal'));
      document.getElementById('addModal').addEventListener('click', e => {
        if (e.target === e.currentTarget) closeModal('addModal');
      });

      @if ($user->isAdmin())
        document.getElementById('openSuppliersModal').addEventListener('click', () => openModal('suppliersModal'));
        document.getElementById('suppliersModal').addEventListener('click', e => {
          if (e.target === e.currentTarget) closeModal('suppliersModal');
        });
      @endif

      /* ── Auto-calculate total cost ── */
      const qtyEl      = document.getElementById('qty');
      const costEl     = document.getElementById('unitCost');
      const previewEl  = document.getElementById('totalPreview');

      function updateTotal() {
        const qty  = parseFloat(qtyEl.value)  || 0;
        const cost = parseFloat(costEl.value) || 0;
        const total = qty * cost;
        previewEl.textContent = '₦ ' + total.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      }

      qtyEl.addEventListener('input', updateTotal);
      costEl.addEventListener('input', updateTotal);

      /* ── Re-open add modal on validation error ── */
      @if ($errors->any())
        openModal('addModal');
      @endif

      /* ── Re-open suppliers modal on supplier action ── */
      @if (session('suppliers_status'))
        openModal('suppliersModal');
      @endif
    </script>
  </body>
</html>
