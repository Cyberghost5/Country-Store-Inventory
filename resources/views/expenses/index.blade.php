<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Expenses - Country Store</title>
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
      .total-pill { background:#fdecea; color:#b33a36; font-size:.78rem; font-weight:700; padding:5px 14px; border-radius:999px; border:1px solid #f1d3d3; }
      .cat-badge { display:inline-flex; align-items:center; font-size:.7rem; font-weight:600; padding:3px 9px; border-radius:999px; white-space:nowrap; }
      .cat-food        { background:#fff8e1; color:#b38b16; }
      .cat-transport   { background:#e8f4fd; color:#1565a8; }
      .cat-utilities   { background:#f3e8ff; color:#7c3aed; }
      .cat-supplies    { background:#eaf6ee; color:#246b3a; }
      .cat-maintenance { background:#fff0e6; color:#c2540a; }
      .cat-other       { background:#f4f0e8; color:#6b7280; }
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
            <h2>Expenses</h2>
            <p>Track and record store expenses.</p>
          </div>
          <div class="top-actions">
            @if ($user->isAdmin())
              <button class="ghost-btn" id="openTitlesModal">
                <i class="bi bi-tags"></i> Manage Types
              </button>
            @endif
            <button class="primary-btn" id="openAddModal">
              <i class="bi bi-plus-lg"></i> Add Expense
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
          <article class="stat-card danger">
            <div class="stat-top">
              <span class="mini-icon" style="background:#fdecea;"><i class="bi bi-cash-stack" style="color:#b33a36;"></i></span>
            </div>
            <h4 class="stat-value">₦{{ number_format($stats['today_total'], 2) }}</h4>
            <p class="stat-unit">today</p>
            <small class="stat-label">Total Expenses Today</small>
          </article>
          <article class="stat-card">
            <div class="stat-top">
              <span class="mini-icon" style="background:#eef2ff;"><i class="bi bi-list-check" style="color:#1d086c;"></i></span>
            </div>
            <h4 class="stat-value">{{ $stats['today_count'] }}</h4>
            <p class="stat-unit">entries</p>
            <small class="stat-label">Expense Entries Today</small>
          </article>
        </section>

        {{-- Date bar --}}
        <form method="GET" action="{{ route('expenses.index') }}" class="date-bar">
          <span class="date-label"><i class="bi bi-calendar3"></i> Viewing:</span>
          <input type="date" name="date" value="{{ $date }}" max="{{ today()->toDateString() }}"
                 class="date-input" />
          <button type="submit" class="date-apply-btn">View</button>
          @if ($date !== today()->toDateString())
            <a href="{{ route('expenses.index') }}" class="date-today-btn">Back to Today</a>
          @endif
          @if ($date !== today()->toDateString())
            <span class="total-pill">
              {{ \Carbon\Carbon::parse($date)->format('d M') }}: ₦{{ number_format($stats['date_total'], 2) }}
            </span>
          @endif
        </form>

        {{-- Expenses table --}}
        <section class="card table-card">
          <div style="padding:14px 20px 6px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
            @if ($date === today()->toDateString())
              <h3 class="section-title">Today's Expenses</h3>
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
                  <th>Description</th>
                  <th>Category</th>
                  <th>Amount</th>
                  <th>Recorded By</th>
                  <th>Notes</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @forelse ($expenses as $i => $expense)
                  <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td><span class="inv-name">{{ $expense->description }}</span></td>
                    <td>
                      <span class="cat-badge cat-{{ $expense->category }}">
                        {{ $categories[$expense->category] ?? ucfirst($expense->category) }}
                      </span>
                    </td>
                    <td><strong>₦{{ number_format($expense->amount, 2) }}</strong></td>
                    <td>{{ $expense->recorder?->name ?? '—' }}</td>
                    <td>{{ $expense->notes ?? '—' }}</td>
                    <td>
                      @if ($user->isAdmin() || $expense->recorded_by === $user->id)
                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}"
                              onsubmit="return confirm('Delete this expense?')">
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
                    <td colspan="7" class="inv-empty-row">
                      <i class="bi bi-wallet2" style="font-size:1.4rem;"></i>
                      <p>No expenses recorded for this date.</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </section>

      </main>
    </div>

    {{-- ═══ MODAL: Add Expense ═══ --}}
    <div class="inv-modal-overlay" id="addModal">
      <div class="inv-modal">
        <div class="inv-modal-head">
          <h3><i class="bi bi-cash-coin"></i> Add Expense</h3>
          <button class="inv-modal-close" onclick="closeModal('addModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form method="POST" action="{{ route('expenses.store') }}" novalidate>
          @csrf
          <div class="inv-modal-body">
            <div class="form-grid two-cols">
              <label class="span-2">
                <span>Description <span class="inv-required">*</span></span>
                <select name="description" required {{ $expenseTitles->isEmpty() ? 'disabled' : '' }}>
                  <option value="">— Select expense type —</option>
                  @foreach ($expenseTitles as $title)
                    <option value="{{ $title->name }}" {{ old('description') === $title->name ? 'selected' : '' }}>
                      {{ $title->name }}
                    </option>
                  @endforeach
                </select>
                @if ($expenseTitles->isEmpty())
                  <small style="color:#b33a36;font-size:.72rem;margin-top:4px;display:block;">
                    @if ($user->isAdmin())
                      No expense types yet.
                      <a href="#" onclick="event.preventDefault();closeModal('addModal');openModal('titlesModal')" style="color:#1d086c;font-weight:600;">Add some first →</a>
                    @else
                      No expense types available. Contact an admin.
                    @endif
                  </small>
                @endif
              </label>
              <label>
                <span>Amount (₦) <span class="inv-required">*</span></span>
                <input type="number" name="amount" step="0.01" min="0.01"
                       value="{{ old('amount') }}" placeholder="0.00" required />
              </label>
              <label>
                <span>Category <span class="inv-required">*</span></span>
                <select name="category" required>
                  @foreach ($categories as $val => $label)
                    <option value="{{ $val }}" {{ old('category') === $val ? 'selected' : '' }}>
                      {{ $label }}
                    </option>
                  @endforeach
                </select>
              </label>
              <label>
                <span>Date <span class="inv-required">*</span></span>
                <input type="date" name="expense_date" value="{{ $date }}"
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
            <button type="submit" class="primary-btn"><i class="bi bi-check-lg"></i> Save Expense</button>
          </div>
        </form>
      </div>
    </div>

    {{-- ═══ MODAL: Manage Expense Types (admin only) ═══ --}}
    @if ($user->isAdmin())
    <div class="inv-modal-overlay" id="titlesModal">
      <div class="inv-modal">
        <div class="inv-modal-head">
          <h3><i class="bi bi-tags"></i> Expense Types</h3>
          <button class="inv-modal-close" onclick="closeModal('titlesModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="inv-modal-body">
          {{-- Add new type --}}
          <form method="POST" action="{{ route('expense_titles.store') }}" style="margin-bottom:18px;">
            @csrf
            <div style="display:flex;gap:10px;">
              <input type="text" name="name" placeholder="e.g. Generator fuel" required
                     style="flex:1;min-width:0;border:1.5px solid #ddd7c8;border-radius:8px;padding:9px 12px;font-family:inherit;font-size:.85rem;color:#2e342b;" />
              <button type="submit" class="primary-btn" style="flex-shrink:0;white-space:nowrap;">
                <i class="bi bi-plus-lg"></i> Add
              </button>
            </div>
          </form>
          {{-- Existing types --}}
          @forelse ($expenseTitles as $title)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 14px;border-radius:8px;background:#f4f0e8;margin-bottom:6px;">
              <span style="font-size:.85rem;color:#2e342b;font-weight:500;">{{ $title->name }}</span>
              <form method="POST" action="{{ route('expense_titles.destroy', $title) }}"
                    onsubmit="return confirm('Remove this expense type?')">
                @csrf @method('DELETE')
                <button type="submit" class="inv-action-btn danger" title="Remove">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
          @empty
            <p style="text-align:center;color:#9a9488;font-size:.82rem;padding:20px 0;">
              <i class="bi bi-tags" style="display:block;font-size:1.6rem;margin-bottom:6px;"></i>
              No expense types yet. Add one above.
            </p>
          @endforelse
        </div>
        <div class="inv-modal-footer">
          <button type="button" class="ghost-btn" onclick="closeModal('titlesModal')">Done</button>
        </div>
      </div>
    </div>
    @endif

    @include('partials._sidebar_js')
    <script>
      document.getElementById('openAddModal').addEventListener('click', () => {
        document.getElementById('addModal').classList.add('active');
      });
      function closeModal(id) {
        document.getElementById(id).classList.remove('active');
      }
      document.getElementById('addModal').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) closeModal('addModal');
      });

      function openModal(id) {
        document.getElementById(id).classList.add('active');
      }

      @if ($user->isAdmin())
        document.getElementById('openTitlesModal').addEventListener('click', () => {
          document.getElementById('titlesModal').classList.add('active');
        });
        document.getElementById('titlesModal').addEventListener('click', (e) => {
          if (e.target === e.currentTarget) closeModal('titlesModal');
        });
      @endif

      @if ($errors->any())
        document.getElementById('addModal').classList.add('active');
      @endif

      @if (session('titles_status'))
        document.getElementById('titlesModal').classList.add('active');
      @endif
    </script>
  </body>
</html>
