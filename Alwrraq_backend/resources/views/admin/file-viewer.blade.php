<!DOCTYPE html>
<html lang="{{ session('ui_locale', 'ar') === 'en' ? 'en' : 'ar' }}" dir="{{ session('ui_locale', 'ar') === 'en' ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>عرض الملف - الورّاق</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #f3f6fb; color: #0f172a; }
        .page { min-height: 100vh; padding: 18px; }
        .viewer-shell { max-width: 1220px; margin: 0 auto; display: grid; grid-template-columns: 330px minmax(0, 1fr); gap: 16px; align-items: start; }
        .panel { background: #ffffff; border: 1px solid #dbe3ef; border-radius: 14px; box-shadow: 0 16px 42px rgba(15, 23, 42, 0.08); overflow: hidden; }
        .info-panel { padding: 16px; position: sticky; top: 18px; }
        .brand { padding: 16px; background: #0f172a; color: #ffffff; border-radius: 12px; margin-bottom: 14px; }
        .brand h1 { margin: 0; font-size: 22px; color: #ffffff; }
        .brand-logo { width: 54px; height: 54px; border-radius: 14px; object-fit: cover; display: block; margin-bottom: 10px; background: #ffffff; border: 1px solid rgba(255,255,255,0.18); }
        .brand p { margin: 6px 0 0; color: #cbd5e1; font-weight: 700; line-height: 1.7; }
        .file-name { margin: 0 0 14px; color: #111827; font-size: 16px; font-weight: 900; line-height: 1.8; word-break: break-word; }
        .meta-grid { display: grid; gap: 8px; }
        .meta-item { padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc; }
        .meta-item span { display: block; margin-bottom: 3px; color: #64748b; font-size: 12px; font-weight: 900; }
        .meta-item strong { display: block; color: #0f172a; font-size: 14px; font-weight: 900; line-height: 1.6; }
        .notice { margin-top: 12px; padding: 11px 12px; border-radius: 10px; border: 1px solid #bae6fd; background: #f0f9ff; color: #0c4a6e; font-size: 12px; font-weight: 900; line-height: 1.8; }
        .actions { display: grid; gap: 9px; margin-top: 14px; }
        .action { width: 100%; min-height: 42px; display: inline-flex; align-items: center; justify-content: center; padding: 11px 14px; border: 0; border-radius: 10px; color: #ffffff; font-family: inherit; font-size: 14px; font-weight: 900; text-decoration: none; cursor: pointer; }
        .action.blue { background: #2563eb; }
        .action.blue:hover { background: #1d4ed8; }
        .action.green { background: #16a34a; }
        .action.green:hover { background: #15803d; }
        .action.dark { background: #0f172a; }
        .action.dark:hover { background: #1e293b; }
        .preview-panel { min-height: calc(100vh - 36px); display: flex; flex-direction: column; }
        .preview-head { padding: 13px 16px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; gap: 12px; align-items: center; }
        .preview-head h2 { margin: 0; font-size: 17px; color: #111827; }
        .preview-head span { color: #64748b; font-size: 12px; font-weight: 900; }
        .preview-frame { width: 100%; flex: 1; min-height: 760px; border: 0; background: #ffffff; }
        .unsupported { min-height: 520px; padding: 28px; display: grid; place-items: center; text-align: center; }
        .unsupported-box { max-width: 520px; padding: 24px; border: 1px dashed #cbd5e1; border-radius: 14px; background: #f8fafc; }
        .unsupported-box h2 { margin: 0 0 8px; font-size: 22px; }
        .unsupported-box p { margin: 0; color: #475569; font-weight: 800; line-height: 1.8; }
        @media (max-width: 900px) {
            .viewer-shell { grid-template-columns: 1fr; }
            .info-panel { position: static; }
            .preview-frame { min-height: 620px; }
        }
        @media print {
            body { background: #ffffff; }
            .info-panel, .preview-head { display: none; }
            .page { padding: 0; }
            .viewer-shell { display: block; max-width: none; }
            .preview-panel { border: 0; box-shadow: none; min-height: 100vh; }
            .preview-frame { min-height: 100vh; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="viewer-shell">
            <aside class="panel info-panel">
                <div class="brand">
                    <img class="brand-logo" src="{{ asset('images/alwrraq-logo.jpeg') }}" alt="شعار الورّاق">
                    <h1>الورّاق</h1>
                    <p>معاينة ملف الطلب والطباعة المباشرة.</p>
                </div>

                <p class="file-name">{{ $file->original_name }}</p>

                <div class="meta-grid">
                    @php($isAcademicWord = in_array($order->service_type, ['thesis', 'phd'], true) && $file->file_type === 'word')
                    <div class="meta-item">
                        <span>العميل</span>
                        <strong>{{ $order->user->name ?? '-' }} - {{ $order->user->phone ?? '-' }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>الخدمة</span>
                        <strong>{{ $serviceNames[$order->service_type] ?? $order->service_type }}</strong>
                    </div>
                    @if ($isAcademicWord)
                    <div class="meta-item">
                        <span>الاستخدام</span>
                        <strong>ملف Word للعرض فقط، وغير محتسب ضمن الطباعة أو التجليد أو التسعير.</strong>
                    </div>
                    @else
                    <div class="meta-item">
                        <span>عدد النسخ</span>
                        <strong>{{ in_array($order->service_type, ['thesis', 'phd'], true) && $file->file_type === 'word' ? 'للعرض فقط' : $file->copies }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>نوع الطباعة</span>
                        <strong>{{ in_array($order->service_type, ['thesis', 'phd'], true) && $file->file_type === 'word' ? 'للعرض فقط' : ($printSideNames[$file->print_sides] ?? 'وجهين') }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>حجم الصفحة</span>
                        <strong>{{ $pageSizeNames[$file->page_size] ?? 'A4' }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>نمط الطباعة</span>
                        <strong>{{ $printColor }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>التغليف / التجليد</span>
                        <strong>{{ $bindingNames[$file->binding_type] ?? '-' }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>عدد الصفحات</span>
                        <strong>{{ $file->pages }}</strong>
                    </div>
                    @endif
                </div>

                <div class="notice">
                    الطباعة المباشرة تفتح نافذة الطابعة من المتصفح. تأكد من ضبط الطابعة على نفس البيانات أعلاه، ولا تعتمد على لون الورق من أمر الطباعة.
                </div>

                <div class="actions">
                    @if ($isPrintablePreview)
                        <button class="action green" type="button" onclick="printPreview('{{ route('admin.files.view', ['file' => $file, 'raw' => 1]) }}')">طباعة الملف مباشرة</button>
                    @endif
                    <a class="action blue" href="{{ route('admin.files.download', $file) }}" data-complete-order-download>تحميل الملف</a>
                    <a class="action dark" href="{{ route('admin.orders') }}">العودة للطلبات</a>
                </div>
            </aside>

            <main class="panel preview-panel">
                <div class="preview-head">
                    <h2>عرض الملف</h2>
                    <span>{{ strtoupper($file->file_type) }}</span>
                </div>

                @if ($isPrintablePreview)
                    <iframe id="filePreview" class="preview-frame" src="{{ route('admin.files.view', ['file' => $file, 'raw' => 1]) }}"></iframe>
                @else
                    <div class="unsupported">
                        <div class="unsupported-box">
                            <h2>المعاينة المباشرة غير مدعومة لهذا النوع</h2>
                            <p>ملفات Word غالبًا لا يعرضها المتصفح مباشرة. استخدم زر تحميل الملف إذا احتجت فتحه في Word.</p>
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>

    <script>
        function printPreview(rawUrl) {
            const printFrame = document.createElement('iframe');
            printFrame.style.cssText = 'position:fixed;width:1px;height:1px;opacity:0;pointer-events:none;border:0;';
            document.body.appendChild(printFrame);

            const triggerPrint = () => {
                try {
                    printFrame.contentWindow.focus();
                    printFrame.contentWindow.print();
                    setTimeout(() => printFrame.remove(), 1000);
                } catch (error) {
                    printFrame.remove();
                }
            };

            printFrame.addEventListener('load', () => setTimeout(triggerPrint, 300), { once: true });
            printFrame.src = rawUrl;
        }
    </script>
    @include('shared.language-tools')
</body>
</html>
