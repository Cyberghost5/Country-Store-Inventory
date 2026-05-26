<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign In - Country Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}" />
  </head>
  <body>
    <div class="login-shell">

      {{-- LEFT PANEL --}}
      <div class="lp-left">
        <div class="lp-slider" aria-hidden="true">
          @foreach(range(1,7) as $i)
          <div class="lp-slide {{ $i === 1 ? 'lp-slide-active' : '' }}"
               style="background-image:url('{{ asset('assets/img/sliders/'.$i.'.jpg') }}')"></div>
          @endforeach
          <div class="lp-slide" style="background-image:url('{{ asset('assets/img/sliders/8.jpeg') }}')"></div>
          <div class="lp-slide" style="background-image:url('{{ asset('assets/img/sliders/9.jpeg') }}')"></div>
          <div class="lp-slide" style="background-image:url('{{ asset('assets/img/sliders/10.jpeg') }}')"></div>
          <div class="lp-slider-overlay"></div>
        </div>

        <div class="lp-brand">
          <div class="lp-brand-icon">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Country Store" />
          </div>
          <div>
            <h1>Country Store</h1>
            <p>Point of Sale System</p>
          </div>
        </div>

        <div class="lp-badge">
          <i class="bi bi-shield-check"></i>
          Secure Login Portal
        </div>

        <div class="lp-hero">
          <h2>Track. Sell.<br><span>Stay in Control.</span></h2>
          <p>
            Fast, reliable store management for your team.<br>
            Record sales, monitor stock, and track expenses<br>
            — all in one place.
          </p>
        </div>

        <div class="lp-stats">
          <div>
            <strong>Real-time</strong>
            <span>Stock Tracking</span>
          </div>
          <div>
            <strong>Daily</strong>
            <span>Sales Reports</span>
          </div>
          <div>
            <strong>3</strong>
            <span>Access Levels</span>
          </div>
        </div>
      </div>

      {{-- RIGHT PANEL --}}
      <div class="lp-right">
        <div class="lp-form-wrap">
          <div class="lp-form-head">
            <h2>Welcome back</h2>
            <p>Sign in to your account to manage the store.</p>
          </div>

          @if ($errors->any())
            <div class="lp-error">
              <i class="bi bi-exclamation-circle"></i>
              {{ $errors->first() }}
            </div>
          @endif

          <form method="POST" action="{{ route('login.post') }}" class="lp-form" novalidate>
            @csrf

            <div class="form-group">
              <label for="phone">Phone Number</label>
              <div class="input-wrap">
                <i class="bi bi-phone"></i>
                <input
                  id="phone"
                  type="tel"
                  name="phone"
                  placeholder="e.g. 08012345678"
                  value="{{ old('phone') }}"
                  autocomplete="tel"
                  autofocus
                  required
                />
              </div>
            </div>

            <div class="form-group">
              <label for="password">Password</label>
              <div class="input-wrap">
                <i class="bi bi-lock"></i>
                <input
                  id="password"
                  type="password"
                  name="password"
                  placeholder="••••••••"
                  autocomplete="current-password"
                  required
                />
                <button type="button" class="pw-toggle" aria-label="Toggle password visibility">
                  <i class="bi bi-eye" id="pwEyeIcon"></i>
                </button>
              </div>
            </div>

            <div class="form-row">
              <label class="check-label">
                <input type="checkbox" name="remember" id="remember" />
                <span class="custom-check"></span>
                Remember me for 30 days
              </label>
            </div>

            <button type="submit" class="btn-signin">
              Sign In to Dashboard
            </button>
          </form>

          <p class="lp-footer-note">
            This portal is restricted to authorized personnel only.<br>
            Country Store &copy; {{ date('Y') }}<br>
            Powered by <a href="https://zeetechfoundation.org" target="_blank" rel="noopener noreferrer">Zee Tech Ventures</a>
          </p>
        </div>
      </div>

    </div>

    <script>
      // Password toggle
      const pwToggle = document.querySelector('.pw-toggle');
      const pwInput  = document.getElementById('password');
      const pwIcon   = document.getElementById('pwEyeIcon');
      pwToggle.addEventListener('click', () => {
        const isHidden = pwInput.type === 'password';
        pwInput.type     = isHidden ? 'text' : 'password';
        pwIcon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
      });

      // Image slider
      (function () {
        const slides = document.querySelectorAll('.lp-slide');
        let current  = 0;
        setInterval(() => {
          slides[current].classList.remove('lp-slide-active');
          current = (current + 1) % slides.length;
          slides[current].classList.add('lp-slide-active');
        }, 4000);
      })();
    </script>
  </body>
</html>
