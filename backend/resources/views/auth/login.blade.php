<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title>{{ __('تسجيل الدخول | ميكس') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@700;800;900&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v=7">
</head>
<body>
<main class="login">
    <section class="art" aria-hidden="true">
        <span class="disc"></span>
        <img src="{{ asset('logo-128.png') }}" alt="" width="88" height="88">
        <div>
            <div class="word">{{ __('ميكس') }}<span>.</span></div>
            <p>{{ __('لوحة تحكم موقع ميكس للمكسرات والقهوة. المنتجات، الاستفسارات، والرسائل في مكان واحد.') }}</p>
        </div>
    </section>

    <section class="panel">
        @php $otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar'; @endphp
        <a href="{{ route('locale.switch', $otherLocale) }}" class="lang-switch" hreflang="{{ $otherLocale }}" lang="{{ $otherLocale }}">
            <x-admin.icon name="globe" style="width:15px;height:15px" />{{ $otherLocale === 'en' ? 'English' : 'العربية' }}
        </a>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div>
                <h1>{{ __('تسجيل الدخول') }}</h1>
                <p class="muted" style="margin:6px 0 0">{{ __('للمشرفين فقط.') }}</p>
            </div>

            @if ($errors->any())
                <div class="flash err" role="alert" style="margin:0">
                    <x-admin.icon name="alert" />
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <div class="field @error('email') has-error @enderror">
                <label for="email">{{ __('البريد الإلكتروني') }}</label>
                <input class="input ltr ltr-val" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>

            <div class="field">
                <label for="password">{{ __('كلمة المرور') }}</label>
                <div class="pw">
                    <input class="input" type="password" id="password" name="password" required autocomplete="current-password">
                    <button type="button" id="togglePw" aria-label="{{ __('إظهار كلمة المرور') }}" aria-pressed="false"><x-admin.icon name="eye" /></button>
                </div>
            </div>

            <label class="check">
                <input type="checkbox" name="remember" id="remember">
                <span>{{ __('تذكرني على هذا الجهاز') }}</span>
            </label>

            <button type="submit" class="btn btn-primary btn-block" style="padding:13px">{{ __('دخول') }}</button>

            <p class="muted" style="margin:0;font-size:12px;text-align:center">{{ __('© :year ميكس. جميع الحقوق محفوظة.', ['year' => date('Y')]) }}</p>
        </form>
    </section>
</main>
<script>
    document.getElementById('togglePw').addEventListener('click', function () {
        const input = document.getElementById('password');
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        this.setAttribute('aria-pressed', show ? 'true' : 'false');
        this.setAttribute('aria-label', show ? @json(__('إخفاء كلمة المرور')) : @json(__('إظهار كلمة المرور')));
    });
</script>
</body>
</html>
