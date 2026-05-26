<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Notifications - Country Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}" />
    <style>
      .page-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 24px;
      }
      .page-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2e342b;
        display: flex;
        align-items: center;
        gap: 10px;
      }
      .page-title .page-title-icon {
        width: 40px;
        height: 40px;
        background: #1d086c;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.1rem;
      }
      .notif-actions-bar {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
      }
      .notif-card {
        background: #fff;
        border-radius: 14px;
        border: 1.5px solid #ece7da;
        overflow: hidden;
      }
      .notif-list {
        list-style: none;
        margin: 0;
        padding: 0;
      }
      .notif-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        color: #9a9488;
        gap: 12px;
      }
      .notif-empty-icon {
        font-size: 3rem;
        color: #ddd7c8;
      }
      .notif-empty p {
        font-size: .9rem;
        font-weight: 500;
        margin: 0;
      }
      .notif-delete-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: #c0b8a8;
        font-size: .88rem;
        padding: 4px 6px;
        border-radius: 6px;
        line-height: 1;
        transition: color .15s, background .15s;
        flex-shrink: 0;
      }
      .notif-delete-btn:hover {
        color: #b33a36;
        background: #fdecea;
      }
      .notif-read-btn {
        background: none;
        border: 1.5px solid #ddd7c8;
        border-radius: 7px;
        font-family: inherit;
        font-size: .7rem;
        font-weight: 600;
        color: #4f574c;
        padding: 3px 10px;
        cursor: pointer;
        white-space: nowrap;
        transition: border-color .15s, background .15s, color .15s;
      }
      .notif-read-btn:hover {
        border-color: #1d086c;
        color: #1d086c;
        background: #eef2ff;
      }
      .pagination-bar {
        margin-top: 20px;
        display: flex;
        justify-content: center;
      }
      .btn-outline-sm {
        background: #fff;
        border: 1.5px solid #ddd7c8;
        border-radius: 8px;
        padding: 7px 18px;
        font-family: inherit;
        font-size: .8rem;
        font-weight: 600;
        color: #4f574c;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: border-color .15s, color .15s, background .15s;
      }
      .btn-outline-sm:hover {
        border-color: #1d086c;
        color: #1d086c;
        background: #f0edff;
      }
      .btn-outline-sm.danger:hover {
        border-color: #b33a36;
        color: #b33a36;
        background: #fdecea;
      }
    </style>
  </head>
  <body>
    @include('partials._mobile_topbar')
    <div class="app-shell">

      <aside class="sidebar" id="sidebar">
        @include('partials._sidebar', ['user' => $user])
      </aside>

      <main class="main-content" id="mainContent">

        @if (session('status'))
          <div class="alert alert-success mb-20">{{ session('status') }}</div>
        @endif

        <div class="page-topbar">
          <div class="page-title">
            <span class="page-title-icon"><i class="bi bi-bell"></i></span>
            Notifications
            @if ($unreadCount > 0)
              <span class="notif-unread-label">{{ $unreadCount }} unread</span>
            @endif
          </div>

          @if ($notifications->isNotEmpty())
            <div class="notif-actions-bar">
              @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.readAll') }}">
                  @csrf
                  <button type="submit" class="btn-outline-sm">
                    <i class="bi bi-check2-all"></i> Mark All Read
                  </button>
                </form>
              @endif
              <form method="POST" action="{{ route('notifications.destroyAll') }}"
                    onsubmit="return confirm('Delete all notifications? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-outline-sm danger">
                  <i class="bi bi-trash3"></i> Delete All
                </button>
              </form>
            </div>
          @endif
        </div>

        <div class="notif-card">
          @forelse ($notifications as $notif)
            @php
              $data    = $notif->data;
              $type    = $data['type'] ?? 'info';
              $isRead  = !is_null($notif->read_at);
              $icon    = match($type) {
                  'sale'     => 'bi-bag-check',
                  'expense'  => 'bi-cash-stack',
                  'purchase' => 'bi-box-arrow-in-down',
                  default    => 'bi-bell',
              };
              $iconColor = match($type) {
                  'sale'     => '#246b3a',
                  'expense'  => '#b33a36',
                  'purchase' => '#1d086c',
                  default    => '#1d086c',
              };
            @endphp
            <li class="notif-item {{ $isRead ? 'notif-read' : 'notif-unread' }}">
              <div class="notif-icon-wrap" style="{{ $isRead ? '' : 'background:#fde68a;' }}">
                <i class="bi {{ $icon }} notif-icon" style="color:{{ $iconColor }};"></i>
              </div>
              <div class="notif-body">
                @if (!empty($data['url']))
                  <a href="{{ $data['url'] }}" class="notif-message" style="text-decoration:none;color:inherit;">
                    {{ $data['message'] ?? 'New notification.' }}
                  </a>
                @else
                  <div class="notif-message">{{ $data['message'] ?? 'New notification.' }}</div>
                @endif
                <div class="notif-time">{{ $notif->created_at->diffForHumans() }}</div>
              </div>
              <div class="notif-actions" style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                @if (!$isRead)
                  <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                    @csrf
                    <button type="submit" class="notif-read-btn"><i class="bi bi-check2-all"></i></button>
                  </form>
                @endif
                <form method="POST" action="{{ route('notifications.destroy', $notif->id) }}"
                      onsubmit="return confirm('Delete this notification?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="notif-delete-btn" title="Delete">
                    <i class="bi bi-trash3"></i>
                  </button>
                </form>
              </div>
            </li>
          @empty
            <div class="notif-empty">
              <i class="bi bi-bell-slash notif-empty-icon"></i>
              <p>No notifications yet.</p>
            </div>
          @endforelse
        </div>

        @if ($notifications->hasPages())
          <div class="pagination-bar">
            {{ $notifications->links() }}
          </div>
        @endif

      </main>
    </div>

    <script>
      // Sidebar toggle (same as other pages)
      const sidebarEl = document.getElementById('sidebar');
      const mainEl    = document.getElementById('mainContent');
      const toggleBtn = document.getElementById('sidebarToggle');
      if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
          sidebarEl.classList.toggle('sidebar-open');
        });
        document.addEventListener('click', e => {
          if (sidebarEl.classList.contains('sidebar-open')
              && !sidebarEl.contains(e.target)
              && !toggleBtn.contains(e.target)) {
            sidebarEl.classList.remove('sidebar-open');
          }
        });
      }
    </script>
  </body>
</html>
