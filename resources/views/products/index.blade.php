<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Products - Country Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}" />
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
            <h2>Products</h2>
            <p>Manage all store products and their unit prices.</p>
          </div>
          @if ($user->isAdmin())
            <div class="top-actions">
              <button class="primary-btn" id="openAddModal">
                <i class="bi bi-plus-lg"></i> Add Product
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
          <article class="stat-card">
            <div class="stat-top">
              <span class="mini-icon"><i class="bi bi-box-seam"></i></span>
              <span class="trend-pill">All</span>
            </div>
            <h4 class="stat-value">{{ $stats['total_products'] }}</h4>
            <p class="stat-unit">products</p>
            <small class="stat-label">Total Products</small>
          </article>
        </section>

        {{-- Search + count bar --}}
        <section class="card inv-filter-bar">
          <form method="GET" action="{{ route('products.index') }}" class="inv-filters">
            <label class="search-wrap inv-search" for="prod_search">
              <i class="bi bi-search search-icon"></i>
              <input id="prod_search" type="search" name="search"
                     placeholder="Search product name…"
                     value="{{ request('search') }}" />
            </label>
            <button type="submit" class="ghost-btn">Search</button>
            @if (request('search'))
              <a href="{{ route('products.index') }}" class="ghost-btn">Clear</a>
            @endif
          </form>
          <span class="inv-count">
            {{ $products->count() }} product{{ $products->count() !== 1 ? 's' : '' }}
          </span>
        </section>

        {{-- Products table --}}
        <section class="card table-card">
          <div class="table-scroll">
            <table class="inv-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Product Name</th>
                  <th>Unit</th>
                  <th>Price (₦)</th>
                  <th>Category</th>
                  @if ($user->isAdmin())
                    <th>Actions</th>
                  @endif
                </tr>
              </thead>
              <tbody>
                @forelse ($products as $i => $product)
                  <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td><span class="inv-name">{{ $product->name }}</span></td>
                    <td>{{ ucfirst($product->unit) }}</td>
                    <td>₦{{ number_format($product->selling_price, 2) }}</td>
                    <td>{{ $product->category ?? '—' }}</td>
                    @if ($user->isAdmin())
                      <td>
                        <div class="inv-actions">
                          <button class="inv-action-btn" title="Edit"
                                  onclick="openEdit({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->unit }}', {{ $product->selling_price }}, '{{ addslashes($product->category ?? '') }}', '{{ addslashes($product->notes ?? '') }}')">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <button class="inv-action-btn danger" title="Delete"
                                  onclick="openDelete({{ $product->id }}, '{{ addslashes($product->name) }}')">
                            <i class="bi bi-trash"></i>
                          </button>
                        </div>
                      </td>
                    @endif
                  </tr>
                @empty
                  <tr>
                    <td colspan="{{ $user->isAdmin() ? 6 : 5 }}" class="inv-empty-row">
                      <i class="bi bi-inbox" style="font-size:1.4rem;"></i>
                      <p>No products found.{{ $user->isAdmin() ? ' Add your first product using the button above.' : '' }}</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </section>

      </main>
    </div>

    {{-- ═══ MODAL: Add Product ═══ --}}
    @if ($user->isAdmin())
    <div class="inv-modal-overlay" id="addModal">
      <div class="inv-modal">
        <div class="inv-modal-head">
          <h3><i class="bi bi-plus-circle"></i> Add Product</h3>
          <button class="inv-modal-close" onclick="closeModal('addModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form method="POST" action="{{ route('products.store') }}" novalidate>
          @csrf
          <div class="inv-modal-body">
            <div class="form-grid two-cols">
              <label class="span-2">
                <span>Product Name <span class="inv-required">*</span></span>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Rice (50kg)" />
              </label>
              <label>
                <span>Unit <span class="inv-required">*</span></span>
                <select name="unit" required>
                  @foreach(['piece'=>'Piece','carton'=>'Carton','pack'=>'Pack','kg'=>'Kg','litre'=>'Litre','dozen'=>'Dozen'] as $val => $label)
                    <option value="{{ $val }}" {{ old('unit') === $val ? 'selected' : '' }}>{{ $label }}</option>
                  @endforeach
                </select>
              </label>
              <label>
                <span>Selling Price (₦) <span class="inv-required">*</span></span>
                <input type="number" name="selling_price" step="0.01" min="0"
                       value="{{ old('selling_price') }}" placeholder="0.00" required />
              </label>
              <label class="span-2">
                <span>Category</span>
                <input type="text" name="category" value="{{ old('category') }}" placeholder="e.g. Beverages, Groceries…" />
              </label>
              <label class="span-2">
                <span>Notes</span>
                <textarea name="notes" rows="2" placeholder="Optional notes…">{{ old('notes') }}</textarea>
              </label>
            </div>
          </div>
          <div class="inv-modal-footer">
            <button type="button" class="ghost-btn" onclick="closeModal('addModal')">Cancel</button>
            <button type="submit" class="primary-btn">Save Product</button>
          </div>
        </form>
      </div>
    </div>

    {{-- ═══ MODAL: Edit Product ═══ --}}
    <div class="inv-modal-overlay" id="editModal">
      <div class="inv-modal">
        <div class="inv-modal-head">
          <h3><i class="bi bi-pencil-square"></i> Edit Product</h3>
          <button class="inv-modal-close" onclick="closeModal('editModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form method="POST" id="editForm" novalidate>
          @csrf
          @method('PUT')
          <div class="inv-modal-body">
            <div class="form-grid two-cols">
              <label class="span-2">
                <span>Product Name <span class="inv-required">*</span></span>
                <input type="text" name="name" id="editName" required />
              </label>
              <label>
                <span>Unit <span class="inv-required">*</span></span>
                <select name="unit" id="editUnit" required>
                  @foreach(['piece'=>'Piece','carton'=>'Carton','pack'=>'Pack','kg'=>'Kg','litre'=>'Litre','dozen'=>'Dozen'] as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                  @endforeach
                </select>
              </label>
              <label>
                <span>Selling Price (₦) <span class="inv-required">*</span></span>
                <input type="number" name="selling_price" id="editPrice" step="0.01" min="0" required />
              </label>
              <label class="span-2">
                <span>Category</span>
                <input type="text" name="category" id="editCategory" />
              </label>
              <label class="span-2">
                <span>Notes</span>
                <textarea name="notes" id="editNotes" rows="2"></textarea>
              </label>
            </div>
          </div>
          <div class="inv-modal-footer">
            <button type="button" class="ghost-btn" onclick="closeModal('editModal')">Cancel</button>
            <button type="submit" class="primary-btn">Update Product</button>
          </div>
        </form>
      </div>
    </div>

    {{-- ═══ MODAL: Delete Confirm ═══ --}}
    <div class="inv-modal-overlay" id="deleteModal">
      <div class="inv-modal inv-modal-sm">
        <div class="inv-modal-head">
          <h3><i class="bi bi-trash" style="color:var(--danger)"></i> Delete Product</h3>
          <button class="inv-modal-close" onclick="closeModal('deleteModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="inv-modal-body">
          <p>You are about to permanently delete <strong id="deleteProductName"></strong>.</p>
          <p class="inv-adjust-hint" style="color:var(--danger)">
            <i class="bi bi-exclamation-triangle"></i> This action cannot be undone.
          </p>
        </div>
        <form method="POST" id="deleteForm">
          @csrf
          @method('DELETE')
          <div class="inv-modal-footer">
            <button type="button" class="ghost-btn" onclick="closeModal('deleteModal')">Cancel</button>
            <button type="submit" class="primary-btn" style="background:var(--danger)">Delete</button>
          </div>
        </form>
      </div>
    </div>

    <script>
      function openModal(id)  { document.getElementById(id).classList.add('active'); document.body.style.overflow = 'hidden'; }
      function closeModal(id) { document.getElementById(id).classList.remove('active'); document.body.style.overflow = ''; }

      document.querySelectorAll('.inv-modal-overlay').forEach(o => {
        o.addEventListener('click', e => { if (e.target === o) closeModal(o.id); });
      });

      document.getElementById('openAddModal').addEventListener('click', () => openModal('addModal'));

      function openEdit(id, name, unit, price, category, notes) {
        const form = document.getElementById('editForm');
        form.action = '/products/' + id;
        document.getElementById('editName').value     = name;
        document.getElementById('editUnit').value     = unit;
        document.getElementById('editPrice').value    = price;
        document.getElementById('editCategory').value = category;
        document.getElementById('editNotes').value    = notes;
        openModal('editModal');
      }

      function openDelete(id, name) {
        document.getElementById('deleteProductName').textContent = name;
        document.getElementById('deleteForm').action = '/products/' + id;
        openModal('deleteModal');
      }
    </script>
    @endif

    @include('partials._sidebar_js')
  </body>
</html>
