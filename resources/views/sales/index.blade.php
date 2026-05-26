<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sales - Country Store</title>
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
      .total-pill { background:#eaf6ee; color:#246b3a; font-size:.78rem; font-weight:700; padding:5px 14px; border-radius:999px; border:1px solid #cdecd8; }
      /* Payment method */
      .pay-badge { display:inline-block; font-size:.67rem; font-weight:700; padding:2px 9px; border-radius:999px; text-transform:uppercase; letter-spacing:.04em; }
      .pay-badge.cash { background:#fff8e0; color:#a07b00; }
      .pay-badge.transfer { background:#eef2ff; color:#1d086c; }
      .pay-badge.split { background:#eaf6f2; color:#1a6b5a; }
      .pay-split-detail { font-size:.7rem; color:#4f574c; margin-top:2px; line-height:1.4; }
      .pay-toggle { display:flex; gap:10px; margin-top:6px; }
      .pay-toggle-btn { flex:1; border:1.5px solid #ddd7c8; border-radius:10px; padding:12px 10px; text-align:center; cursor:pointer; font-size:.82rem; font-weight:600; color:#4f574c; background:#fff; transition:border-color .15s,background .15s,color .15s; user-select:none; }
      .pay-toggle-btn.active { border-color:#1d086c; background:#eef2ff; color:#1d086c; }
      .pay-toggle-btn input[type=checkbox] { display:none; }
      .pay-toggle-icon { font-size:1.15rem; display:block; margin-bottom:4px; }
      .pay-amount-row { margin-top:10px; }
      .pay-amount-row small { font-size:.72rem; color:#9a9488; font-weight:600; text-transform:uppercase; display:block; margin-bottom:4px; }
      .pay-amount-row input { width:100%; border:1.5px solid #ddd7c8; border-radius:8px; padding:8px 12px; font-family:inherit; font-size:.88rem; color:#2e342b; box-sizing:border-box; }
      .pay-amount-row input:focus { outline:none; border-color:#1d086c; }
      .pay-amount-row input[readonly] { background:#f4f0e8; color:#9a9488; cursor:not-allowed; }
      .pay-amounts-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
      .pay-balance { font-size:.78rem; font-weight:600; padding:8px 14px; border-radius:8px; margin-top:10px; text-align:center; display:none; }
      .pay-balance.ok { background:#eaf6ee; color:#246b3a; }
      .pay-balance.bad { background:#fdecea; color:#b33a36; }
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
            <h2>Sales</h2>
            <p>Record and track daily product sales.</p>
          </div>
          <div class="top-actions">
            <button class="primary-btn" id="openAddModal">
              <i class="bi bi-plus-lg"></i> Record Sale
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
          <article class="stat-card success">
            <div class="stat-top">
              <span class="mini-icon" style="background:#eaf6ee;"><i class="bi bi-bag-check-fill" style="color:#246b3a;"></i></span>
            </div>
            <h4 class="stat-value">₦{{ number_format($stats['today_total'], 2) }}</h4>
            <p class="stat-unit">today</p>
            <small class="stat-label">Total Sales Today</small>
          </article>
          <article class="stat-card">
            <div class="stat-top">
              <span class="mini-icon" style="background:#eef2ff;"><i class="bi bi-receipt" style="color:#1d086c;"></i></span>
            </div>
            <h4 class="stat-value">{{ $stats['today_count'] }}</h4>
            <p class="stat-unit">entries</p>
            <small class="stat-label">Transactions Today</small>
          </article>
        </section>

        {{-- Date bar --}}
        <form method="GET" action="{{ route('sales.index') }}" class="date-bar">
          <span class="date-label"><i class="bi bi-calendar3"></i> Viewing:</span>
          <input type="date" name="date" value="{{ $date }}" max="{{ today()->toDateString() }}"
                 class="date-input" />
          <button type="submit" class="date-apply-btn">View</button>
          @if ($date !== today()->toDateString())
            <a href="{{ route('sales.index') }}" class="date-today-btn">Back to Today</a>
          @endif
          @if ($date !== today()->toDateString())
            <span class="total-pill">
              {{ \Carbon\Carbon::parse($date)->format('d M') }}: ₦{{ number_format($stats['date_total'], 2) }}
            </span>
          @endif
        </form>

        {{-- Sales table --}}
        <section class="card table-card">
          @if ($date === today()->toDateString())
            <div style="padding:14px 20px 6px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
              <h3 class="section-title">Today's Sales</h3>
              <span class="total-pill">Total: ₦{{ number_format($stats['date_total'], 2) }}</span>
            </div>
          @else
            <div style="padding:14px 20px 6px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
              <h3 class="section-title">{{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}</h3>
              <span class="total-pill">Total: ₦{{ number_format($stats['date_total'], 2) }}</span>
            </div>
          @endif
          <div class="table-scroll">
            <table class="inv-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Product</th>
                  <th>Qty</th>
                  <th>Unit Price</th>
                  <th>Total</th>
                  <th>Payment</th>
                  <th>Sold By</th>
                  <th>Notes</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @forelse ($sales as $i => $sale)
                  <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td><span class="inv-name">{{ $sale->product?->name ?? '—' }}</span></td>
                    <td>{{ number_format($sale->quantity) }} {{ $sale->product?->unit }}</td>
                    <td>₦{{ number_format($sale->unit_price, 2) }}</td>
                    <td><strong>₦{{ number_format($sale->total_amount, 2) }}</strong></td>
                    <td>
                      @php $cash = (float)$sale->cash_amount; $xfer = (float)$sale->transfer_amount; @endphp
                      @if ($cash > 0 && $xfer > 0)
                        <span class="pay-badge split">Split</span>
                        <div class="pay-split-detail">₦{{ number_format($cash,2) }} cash<br>₦{{ number_format($xfer,2) }} transfer</div>
                      @elseif ($xfer > 0)
                        <span class="pay-badge transfer">Transfer</span>
                      @elseif ($cash > 0)
                        <span class="pay-badge cash">Cash</span>
                      @else
                        <span>—</span>
                      @endif
                    </td>
                    <td>{{ $sale->seller?->name ?? '—' }}</td>
                    <td>{{ $sale->notes ?? '—' }}</td>
                    <td>
                      @if ($user->isAdmin() || $sale->sold_by === $user->id)
                        <form method="POST" action="{{ route('sales.destroy', $sale) }}"
                              onsubmit="return confirm('Delete this sale entry?')">
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
                    <td colspan="8" class="inv-empty-row">
                      <i class="bi bi-bag-x" style="font-size:1.4rem;"></i>
                      <p>No sales recorded for this date.{{ $user->role === 'staff' ? '' : '' }}</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </section>

      </main>
    </div>

    {{-- ═══ MODAL: Record Sale ═══ --}}
    <div class="inv-modal-overlay" id="addModal">
      <div class="inv-modal">
        <div class="inv-modal-head">
          <h3><i class="bi bi-bag-plus"></i> Record Sale</h3>
          <button class="inv-modal-close" onclick="closeModal('addModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form method="POST" action="{{ route('sales.store') }}" novalidate id="saleForm">
          @csrf
          <div class="inv-modal-body">
            <div class="form-grid two-cols">
              <label class="span-2">
                <span>Product <span class="inv-required">*</span></span>
                <select name="product_id" id="sale_product" required onchange="updatePrice(this)">
                  <option value="">— Select product —</option>
                  @foreach ($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}">
                      {{ $product->name }} ({{ ucfirst($product->unit) }}) — ₦{{ number_format($product->selling_price, 2) }}
                    </option>
                  @endforeach
                </select>
              </label>
              <label>
                <span>Quantity <span class="inv-required">*</span></span>
                <input type="number" name="quantity" id="sale_qty" min="1" value="1" required
                       oninput="updateTotal()" />
              </label>
              <label>
                <span>Date <span class="inv-required">*</span></span>
                <input type="date" name="sale_date" value="{{ $date }}"
                       max="{{ today()->toDateString() }}" required />
              </label>
              <label class="span-2" id="totalDisplay" style="background:#f4f0e8;border-radius:8px;padding:10px 14px;display:none;">
                <span style="font-size:.75rem;color:#9a9488;font-weight:600;text-transform:uppercase;">Total Amount</span>
                <strong id="totalAmount" style="font-size:1.15rem;color:#1d086c;display:block;margin-top:2px;">₦0.00</strong>
              </label>

              {{-- Payment method --}}
              <div class="span-2" id="paymentSection" style="display:none;">
                <span style="font-size:.82rem;font-weight:500;color:#2e342b;display:block;">Payment Method <span class="inv-required">*</span></span>
                <div class="pay-toggle">
                  <label class="pay-toggle-btn" id="cashToggle">
                    <input type="checkbox" id="pay_cash" onchange="updatePayment()">
                    <i class="bi bi-cash-stack pay-toggle-icon"></i> Cash
                  </label>
                  <label class="pay-toggle-btn" id="transferToggle">
                    <input type="checkbox" id="pay_transfer" onchange="updatePayment()">
                    <i class="bi bi-bank pay-toggle-icon"></i> Bank Transfer
                  </label>
                </div>
                <div class="pay-amounts-grid" id="payAmountGrid" style="display:none;">
                  <div class="pay-amount-row" id="cashAmountWrap" style="display:none;">
                    <small>Cash Amount (₦)</small>
                    <input type="number" name="cash_amount" id="cashAmountInput" step="0.01" min="0" placeholder="0.00" oninput="checkBalance()" />
                  </div>
                  <div class="pay-amount-row" id="transferAmountWrap" style="display:none;">
                    <small>Transfer Amount (₦)</small>
                    <input type="number" name="transfer_amount" id="transferAmountInput" step="0.01" min="0" placeholder="0.00" oninput="checkBalance()" />
                  </div>
                </div>
                <div class="pay-balance" id="payBalance"></div>
              </div>

              <label class="span-2">
                <span>Notes</span>
                <input type="text" name="notes" placeholder="Optional note…" />
              </label>
            </div>
          </div>
          <div class="inv-modal-footer">
            <button type="button" class="ghost-btn" onclick="closeModal('addModal')">Cancel</button>
            <button type="submit" class="primary-btn"><i class="bi bi-check-lg"></i> Save Sale</button>
          </div>
        </form>
      </div>
    </div>

    @include('partials._sidebar_js')
    <script>
      // Modal open/close
      document.getElementById('openAddModal').addEventListener('click', () => {
        document.getElementById('addModal').classList.add('active');
      });
      function closeModal(id) {
        document.getElementById(id).classList.remove('active');
      }
      document.getElementById('addModal').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) closeModal('addModal');
      });

      // ── Helpers ──────────────────────────────────────────
      function getCurrentTotal() {
        const select = document.getElementById('sale_product');
        const qty    = parseInt(document.getElementById('sale_qty').value) || 0;
        const price  = parseFloat(select.options[select.selectedIndex]?.dataset.price) || 0;
        return price * qty;
      }

      function fmtNGN(n) {
        return '₦' + n.toLocaleString('en-NG', {minimumFractionDigits:2, maximumFractionDigits:2});
      }

      // ── Total display ─────────────────────────────────────
      function updatePrice(select) {
        updateTotal();
        const hasProduct = !!select.value;
        document.getElementById('totalDisplay').style.display    = hasProduct ? 'block' : 'none';
        document.getElementById('paymentSection').style.display  = hasProduct ? 'block' : 'none';
        if (!hasProduct) resetPayment();
      }

      function updateTotal() {
        const total = getCurrentTotal();
        document.getElementById('totalAmount').textContent = fmtNGN(total);
        // If payment already selected, refresh the auto-filled amounts
        const cashChecked     = document.getElementById('pay_cash').checked;
        const transferChecked = document.getElementById('pay_transfer').checked;
        if ((cashChecked || transferChecked) && !(cashChecked && transferChecked)) {
          autoFillSingle();
        } else if (cashChecked && transferChecked) {
          checkBalance();
        }
      }

      // ── Payment method logic ──────────────────────────────
      function resetPayment() {
        document.getElementById('pay_cash').checked     = false;
        document.getElementById('pay_transfer').checked = false;
        document.getElementById('cashToggle').classList.remove('active');
        document.getElementById('transferToggle').classList.remove('active');
        document.getElementById('payAmountGrid').style.display  = 'none';
        document.getElementById('cashAmountWrap').style.display = 'none';
        document.getElementById('transferAmountWrap').style.display = 'none';
        document.getElementById('payBalance').style.display = 'none';
        document.getElementById('cashAmountInput').value     = '';
        document.getElementById('transferAmountInput').value = '';
      }

      function updatePayment() {
        const cashChecked     = document.getElementById('pay_cash').checked;
        const transferChecked = document.getElementById('pay_transfer').checked;

        document.getElementById('cashToggle').classList.toggle('active', cashChecked);
        document.getElementById('transferToggle').classList.toggle('active', transferChecked);

        const cashWrap     = document.getElementById('cashAmountWrap');
        const transferWrap = document.getElementById('transferAmountWrap');
        const amountGrid   = document.getElementById('payAmountGrid');
        const balance      = document.getElementById('payBalance');

        if (!cashChecked && !transferChecked) {
          amountGrid.style.display    = 'none';
          balance.style.display       = 'none';
          document.getElementById('cashAmountInput').value     = '';
          document.getElementById('transferAmountInput').value = '';
          return;
        }

        amountGrid.style.display     = 'grid';
        cashWrap.style.display       = cashChecked     ? 'block' : 'none';
        transferWrap.style.display   = transferChecked ? 'block' : 'none';

        if (cashChecked && transferChecked) {
          // Both: unlock for manual entry, clear any auto-filled values
          const cashIn   = document.getElementById('cashAmountInput');
          const xferIn   = document.getElementById('transferAmountInput');
          cashIn.readOnly  = false;
          xferIn.readOnly  = false;
          // If previously auto-filled from single, clear so user fills in
          const total = getCurrentTotal();
          if (parseFloat(cashIn.value) === total)  cashIn.value  = '';
          if (parseFloat(xferIn.value) === total)  xferIn.value  = '';
          checkBalance();
        } else {
          autoFillSingle();
        }
      }

      function autoFillSingle() {
        const total         = getCurrentTotal();
        const cashChecked   = document.getElementById('pay_cash').checked;
        const cashIn        = document.getElementById('cashAmountInput');
        const xferIn        = document.getElementById('transferAmountInput');

        if (cashChecked) {
          cashIn.value    = total.toFixed(2);
          cashIn.readOnly = true;
          xferIn.value    = '0';
        } else {
          xferIn.value    = total.toFixed(2);
          xferIn.readOnly = true;
          cashIn.value    = '0';
        }
        const balance = document.getElementById('payBalance');
        balance.className   = 'pay-balance ok';
        balance.textContent = '✓ Full amount covered';
        balance.style.display = 'block';
      }

      function checkBalance() {
        const total    = getCurrentTotal();
        const cash     = parseFloat(document.getElementById('cashAmountInput').value)    || 0;
        const transfer = parseFloat(document.getElementById('transferAmountInput').value) || 0;
        const sum      = cash + transfer;
        const diff     = total - sum;
        const balance  = document.getElementById('payBalance');
        balance.style.display = 'block';
        if (Math.abs(diff) < 0.01) {
          balance.className   = 'pay-balance ok';
          balance.textContent = '✓ Amounts tally — ' + fmtNGN(total) + ' covered';
        } else if (diff > 0) {
          balance.className   = 'pay-balance bad';
          balance.textContent = fmtNGN(diff) + ' still remaining';
        } else {
          balance.className   = 'pay-balance bad';
          balance.textContent = fmtNGN(Math.abs(diff)) + ' over the total';
        }
      }

      // ── Form submit guard ─────────────────────────────────
      document.getElementById('saleForm').addEventListener('submit', function(e) {
        const cashChecked     = document.getElementById('pay_cash').checked;
        const transferChecked = document.getElementById('pay_transfer').checked;

        if (!cashChecked && !transferChecked) {
          e.preventDefault();
          alert('Please select at least one payment method.');
          return;
        }

        if (cashChecked && transferChecked) {
          const total    = getCurrentTotal();
          const cash     = parseFloat(document.getElementById('cashAmountInput').value)    || 0;
          const transfer = parseFloat(document.getElementById('transferAmountInput').value) || 0;
          if (Math.abs((cash + transfer) - total) >= 0.01) {
            e.preventDefault();
            alert('Cash + Transfer amounts must equal the total (' + fmtNGN(total) + ').');
            return;
          }
        }
      });

      @if ($errors->any())
        document.getElementById('addModal').classList.add('active');
      @endif
    </script>
  </body>
</html>
