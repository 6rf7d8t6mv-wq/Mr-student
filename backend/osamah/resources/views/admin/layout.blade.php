<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'لوحة المدير')</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f3f4f6; color: #111827; }
        .layout { min-height: 100vh; display: grid; grid-template-columns: clamp(112px, 18vw, 240px) minmax(0, 1fr); }
        aside { background: #0f172a; color: #f8fafc; padding: clamp(16px, 2vw, 24px) clamp(12px, 1.8vw, 18px); position: sticky; top: 0; height: 100vh; overflow-y: auto; }
        .brand { font-size: clamp(19px, 2.2vw, 23px); font-weight: 800; margin-bottom: 6px; }
        .admin-name { color: #cbd5e1; font-size: 13px; margin-bottom: 24px; line-height: 1.6; }
        nav { display: flex; flex-direction: column; gap: 8px; }
        nav a, .logout { color: #f8fafc; text-decoration: none; border: 1px solid transparent; border-radius: 8px; padding: 11px 12px; background: transparent; text-align: right; font: inherit; cursor: pointer; }
        nav a { position: relative; display: block; }
        nav a:hover, nav a.active, .logout:hover { background: #1e293b; border-color: #334155; }
        .nav-notice-dot { position: absolute; top: 8px; left: 9px; width: 7px; height: 7px; border-radius: 999px; background: #dc2626; box-shadow: 0 0 0 2px rgba(15, 23, 42, 0.95); }
        .logout { width: 100%; margin-top: 18px; }
        main { min-width: 0; padding: clamp(16px, 3vw, 28px); overflow: auto; }
        .page-title { display: flex; justify-content: space-between; align-items: end; gap: 16px; margin-bottom: 20px; }
        h1 { margin: 0; font-size: clamp(24px, 4vw, 30px); }
        h2 { margin: 0 0 14px; font-size: clamp(19px, 3vw, 21px); }
        .subtitle { color: #64748b; margin: 6px 0 0; }
        .notice, .errors { margin-bottom: 18px; padding: 12px 14px; border-radius: 8px; }
        .notice { background: #ecfdf5; color: #047857; }
        .errors { background: #fef2f2; color: #b91c1c; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; }
        .stat, .panel, .order { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px; box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06); }
        .stat { padding: 16px; }
        .stat span { display: block; color: #64748b; font-size: 12px; margin-bottom: 8px; }
        .stat strong { font-size: 24px; }
        .panel { padding: 18px; margin-bottom: 18px; }
        .order { margin-bottom: 16px; overflow: hidden; }
        .order-head { padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; background: #f8fafc; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .label { color: #64748b; display: block; margin-bottom: 4px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { padding: 9px 10px; border-bottom: 1px solid #e5e7eb; text-align: right; vertical-align: middle; }
        th { background: #ffffff; color: #334155; }
        a { color: #0369a1; font-weight: 700; text-decoration: none; }
        .empty { padding: 20px; color: #64748b; }
        .forms-grid { display: grid; grid-template-columns: 0.85fr 1.15fr; gap: 18px; align-items: start; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .form-section { margin-top: 14px; padding-top: 14px; border-top: 1px solid #e5e7eb; }
        .form-section:first-child { margin-top: 0; padding-top: 0; border-top: 0; }
        .form-section-title { margin: 0 0 10px; color: #0f172a; font-size: 16px; font-weight: 900; }
        .form-note { margin: 0 0 10px; color: #64748b; font-size: 12px; line-height: 1.7; }
        .permissions-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
        .permission-option { display: flex; align-items: center; gap: 8px; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff; color: #0f172a; font-size: 13px; font-weight: 800; }
        .permission-option input { width: auto; }
        label { display: block; color: #475569; font-weight: 700; font-size: 12px; margin-bottom: 5px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff; font-size: 16px; }
        .save { margin-top: 10px; padding: 10px 14px; border: 0; border-radius: 8px; background: #0f172a; color: #ffffff; font-weight: 800; cursor: pointer; }
        .danger { margin-top: 10px; padding: 10px 14px; border: 0; border-radius: 8px; background: #b91c1c; color: #ffffff; font-weight: 800; cursor: pointer; }
        .toolbar { display: flex; justify-content: space-between; gap: 12px; align-items: end; margin-bottom: 16px; }
        .search-form { display: flex; gap: 8px; align-items: center; min-width: min(520px, 100%); }
        .search-form input { min-width: 280px; }
        .actions { display: flex; gap: 8px; align-items: center; }
        .small-button { margin-top: 0; padding: 7px 10px; border-radius: 7px; font-size: 12px; line-height: 1; }
        .ghost { margin-top: 0; padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 7px; background: #ffffff; color: #0f172a; font-size: 12px; font-weight: 800; cursor: pointer; }
        .badge { display: inline-flex; align-items: center; padding: 4px 8px; border-radius: 999px; background: #e0f2fe; color: #0369a1; font-size: 12px; font-weight: 800; }
        .tiny-status-dot { display: inline-flex; width: 8px; height: 8px; border-radius: 999px; vertical-align: middle; margin-inline-start: 6px; box-shadow: 0 0 0 2px #ffffff; }
        .tiny-status-dot.red { background: #dc2626; }
        .tiny-status-dot.yellow { background: #facc15; }
        .tiny-status-dot.green { background: #16a34a; }
        .id-badge { display: inline-flex; align-items: center; margin-inline-start: 8px; padding: 2px 7px; border-radius: 999px; background: #f1f5f9; color: #64748b; font-size: 11px; font-weight: 800; }
        .identity { display: flex; align-items: center; gap: 6px; white-space: nowrap; }
        .muted { color: #64748b; font-size: 12px; }
        .delivered-files-list { display: flex; flex-direction: column; gap: 8px; margin: 10px 0 14px; }
        .delivered-file-item { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff; }
        .delivered-file-name { color: #0f172a; font-weight: 900; line-height: 1.6; word-break: break-word; }
        .delivered-file-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .delivered-file-actions .ghost,
        .delivered-file-actions .save,
        .delivered-file-actions .danger { min-width: 110px; justify-content: center; text-align: center; }
        .modal-backdrop { position: fixed; inset: 0; display: none; place-items: center; padding: clamp(10px, 3vw, 20px); background: rgba(15, 23, 42, 0.55); z-index: 40; overflow-y: auto; }
        .modal-backdrop.active { display: grid; }
        .modal { width: min(560px, 100%); max-height: calc(100vh - 20px); background: #ffffff; border-radius: 12px; box-shadow: 0 24px 70px rgba(15, 23, 42, 0.28); overflow: hidden; display: flex; flex-direction: column; }
        .modal-head { padding: 18px 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .modal-head h2 { margin: 0; }
        .modal-body { padding: clamp(14px, 3vw, 20px); overflow-y: auto; }
        .modal-close { border: 0; background: #f1f5f9; border-radius: 8px; padding: 7px 10px; cursor: pointer; font-weight: 800; }
        .full { grid-column: 1 / -1; }
        @media (max-width: 980px) {
            .layout { grid-template-columns: 112px minmax(0, 1fr); }
            aside { position: sticky; top: 0; height: 100vh; padding: 14px 7px; }
            nav { flex-direction: column; flex-wrap: nowrap; }
            .stats, .forms-grid, .form-grid, .permissions-grid { grid-template-columns: 1fr; }
            .toolbar, .search-form { align-items: stretch; flex-direction: column; }
            .order-head { grid-template-columns: 1fr; }
            table { display: block; overflow-x: auto; white-space: nowrap; }
            .search-form input { min-width: 0; }
            .actions, nav form { width: 100%; }
            nav a, .logout, .save, .danger, .ghost { width: 100%; text-align: center; }
            .delivered-file-item { align-items: stretch; flex-direction: column; }
            .delivered-file-actions { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="layout">
        <aside>
            @php
                $hasUnopenedOrdersForAdmin = \App\Models\Order::query()
                    ->whereNull('admin_notification_seen_at')
                    ->where('status', '!=', 'completed')
                    ->exists();
            @endphp
            <div class="brand">Mr-Student</div>
            <div class="admin-name">{{ auth()->user()->name }}<br>مدير النظام</div>
            <nav>
                <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">الرئيسية</a>
                <a class="{{ request()->routeIs('admin.orders') ? 'active' : '' }}" href="{{ route('admin.orders') }}">
                    الطلبات
                    @if ($hasUnopenedOrdersForAdmin)
                        <span class="nav-notice-dot" aria-label="طلبات جديدة"></span>
                    @endif
                </a>
                <a class="{{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users') }}">المستخدمين</a>
                <a class="{{ request()->routeIs('admin.customers') ? 'active' : '' }}" href="{{ route('admin.customers') }}">العملاء</a>
                <a class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">الإعدادات</a>
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout" type="submit">تسجيل الخروج</button>
                </form>
            </nav>
        </aside>

        <main>
            @if (session('status'))
                <div class="notice">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="errors">{{ $errors->first() }}</div>
            @endif

            @yield('content')
        </main>
    </div>

    <div class="modal-backdrop" id="adminModal" onclick="closeAdminModal(event)">
        <div class="modal" role="dialog" aria-modal="true" onclick="event.stopPropagation()">
            <div class="modal-head">
                <h2 id="adminModalTitle">تعديل</h2>
                <button class="modal-close" type="button" onclick="closeAdminModal()">إغلاق</button>
            </div>
            <div class="modal-body" id="adminModalBody"></div>
        </div>
    </div>

    <script>
        function openAdminModal(title, templateId) {
            const modal = document.getElementById('adminModal');
            const body = document.getElementById('adminModalBody');
            const template = document.getElementById(templateId);

            document.getElementById('adminModalTitle').textContent = title;
            body.innerHTML = '';
            body.appendChild(template.content.cloneNode(true));
            modal.classList.add('active');
        }

        function closeAdminModal(event) {
            if (event && event.target.id !== 'adminModal') return;
            document.getElementById('adminModal').classList.remove('active');
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeAdminModal();
        });

        let activeSearchRequest = null;

        function bindAutoSearchForms(root = document) {
            root.querySelectorAll('.auto-search-form').forEach((form) => {
                if (form.dataset.searchBound === 'true') return;

                const input = form.querySelector('input[name="search"]');
                let timeoutId;

                const runSearch = () => {
                    if (!input) return;

                    const caretPosition = input.selectionStart ?? input.value.length;
                    const params = new URLSearchParams(new FormData(form));
                    const url = `${form.action}?${params.toString()}`;

                    activeSearchRequest?.abort();
                    activeSearchRequest = new AbortController();

                    fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        signal: activeSearchRequest.signal,
                    })
                        .then((response) => response.text())
                        .then((html) => {
                            const nextDocument = new DOMParser().parseFromString(html, 'text/html');
                            const nextMain = nextDocument.querySelector('main');
                            const currentMain = document.querySelector('main');

                            if (!nextMain || !currentMain) {
                                window.location.href = url;
                                return;
                            }

                            currentMain.innerHTML = nextMain.innerHTML;
                            window.history.replaceState({}, '', url);
                            bindAutoSearchForms(currentMain);

                            const nextInput = currentMain.querySelector('.auto-search-form input[name="search"]');
                            if (nextInput) {
                                nextInput.focus();
                                const nextCaretPosition = Math.min(caretPosition, nextInput.value.length);
                                nextInput.setSelectionRange(nextCaretPosition, nextCaretPosition);
                            }
                        })
                        .catch((error) => {
                            if (error.name !== 'AbortError') form.submit();
                        });
                };

                input?.addEventListener('input', () => {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(runSearch, 450);
                });

                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    clearTimeout(timeoutId);
                    runSearch();
                });

                form.dataset.searchBound = 'true';
            });
        }

        bindAutoSearchForms();

        document.addEventListener('click', (event) => {
            const link = event.target.closest('[data-complete-order-download]');
            if (!link) return;

            const dot = link.closest('.order')?.querySelector('[data-order-status-dot]');
            dot?.classList.remove('red', 'yellow');
            dot?.classList.add('green');
        });
    </script>
</body>
</html>
