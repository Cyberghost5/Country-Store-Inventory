<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>People - Country Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}" />
    <style>
      .section-title { font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#9a9488; }
      /* Role badges */
      .role-pill { display:inline-block; font-size:.67rem; font-weight:700; padding:3px 10px; border-radius:999px; text-transform:capitalize; letter-spacing:.04em; }
      .role-pill.super-admin { background:#1d086c; color:#ffd900; }
      .role-pill.admin       { background:#fff3b0; color:#7a5a00; border:1px solid #f0d060; }
      .role-pill.staff       { background:#f4f0e8; color:#4f574c; border:1px solid #ddd7c8; }
      /* Avatar in table */
      .people-avatar { width:30px; height:30px; border-radius:50%; background:#1d086c; color:#ffd900; display:inline-grid; place-items:center; font-size:.75rem; font-weight:700; flex-shrink:0; vertical-align:middle; margin-right:8px; }
      .people-name-cell { display:flex; align-items:center; }
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
            <h2>People</h2>
            <p>Manage admin and staff accounts.</p>
          </div>
          <div class="top-actions">
            <button class="primary-btn" id="openAddModal">
              <i class="bi bi-person-plus"></i> Add User
            </button>
          </div>
        </header>

        {{-- Flash --}}
        @if (session('status'))
          <div class="lp-success" style="margin-bottom:14px;">
            <i class="bi bi-check-circle"></i> {{ session('status') }}
          </div>
        @endif
        @if (session('error'))
          <div class="lp-error" style="margin-bottom:14px;">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
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
              <span class="mini-icon" style="background:#eef2ff;"><i class="bi bi-people-fill" style="color:#1d086c;"></i></span>
            </div>
            <h4 class="stat-value">{{ $stats['total'] }}</h4>
            <p class="stat-unit">accounts</p>
            <small class="stat-label">Total Users</small>
          </article>
          <article class="stat-card">
            <div class="stat-top">
              <span class="mini-icon" style="background:#fff8e0;"><i class="bi bi-shield-check" style="color:#a07b00;"></i></span>
            </div>
            <h4 class="stat-value">{{ $stats['admins'] }}</h4>
            <p class="stat-unit">accounts</p>
            <small class="stat-label">Admins</small>
          </article>
          <article class="stat-card">
            <div class="stat-top">
              <span class="mini-icon" style="background:#f4f0e8;"><i class="bi bi-person" style="color:#4f574c;"></i></span>
            </div>
            <h4 class="stat-value">{{ $stats['staff'] }}</h4>
            <p class="stat-unit">accounts</p>
            <small class="stat-label">Staff</small>
          </article>
        </section>

        {{-- People table --}}
        <section class="card table-card">
          <div style="padding:14px 20px 6px;">
            <h3 class="section-title">All Users</h3>
          </div>
          <div class="table-scroll">
            <table class="inv-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Phone</th>
                  <th>Role</th>
                  <th>Joined</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @forelse ($people as $i => $person)
                  <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td>
                      <div class="people-name-cell">
                        <span class="people-avatar">{{ strtoupper(substr($person->name, 0, 1)) }}</span>
                        <span class="inv-name">{{ $person->name }}</span>
                        @if ($person->id === $user->id)
                          <span style="font-size:.65rem;font-weight:600;background:#eaf6ee;color:#246b3a;padding:2px 7px;border-radius:999px;margin-left:6px;">You</span>
                        @endif
                      </div>
                    </td>
                    <td>{{ $person->phone }}</td>
                    <td>
                      @php $roleKey = str_replace('_', '-', $person->role); @endphp
                      <span class="role-pill {{ $roleKey }}">{{ ucfirst(str_replace('_', ' ', $person->role)) }}</span>
                    </td>
                    <td>{{ $person->created_at->format('d M Y') }}</td>
                    <td>
                      {{-- Delete: not yourself, not super_admin, admin cannot delete other admins --}}
                      @if (
                        $person->id !== $user->id &&
                        $person->role !== 'super_admin' &&
                        ($user->isSuperAdmin() || $person->role === 'staff')
                      )
                        <form method="POST" action="{{ route('people.destroy', $person) }}"
                              onsubmit="return confirm('Remove {{ addslashes($person->name) }} from the system?')">
                          @csrf @method('DELETE')
                          <button class="inv-action-btn danger" type="submit" title="Remove user">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="inv-empty-row">
                      <i class="bi bi-people" style="font-size:1.4rem;"></i>
                      <p>No users found.</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </section>

      </main>
    </div>

    {{-- ═══ MODAL: Add User ═══ --}}
    <div class="inv-modal-overlay" id="addModal">
      <div class="inv-modal">
        <div class="inv-modal-head">
          <h3><i class="bi bi-person-plus"></i> Add User</h3>
          <button class="inv-modal-close" onclick="closeModal('addModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form method="POST" action="{{ route('people.store') }}" novalidate id="addForm">
          @csrf
          <div class="inv-modal-body">
            <div class="form-grid two-cols">

              <label class="span-2">
                <span>Full Name <span class="inv-required">*</span></span>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="e.g. Amina Bello" />
              </label>

              <label class="span-2">
                <span>Phone Number <span class="inv-required">*</span></span>
                <input type="text" name="phone" value="{{ old('phone') }}" required
                       placeholder="e.g. 08012345678" />
              </label>

              <label class="{{ $user->isSuperAdmin() ? 'span-2' : 'span-2' }}">
                <span>Role <span class="inv-required">*</span></span>
                @if ($user->isSuperAdmin())
                  <select name="role" required>
                    <option value="staff" {{ old('role', 'staff') === 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                  </select>
                @else
                  <input type="hidden" name="role" value="staff" />
                  <input type="text" value="Staff" readonly style="background:#f4f0e8;color:#9a9488;cursor:not-allowed;" />
                @endif
              </label>

              <label>
                <span>Password <span class="inv-required">*</span></span>
                <input type="password" name="password" required
                       placeholder="Min. 6 characters" autocomplete="new-password" />
              </label>

              <label>
                <span>Confirm Password <span class="inv-required">*</span></span>
                <input type="password" name="password_confirmation" required
                       placeholder="Repeat password" autocomplete="new-password" />
              </label>

            </div>
          </div>
          <div class="inv-modal-footer">
            <button type="button" class="ghost-btn" onclick="closeModal('addModal')">Cancel</button>
            <button type="submit" class="primary-btn"><i class="bi bi-check-lg"></i> Create User</button>
          </div>
        </form>
      </div>
    </div>

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

      @if ($errors->any())
        document.getElementById('addModal').classList.add('active');
      @endif
    </script>
  </body>
</html>
