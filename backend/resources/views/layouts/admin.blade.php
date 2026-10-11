<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title>@yield('title', __('لوحة التحكم')) | {{ __('ميكس') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@700;800;900&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v=10">
</head>
<body>
@php
    $newInquiries = \App\Models\Order::where('status', 'new')->count();
    $unreadMessages = \App\Models\Contact::where('is_read', false)->count();
    $nav = [
        ['label' => null, 'items' => [
            ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'icon' => 'home', 'text' => __('الرئيسية')],
        ]],
        ['label' => __('الكتالوج'), 'items' => [
            ['route' => 'admin.products.index', 'match' => 'admin.products.*', 'icon' => 'box', 'text' => __('المنتجات')],
            ['route' => 'admin.categories.index', 'match' => 'admin.categories.*', 'icon' => 'tag', 'text' => __('التصنيفات')],
            ['route' => 'admin.brands.index', 'match' => 'admin.brands.*', 'icon' => 'globe', 'text' => __('بلد المنشأ')],
        ]],
        ['label' => __('الوارد'), 'items' => [
            ['route' => 'admin.orders.index', 'match' => 'admin.orders.*', 'icon' => 'inbox', 'text' => __('استفسارات المنتجات'), 'count' => $newInquiries],
            ['route' => 'admin.contacts.index', 'match' => 'admin.contacts.*', 'icon' => 'mail', 'text' => __('رسائل التواصل'), 'count' => $unreadMessages],
        ]],
        ['label' => __('الشركة'), 'items' => [
            ['route' => 'admin.certificates.index', 'match' => 'admin.certificates.*', 'icon' => 'award', 'text' => __('الاعتمادات والتراخيص')],
        ]],
    ];
@endphp

<div class="overlay" id="overlay"></div>

<div class="shell">
    <aside class="sidebar" id="sidebar" aria-label="{{ __('القائمة الرئيسية') }}">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            <img src="{{ asset('logo-128.png') }}" alt="" width="44" height="44" style="flex:none">
            <span><b>{{ __('ميكس') }}</b><small>{{ __('لوحة التحكم') }}</small></span>
        </a>

        <nav class="nav">
            @foreach ($nav as $group)
                @if ($group['label'])
                    <div class="nav-label">{{ $group['label'] }}</div>
                @endif
                @foreach ($group['items'] as $item)
                    <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['match']) ? 'active' : '' }}"
                       @if (request()->routeIs($item['match'])) aria-current="page" @endif>
                        <x-admin.icon :name="$item['icon']" />
                        <span>{{ $item['text'] }}</span>
                        @if (!empty($item['count']))
                            <span class="count" title="{{ __('غير مقروء') }}">{{ $item['count'] }}</span>
                        @endif
                    </a>
                @endforeach
            @endforeach
        </nav>

        <div class="foot">
            <a href="{{ config('app.frontend_url') }}" target="_blank" rel="noopener">
                <x-admin.icon name="external" /><span>{{ __('فتح الموقع') }}</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"><x-admin.icon name="logout" /><span>{{ __('تسجيل الخروج') }}</span></button>
            </form>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <button type="button" class="menu-btn" id="menuBtn" aria-label="{{ __('فتح القائمة') }}" aria-controls="sidebar" aria-expanded="false">
                <x-admin.icon name="menu" />
            </button>
            <div class="crumbs">
                @hasSection('breadcrumb')
                    <a href="{{ route('admin.dashboard') }}">{{ __('الرئيسية') }}</a>
                    <span aria-hidden="true">/</span>
                    @yield('breadcrumb')
                @else
                    <b>@yield('title', __('لوحة التحكم'))</b>
                @endif
            </div>
            <div class="spacer"></div>
            @php $otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar'; @endphp
            <a href="?{{ http_build_query(array_merge(request()->query(), ['lang' => $otherLocale])) }}" class="lang-switch" hreflang="{{ $otherLocale }}" lang="{{ $otherLocale }}">
                <x-admin.icon name="globe" style="width:15px;height:15px" />{{ $otherLocale === 'en' ? 'English' : 'العربية' }}
            </a>
            <a href="{{ config('app.frontend_url') }}" target="_blank" rel="noopener" class="site-link">
                <x-admin.icon name="external" /><span>{{ __('عرض الموقع') }}</span>
            </a>
            <div class="user">
                <span class="avatar">{{ mb_substr(auth()->user()?->name ?? __('م'), 0, 1) }}</span>
                <span class="who"><b>{{ auth()->user()?->name ?? __('مدير النظام') }}</b><small>{{ __('مدير') }}</small></span>
            </div>
        </header>

        <main class="content">
            @if (session('success'))
                <div class="flash" role="status">
                    <x-admin.icon name="check" />
                    <span>{{ session('success') }}</span>
                    <button type="button" data-dismiss aria-label="{{ __('إغلاق') }}">×</button>
                </div>
            @endif
            @if ($errors->any())
                <div class="flash err" role="alert">
                    <x-admin.icon name="alert" />
                    <span>{{ __('راجع الحقول المميّزة باللون الأحمر ثم احفظ مرة أخرى.') }}</span>
                    <button type="button" data-dismiss aria-label="{{ __('إغلاق') }}">×</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<div class="dialog-back" id="confirm" hidden>
    <div class="dialog" role="alertdialog" aria-modal="true" aria-labelledby="confirmTitle" aria-describedby="confirmText">
        <h3 id="confirmTitle">{{ __('حذف العنصر') }}</h3>
        <p id="confirmText"></p>
        <div class="acts">
            <button type="button" class="btn btn-ghost" id="confirmNo">{{ __('إلغاء') }}</button>
            <button type="button" class="btn btn-danger" id="confirmYes"><x-admin.icon name="trash" />{{ __('حذف') }}</button>
        </div>
    </div>
</div>

<script>
(function () {
    // Sidebar on small screens
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const menuBtn = document.getElementById('menuBtn');
    function setMenu(open) {
        sidebar.classList.toggle('open', open);
        overlay.classList.toggle('open', open);
        menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    menuBtn.addEventListener('click', () => setMenu(!sidebar.classList.contains('open')));
    overlay.addEventListener('click', () => setMenu(false));

    // Flash dismiss
    document.querySelectorAll('[data-dismiss]').forEach(b => b.addEventListener('click', () => b.parentElement.remove()));

    // Confirm dialog for destructive forms
    const dlg = document.getElementById('confirm');
    let pending = null, lastFocus = null;
    document.addEventListener('submit', e => {
        const form = e.target;
        if (!form.matches('[data-confirm]') || form.dataset.confirmed) return;
        e.preventDefault();
        pending = form; lastFocus = document.activeElement;
        document.getElementById('confirmTitle').textContent = form.dataset.confirmTitle || @json(__('تأكيد'));
        document.getElementById('confirmText').textContent = form.dataset.confirm;
        dlg.hidden = false;
        document.getElementById('confirmNo').focus();
    });
    function closeDlg() { dlg.hidden = true; pending = null; lastFocus && lastFocus.focus(); }
    document.getElementById('confirmNo').addEventListener('click', closeDlg);
    dlg.addEventListener('click', e => { if (e.target === dlg) closeDlg(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && !dlg.hidden) closeDlg(); });
    document.getElementById('confirmYes').addEventListener('click', () => {
        if (!pending) return;
        pending.dataset.confirmed = '1';
        const yes = document.getElementById('confirmYes');
        window.adminLoading && window.adminLoading.start(pending, yes);
        pending.submit();
    });

    // Arabic / English tabs inside forms
    document.querySelectorAll('[data-lang-tabs]').forEach(box => {
        const btns = box.querySelectorAll('.lang-tabs button');
        btns.forEach(b => b.addEventListener('click', () => {
            btns.forEach(x => x.classList.toggle('on', x === b));
            box.querySelectorAll('.lang-pane').forEach(p => p.hidden = p.dataset.pane !== b.dataset.pane);
        }));
        // Open the tab that holds the first error
        const errField = box.querySelector('.lang-pane .field.has-error');
        if (errField) {
            const pane = errField.closest('.lang-pane').dataset.pane;
            box.querySelector('.lang-tabs button[data-pane="' + pane + '"]')?.click();
        }
    });

    // File inputs: preview images, show chosen file name
    document.querySelectorAll('.upload input[type=file]').forEach(input => {
        const box = input.closest('.upload');
        ['dragenter', 'dragover'].forEach(ev => box.addEventListener(ev, () => box.classList.add('drag')));
        ['dragleave', 'drop'].forEach(ev => box.addEventListener(ev, () => box.classList.remove('drag')));
        input.addEventListener('change', () => {
            const f = input.files[0];
            if (!f) return;
            const name = box.querySelector('[data-file-name]');
            if (name) name.textContent = input.multiple && input.files.length > 1
                ? @json(__('صور مختارة: ')) + input.files.length
                : f.name;
            const preview = box.querySelector('.preview');
            if (preview && f.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(f);
                img.alt = '';
                preview.replaceChildren(img);
                preview.hidden = false;
            }
        });
    });

    // Auto-submit filter selects
    document.querySelectorAll('[data-autosubmit]').forEach(el => el.addEventListener('change', () => el.form.submit()));
})();
</script>
<div class="page-loading" id="pageLoading" aria-hidden="true"></div>
<script>
// Loading feedback: spinner + disabled button on any form submit, and a top bar on page navigation.
(function () {
    const bar = document.getElementById('pageLoading');
    const busyText = @json(__('جاري التنفيذ…'));

    function setBusy(btn) {
        if (!btn || btn.classList.contains('is-loading')) return;
        btn.classList.add('is-loading');
        btn.setAttribute('aria-busy', 'true');
        btn.setAttribute('aria-disabled', 'true');
        if (!btn.querySelector('.spin')) btn.insertAdjacentHTML('afterbegin', '<span class="spin" aria-hidden="true"></span>');
        const sr = document.createElement('span');
        sr.className = 'sr-only';
        sr.textContent = busyText;
        btn.appendChild(sr);
    }
    function start(form, btn) {
        bar.classList.add('on');
        const target = btn || form?.querySelector('button[type=submit], button:not([type])');
        setBusy(target);
        // Lock the other submit buttons of this form so it can't be sent twice.
        form?.querySelectorAll('button[type=submit], button:not([type])').forEach(b => { if (b !== target) b.disabled = true; });
    }
    window.adminLoading = { start };

    document.addEventListener('submit', e => {
        if (e.defaultPrevented) return; // e.g. waiting for the delete confirmation
        const form = e.target;
        if (form.dataset.loading) { e.preventDefault(); return; } // already submitting
        form.dataset.loading = '1';
        start(form, e.submitter);
    });

    // Top bar for normal page links.
    document.addEventListener('click', e => {
        const a = e.target.closest('a[href]');
        if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        if (a.target === '_blank' || a.hasAttribute('download')) return;
        const href = a.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:')) return;
        if (a.origin !== location.origin) return;
        bar.classList.add('on');
        if (a.classList.contains('btn') || a.classList.contains('icon-btn')) setBusy(a);
    });

    // Coming back with the browser's back button restores the page from cache: clear the loading state.
    window.addEventListener('pageshow', e => {
        if (!e.persisted) return;
        bar.classList.remove('on');
        document.querySelectorAll('.is-loading').forEach(b => {
            b.classList.remove('is-loading');
            b.removeAttribute('aria-busy');
            b.removeAttribute('aria-disabled');
            b.querySelector('.spin')?.remove();
        });
        document.querySelectorAll('form[data-loading]').forEach(f => delete f.dataset.loading);
        document.querySelectorAll('button[disabled]').forEach(b => b.disabled = false);
    });
})();
</script>
@stack('scripts')
</body>
</html>
