<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>خدمات الطباعة والتجليد</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f3f4f6; color: #1f2937; margin: 0; padding: 0; }
            .page-header { background: #0f172a; color: #f8fafc; padding: 20px 24px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15); position: sticky; top: 0; z-index: 10; }
            .header-inner { max-width: 1000px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; gap: 16px; }
            .brand { font-size: 24px; font-weight: 700; letter-spacing: 0.02em; }
            .brand-subtitle { margin: 4px 0 0; color: #cbd5e1; font-size: 14px; }
            .header-actions { display: flex; align-items: center; gap: 12px; color: #cbd5e1; font-size: 14px; }
            .header-actions a { color: #f8fafc; text-decoration: none; }
            .header-user, .header-link { display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
            .logout-button { background: transparent; color: #f8fafc; border: 1px solid #64748b; border-radius: 6px; padding: 7px 10px; cursor: pointer; }
            .container { max-width: 1000px; margin: 32px auto 24px; padding: 32px; background: #ffffff; border-radius: 24px; box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08); }
            h1 { margin: 0 0 8px; font-size: 36px; color: #111827; }
            h2 { margin: 28px 0 16px; font-size: 24px; color: #1f2937; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px; }
            p { margin: 0 0 26px; color: #475569; line-height: 1.7; }
            
            .services-screen { display: flex; flex-direction: column; gap: 20px; }
            .service-btn { padding: 20px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; border: none; border-radius: 12px; cursor: pointer; font-size: 18px; font-weight: 700; transition: all 0.3s; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15); }
            .service-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(15, 23, 42, 0.25); background: linear-gradient(135deg, #1e293b 0%, #334155 100%); }
            .service-btn:active { transform: translateY(0); }
            
            .back-button { padding: 10px 16px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: all 0.3s; align-self: flex-start; margin-bottom: 20px; }
            .back-button:hover { background: #4b5563; }
            
            .upload-content { display: none; }
            .upload-content.active { display: block; }
            
            .upload-section { margin-top: 40px; display: flex; gap: 20px; flex-wrap: wrap; }
            .upload-box { flex: 1; min-width: 280px; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 24px; background: #f9fafb; text-align: center; transition: all 0.3s; cursor: pointer; }
            .upload-box:hover { border-color: #38bdf8; background: #f0f9ff; }
            .upload-box.drag-over { border-color: #0f172a; background: #e0e7ff; }
            .upload-box h3 { margin: 0 0 12px; font-size: 18px; color: #111827; }
            .upload-box .file-icon { font-size: 36px; margin-bottom: 12px; }
            .upload-box input[type="file"] { display: none; }
            .upload-box .file-info { margin-top: 12px; font-size: 12px; color: #6b7280; }
            .upload-box .file-name { margin-top: 8px; padding: 8px; background: #ffffff; border-radius: 6px; color: #047857; font-weight: 600; }
            .upload-box.error { border-color: #ef4444; background: #fef2f2; }
            .upload-box.error .error-msg { color: #b91c1c; margin-top: 8px; font-size: 12px; }
            
            .files-list { margin-top: 20px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
            .files-list-header { background: #f8fafc; padding: 12px 16px; font-weight: 600; color: #111827; border-bottom: 1px solid #e2e8f0; display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 0.5fr; gap: 12px; font-size: 13px; }
            .files-list-item { padding: 12px 16px; border-bottom: 1px solid #e2e8f0; display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 0.5fr; gap: 12px; align-items: center; font-size: 13px; }
            .files-list-header.has-price,
            .files-list-item.has-price { grid-template-columns: 2fr 0.7fr 0.7fr 1.15fr 0.85fr 0.85fr 0.85fr 0.7fr 0.45fr; }
            .files-list-header.has-copies-price,
            .files-list-item.has-copies-price { grid-template-columns: 2fr 0.7fr 0.7fr 0.65fr 0.85fr 0.85fr 0.85fr 0.7fr 0.45fr; }
            .files-list-item:last-child { border-bottom: none; }
            .file-name-cell { color: #111827; font-weight: 500; word-break: break-word; }
            .file-pages { color: #475569; }
            .file-size { color: #6b7280; }
            .file-price { color: #0f172a; font-weight: 700; }
            .file-price-note { display: block; margin-top: 4px; color: #b91c1c; font-size: 11px; font-weight: 600; line-height: 1.4; }
            .binding-select { width: 100%; min-width: 130px; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: #ffffff; color: #111827; font-weight: 600; }
            .binding-select:invalid { color: #6b7280; }
            .copies-input { width: 72px; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: #ffffff; color: #111827; font-weight: 700; text-align: center; }
            .file-remove { cursor: pointer; color: #ef4444; font-weight: 600; text-align: center; }
            .file-remove:hover { color: #b91c1c; }
            .empty-message { padding: 20px; text-align: center; color: #9ca3af; font-size: 14px; }
            
            .progress-bar { margin-top: 12px; width: 100%; height: 4px; background: #e2e8f0; border-radius: 2px; overflow: hidden; display: none; }
            .progress-bar.active { display: block; }
            .progress-bar-fill { height: 100%; background: #38bdf8; width: 0%; transition: width 0.3s; }
            
            .upload-button { padding: 10px 16px; background: #0f172a; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; margin-top: 12px; transition: all 0.3s; }
            .upload-button:hover { background: #1e293b; }
            .upload-button:disabled { background: #cbd5e1; cursor: not-allowed; }
            
            .binding-section { margin-top: 30px; padding: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; }
            .binding-section h3 { margin-top: 0; margin-bottom: 16px; color: #111827; font-size: 18px; }
            .binding-required { color: #b91c1c; font-weight: 600; font-size: 12px; }
            .pricing-summary { margin-top: 16px; padding: 14px 16px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; color: #111827; font-weight: 700; line-height: 1.7; }
            .pricing-summary.empty { color: #6b7280; font-weight: 600; }
            .checkout-row { margin-top: 14px; display: flex; justify-content: flex-end; }
            .checkout-button { display: inline-flex; align-items: center; justify-content: center; padding: 12px 18px; background: #047857; color: #ffffff; border-radius: 8px; text-decoration: none; font-weight: 800; border: 0; cursor: pointer; }
            .checkout-button:hover { background: #065f46; }
            .checkout-button.disabled { background: #cbd5e1; color: #64748b; pointer-events: none; }
            
            .page-footer { background: #0f172a; color: #cbd5e1; padding: 22px 24px; }
            .footer-content { max-width: 1000px; margin: 0 auto; display: flex; flex-direction: column; gap: 8px; font-size: 14px; }
            @media (max-width: 768px) {
                .header-inner, .container, .footer-content { padding: 20px; }
                .upload-section { flex-direction: column; }
                .upload-box { min-width: 100%; }
            }
        </style>
    </head>
    <body>
        <header class="page-header">
            <div class="header-inner">
                <div>
                    <div class="brand">Mr-Student</div>
                    <p class="brand-subtitle">خدمات الطباعة والتجليد</p>
                </div>
                <div class="header-actions">
                    <span class="header-user">👤 {{ auth()->user()->name }}</span>
                    <a class="header-link" href="{{ route('orders.index') }}">🧾 طلباتي</a>
                    <a class="header-link" href="{{ route('account.settings') }}">⚙️ إعداداتي</a>
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.orders') }}">لوحة المدير</a>
                    @endif
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button class="logout-button" type="submit">خروج</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="container" id="services">
            <!-- Services Selection Screen -->
            <div id="servicesScreen" class="services-screen">
                <h2 style="margin-top: 0;">اختر الخدمة المطلوبة</h2>
                <button class="service-btn" onclick="selectService('notes')">📝 طباعة وتغليف المذكرات</button>
                <button class="service-btn" onclick="selectService('thesis')">📚 طبعة وتجليد رسالة ماجستير أو بحث تكميلي أو بحث تخرج</button>
                <button class="service-btn" onclick="selectService('phd')">🎓 طباعة وتجليد رسالة دكتوراه</button>
            </div>

            <!-- Upload Section for Notes -->
            <div id="uploadNotes" class="upload-content">
                <button class="back-button" onclick="backToServices()">← العودة للخدمات</button>
                <h2>تحميل ملفات مذكرات</h2>
                
                <div class="upload-section">
                    <div class="upload-box" id="notesWordBox">
                        <div class="file-icon">📄</div>
                        <h3>تحميل ملفات Word</h3>
                        <input type="file" id="notesWordFile" accept=".doc,.docx" multiple />
                        <p class="file-info">صيغ مدعومة: .doc, .docx</p>
                        <p class="file-info">حجم الملف: بدون حد أقصى</p>
                        <button class="upload-button" id="notesWordUploadBtn" onclick="document.getElementById('notesWordFile').click()">اختر ملفات</button>
                        <div class="progress-bar" id="notesWordProgress"><div class="progress-bar-fill"></div></div>
                        <div id="notesWordError" class="error-msg" style="display: none;"></div>
                    </div>

                    <div class="upload-box" id="notesPdfBox">
                        <div class="file-icon">📕</div>
                        <h3>تحميل ملفات PDF</h3>
                        <input type="file" id="notesPdfFile" accept=".pdf" multiple />
                        <p class="file-info">صيغ مدعومة: .pdf</p>
                        <p class="file-info">حجم الملف: بدون حد أقصى</p>
                        <button class="upload-button" id="notesPdfUploadBtn" onclick="document.getElementById('notesPdfFile').click()">اختر ملفات</button>
                        <div class="progress-bar" id="notesPdfProgress"><div class="progress-bar-fill"></div></div>
                        <div id="notesPdfError" class="error-msg" style="display: none;"></div>
                    </div>
                </div>

                <div style="margin-top: 40px;">
                    <h3 style="margin-bottom: 16px; color: #111827;">📄 ملفات Word المحملة</h3>
                    <div class="files-list">
                        <div class="files-list-header has-price">
                            <div>اسم الملف</div>
                            <div>الصفحات</div>
                            <div>الحجم</div>
                            <div>نوع التغليف</div>
                            <div>سعر الطباعة</div>
                            <div>سعر التغليف</div>
                            <div>الإجمالي</div>
                            <div>الحالة</div>
                            <div></div>
                        </div>
                        <div id="notesWordFilesList" class="empty-message">لم يتم تحميل أي ملفات</div>
                    </div>
                </div>

                <div style="margin-top: 40px; margin-bottom: 40px;">
                    <h3 style="margin-bottom: 16px; color: #111827;">📕 ملفات PDF المحملة</h3>
                    <div class="files-list">
                        <div class="files-list-header has-price">
                            <div>اسم الملف</div>
                            <div>الصفحات</div>
                            <div>الحجم</div>
                            <div>نوع التغليف</div>
                            <div>سعر الطباعة</div>
                            <div>سعر التغليف</div>
                            <div>الإجمالي</div>
                            <div>الحالة</div>
                            <div></div>
                        </div>
                        <div id="notesPdfFilesList" class="empty-message">لم يتم تحميل أي ملفات</div>
                    </div>
                </div>

                <!-- Binding Options for Notes -->
                <div class="binding-section">
                    <h3>إجمالي المذكرات</h3>
                    <div id="notesPricingSummary" class="pricing-summary empty">اختر نوع التغليف لكل ملف لعرض السعر.</div>
                </div>
            </div>

            <!-- Upload Section for Thesis -->
            <div id="uploadThesis" class="upload-content">
                <button class="back-button" onclick="backToServices()">← العودة للخدمات</button>
                <h2>تحميل ملفات رسالة ماجستير أو بحث</h2>
                
                <div class="upload-section">
                    <div class="upload-box" id="thesisWordBox">
                        <div class="file-icon">📄</div>
                        <h3>تحميل ملفات Word</h3>
                        <input type="file" id="thesisWordFile" accept=".doc,.docx" multiple />
                        <p class="file-info">صيغ مدعومة: .doc, .docx</p>
                        <p class="file-info">حجم الملف: بدون حد أقصى</p>
                        <button class="upload-button" id="thesisWordUploadBtn" onclick="document.getElementById('thesisWordFile').click()">اختر ملفات</button>
                        <div class="progress-bar" id="thesisWordProgress"><div class="progress-bar-fill"></div></div>
                        <div id="thesisWordError" class="error-msg" style="display: none;"></div>
                    </div>

                    <div class="upload-box" id="thesisPdfBox">
                        <div class="file-icon">📕</div>
                        <h3>تحميل ملفات PDF</h3>
                        <input type="file" id="thesisPdfFile" accept=".pdf" multiple />
                        <p class="file-info">صيغ مدعومة: .pdf</p>
                        <p class="file-info">حجم الملف: بدون حد أقصى</p>
                        <button class="upload-button" id="thesisPdfUploadBtn" onclick="document.getElementById('thesisPdfFile').click()">اختر ملفات</button>
                        <div class="progress-bar" id="thesisPdfProgress"><div class="progress-bar-fill"></div></div>
                        <div id="thesisPdfError" class="error-msg" style="display: none;"></div>
                    </div>
                </div>

                <div style="margin-top: 40px;">
                    <h3 style="margin-bottom: 16px; color: #111827;">📄 ملفات Word المحملة</h3>
                    <div class="files-list">
                        <div class="files-list-header has-copies-price">
                            <div>اسم الملف</div>
                            <div>الصفحات</div>
                            <div>الحجم</div>
                            <div>النسخ</div>
                            <div>سعر الطباعة</div>
                            <div>سعر التغليف</div>
                            <div>الإجمالي</div>
                            <div>الحالة</div>
                            <div></div>
                        </div>
                        <div id="thesisWordFilesList" class="empty-message">لم يتم تحميل أي ملفات</div>
                    </div>
                </div>

                <div style="margin-top: 40px; margin-bottom: 40px;">
                    <h3 style="margin-bottom: 16px; color: #111827;">📕 ملفات PDF المحملة</h3>
                    <div class="files-list">
                        <div class="files-list-header has-copies-price">
                            <div>اسم الملف</div>
                            <div>الصفحات</div>
                            <div>الحجم</div>
                            <div>النسخ</div>
                            <div>سعر الطباعة</div>
                            <div>سعر التغليف</div>
                            <div>الإجمالي</div>
                            <div>الحالة</div>
                            <div></div>
                        </div>
                        <div id="thesisPdfFilesList" class="empty-message">لم يتم تحميل أي ملفات</div>
                    </div>
                </div>

                <div class="binding-section">
                    <h3>إجمالي الماجستير</h3>
                    <div id="thesisPricingSummary" class="pricing-summary empty">ارفع الملفات لعرض الإجمالي.</div>
                </div>
            </div>

            <!-- Upload Section for PhD -->
            <div id="uploadPhd" class="upload-content">
                <button class="back-button" onclick="backToServices()">← العودة للخدمات</button>
                <h2>تحميل ملفات رسالة دكتوراه</h2>
                
                <div class="upload-section">
                    <div class="upload-box" id="phdWordBox">
                        <div class="file-icon">📄</div>
                        <h3>تحميل ملفات Word</h3>
                        <input type="file" id="phdWordFile" accept=".doc,.docx" multiple />
                        <p class="file-info">صيغ مدعومة: .doc, .docx</p>
                        <p class="file-info">حجم الملف: بدون حد أقصى</p>
                        <button class="upload-button" id="phdWordUploadBtn" onclick="document.getElementById('phdWordFile').click()">اختر ملفات</button>
                        <div class="progress-bar" id="phdWordProgress"><div class="progress-bar-fill"></div></div>
                        <div id="phdWordError" class="error-msg" style="display: none;"></div>
                    </div>

                    <div class="upload-box" id="phdPdfBox">
                        <div class="file-icon">📕</div>
                        <h3>تحميل ملفات PDF</h3>
                        <input type="file" id="phdPdfFile" accept=".pdf" multiple />
                        <p class="file-info">صيغ مدعومة: .pdf</p>
                        <p class="file-info">حجم الملف: بدون حد أقصى</p>
                        <button class="upload-button" id="phdPdfUploadBtn" onclick="document.getElementById('phdPdfFile').click()">اختر ملفات</button>
                        <div class="progress-bar" id="phdPdfProgress"><div class="progress-bar-fill"></div></div>
                        <div id="phdPdfError" class="error-msg" style="display: none;"></div>
                    </div>
                </div>

                <div style="margin-top: 40px;">
                    <h3 style="margin-bottom: 16px; color: #111827;">📄 ملفات Word المحملة</h3>
                    <div class="files-list">
                        <div class="files-list-header has-copies-price">
                            <div>اسم الملف</div>
                            <div>الصفحات</div>
                            <div>الحجم</div>
                            <div>النسخ</div>
                            <div>سعر الطباعة</div>
                            <div>سعر التغليف</div>
                            <div>الإجمالي</div>
                            <div>الحالة</div>
                            <div></div>
                        </div>
                        <div id="phdWordFilesList" class="empty-message">لم يتم تحميل أي ملفات</div>
                    </div>
                </div>

                <div style="margin-top: 40px; margin-bottom: 40px;">
                    <h3 style="margin-bottom: 16px; color: #111827;">📕 ملفات PDF المحملة</h3>
                    <div class="files-list">
                        <div class="files-list-header has-copies-price">
                            <div>اسم الملف</div>
                            <div>الصفحات</div>
                            <div>الحجم</div>
                            <div>النسخ</div>
                            <div>سعر الطباعة</div>
                            <div>سعر التغليف</div>
                            <div>الإجمالي</div>
                            <div>الحالة</div>
                            <div></div>
                        </div>
                        <div id="phdPdfFilesList" class="empty-message">لم يتم تحميل أي ملفات</div>
                    </div>
                </div>

                <div class="binding-section">
                    <h3>إجمالي الدكتوراه</h3>
                    <div id="phdPricingSummary" class="pricing-summary empty">ارفع الملفات لعرض الإجمالي.</div>
                </div>
            </div>
        </main>

        <script>
            // Store uploaded files for each service
            const uploadedFiles = {
                notes: { word: [], pdf: [] },
                thesis: { word: [], pdf: [] },
                phd: { word: [], pdf: [] }
            };
            const currentOrders = {
                notes: null,
                thesis: null,
                phd: null
            };

            const getCsrfToken = () => {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            };

            async function updateStoredFile(fileData, payload) {
                if (!fileData.id) return;

                try {
                    await fetch(`/order-files/${fileData.id}`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                } catch (error) {
                    console.error('Failed to update file details', error);
                }
            }

            const fileConfigs = {
                notesWord: { inputId: 'notesWordFile', boxId: 'notesWordBox', progressId: 'notesWordProgress', errorId: 'notesWordError', listId: 'notesWordFilesList', service: 'notes', type: 'word' },
                notesPdf: { inputId: 'notesPdfFile', boxId: 'notesPdfBox', progressId: 'notesPdfProgress', errorId: 'notesPdfError', listId: 'notesPdfFilesList', service: 'notes', type: 'pdf' },
                thesisWord: { inputId: 'thesisWordFile', boxId: 'thesisWordBox', progressId: 'thesisWordProgress', errorId: 'thesisWordError', listId: 'thesisWordFilesList', service: 'thesis', type: 'word' },
                thesisPdf: { inputId: 'thesisPdfFile', boxId: 'thesisPdfBox', progressId: 'thesisPdfProgress', errorId: 'thesisPdfError', listId: 'thesisPdfFilesList', service: 'thesis', type: 'pdf' },
                phdWord: { inputId: 'phdWordFile', boxId: 'phdWordBox', progressId: 'phdWordProgress', errorId: 'phdWordError', listId: 'phdWordFilesList', service: 'phd', type: 'word' },
                phdPdf: { inputId: 'phdPdfFile', boxId: 'phdPdfBox', progressId: 'phdPdfProgress', errorId: 'phdPdfError', listId: 'phdPdfFilesList', service: 'phd', type: 'pdf' }
            };

            const fileTypes = {
                word: { types: ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'], extensions: ['.doc', '.docx'] },
                pdf: { types: ['application/pdf'], extensions: ['.pdf'] }
            };

            function selectService(service) {
                document.getElementById('servicesScreen').style.display = 'none';
                document.getElementById('upload' + service.charAt(0).toUpperCase() + service.slice(1)).classList.add('active');
                initializeService(service);
            }

            function backToServices() {
                document.getElementById('servicesScreen').style.display = 'flex';
                document.getElementById('uploadNotes').classList.remove('active');
                document.getElementById('uploadThesis').classList.remove('active');
                document.getElementById('uploadPhd').classList.remove('active');
            }

            function initializeService(service) {
                const configKeys = Object.keys(fileConfigs).filter(key => fileConfigs[key].service === service);
                configKeys.forEach(key => setupFileUpload(key));
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            }

            function calculateNotesFilePrice(pages, binding) {
                const printPrice = Math.ceil(pages / 15);
                let bindingPrice = 0;
                let note = '';

                if (binding === 'normal') {
                    bindingPrice = 3;
                } else if (binding === 'wire') {
                    if (pages < 100) {
                        bindingPrice = 5;
                    } else if (pages < 300) {
                        bindingPrice = 7;
                    } else if (pages <= 600) {
                        bindingPrice = 9;
                    } else {
                        bindingPrice = 14;
                        note = 'الملف لازم يتقسم على ملفين';
                    }
                }

                return {
                    printPrice,
                    bindingPrice,
                    total: printPrice + bindingPrice,
                    note
                };
            }

            function getPrintPrice(pages) {
                return Math.ceil(pages / 15);
            }

            function calculateAcademicFilePrice(service, pages, copies) {
                const copyCount = Math.max(1, Number(copies) || 1);
                const printPrice = getPrintPrice(pages) * copyCount;
                const bindingSinglePrice = service === 'phd' ? 90 : 70;
                const bindingMultiPrice = service === 'phd' ? 70 : 55;
                const bindingPrice = copyCount === 1 ? bindingSinglePrice : bindingMultiPrice * copyCount;

                return {
                    printPrice,
                    bindingPrice,
                    total: printPrice + bindingPrice
                };
            }

            function getAllNotesFiles() {
                return [
                    ...uploadedFiles.notes.word,
                    ...uploadedFiles.notes.pdf
                ];
            }

            function getAllServiceFiles(service) {
                return [
                    ...uploadedFiles[service].word,
                    ...uploadedFiles[service].pdf
                ];
            }

            function renderCheckoutSummary(summary, service, message, totals = null, canCheckout = false) {
                const orderId = currentOrders[service];
                summary.classList.toggle('empty', !canCheckout);

                if (!canCheckout || !orderId || !totals) {
                    summary.innerHTML = `
                        <div>${message}</div>
                        <div class="checkout-row">
                            <span class="checkout-button disabled">إتمام الطلب</span>
                        </div>
                    `;
                    return;
                }

                summary.innerHTML = `
                    <div>سعر الطباعة: ${totals.print} ريال | سعر التغليف: ${totals.binding} ريال | الإجمالي: ${totals.total} ريال</div>
                    <div class="checkout-row">
                        <a class="checkout-button" href="/cart/${orderId}">إتمام الطلب</a>
                    </div>
                `;
            }

            function updateNotesPricingSummary() {
                const summary = document.getElementById('notesPricingSummary');
                if (!summary) return;

                const files = getAllNotesFiles();

                if (files.length === 0) {
                    renderCheckoutSummary(summary, 'notes', 'ارفع الملفات لعرض الإجمالي.');
                    return;
                }

                if (files.some(fileData => !fileData.binding)) {
                    renderCheckoutSummary(summary, 'notes', 'اختر نوع التغليف لكل ملف قبل إتمام الطلب.');
                    return;
                }

                const totals = files.reduce((sum, fileData) => {
                    const price = calculateNotesFilePrice(fileData.pages, fileData.binding);
                    sum.print += price.printPrice;
                    sum.binding += price.bindingPrice;
                    sum.total += price.total;
                    return sum;
                }, { print: 0, binding: 0, total: 0 });

                renderCheckoutSummary(summary, 'notes', '', totals, true);
            }

            function updateAcademicPricingSummary(service) {
                const summary = document.getElementById(`${service}PricingSummary`);
                if (!summary) return;

                const files = getAllServiceFiles(service);

                if (files.length === 0) {
                    renderCheckoutSummary(summary, service, 'ارفع الملفات لعرض الإجمالي.');
                    return;
                }

                const totals = files.reduce((sum, fileData) => {
                    const price = calculateAcademicFilePrice(service, fileData.pages, fileData.copies);
                    sum.print += price.printPrice;
                    sum.binding += price.bindingPrice;
                    sum.total += price.total;
                    return sum;
                }, { print: 0, binding: 0, total: 0 });

                renderCheckoutSummary(summary, service, '', totals, true);
            }

            async function countPDFPages(file) {
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const content = e.target.result;
                        const pageMatches = content.match(/\/Type\s*\/Page[^s]/g);
                        const pageCount = pageMatches ? pageMatches.length : 1;
                        resolve(pageCount);
                    };
                    reader.readAsText(file);
                });
            }

            async function countWordPages(file) {
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = async (e) => {
                        try {
                            const zip = new JSZip();
                            const docxData = await zip.loadAsync(e.target.result);
                            const docXml = await docxData.files['word/document.xml'].async('string');
                            const paragraphs = docXml.match(/<w:p>/g) || [];
                            const pageCount = Math.max(1, Math.ceil(paragraphs.length / 30));
                            resolve(pageCount);
                        } catch (error) {
                            resolve(1);
                        }
                    };
                    reader.readAsArrayBuffer(file);
                });
            }

            function updateFilesList(configKey) {
                const config = fileConfigs[configKey];
                const listDiv = document.getElementById(config.listId);
                const files = uploadedFiles[config.service][config.type];
                const showPrice = config.service === 'notes';
                const showAcademicPrice = config.service === 'thesis' || config.service === 'phd';

                if (files.length === 0) {
                    listDiv.innerHTML = '<div class="empty-message">لم يتم تحميل أي ملفات</div>';
                    if (showPrice) {
                        updateNotesPricingSummary();
                    } else if (showAcademicPrice) {
                        updateAcademicPricingSummary(config.service);
                    }
                    return;
                }

                let html = '';
                files.forEach((fileData, index) => {
                    const price = showPrice && fileData.binding ? calculateNotesFilePrice(fileData.pages, fileData.binding) : null;
                    const bindingHtml = showPrice
                        ? `
                            <div>
                                <select class="binding-select" required onchange="setNotesFileBinding('${config.type}', ${index}, this.value)">
                                    <option value="" ${!fileData.binding ? 'selected' : ''} disabled>اختر التغليف</option>
                                    <option value="tape" ${fileData.binding === 'tape' ? 'selected' : ''}>تغليف دبوس</option>
                                    <option value="wire" ${fileData.binding === 'wire' ? 'selected' : ''}>تغليف سلك</option>
                                    <option value="normal" ${fileData.binding === 'normal' ? 'selected' : ''}>تغليف عادي</option>
                                    <option value="none" ${fileData.binding === 'none' ? 'selected' : ''}>بدون أي تغليف</option>
                                </select>
                            </div>
                        `
                        : '';
                    const notesPrintPriceHtml = showPrice
                        ? `<div class="file-price">${price ? `${price.printPrice} ريال` : '-'}</div>`
                        : '';
                    const notesBindingPriceHtml = showPrice
                        ? `<div class="file-price">${price ? `${price.bindingPrice} ريال` : '-'}</div>`
                        : '';
                    const notesTotalPriceHtml = showPrice
                        ? `<div class="file-price">${price ? `${price.total} ريال` : 'اختر التغليف'}${price?.note ? `<span class="file-price-note">${price.note}</span>` : ''}</div>`
                        : '';
                    const academicPrice = showAcademicPrice ? calculateAcademicFilePrice(config.service, fileData.pages, fileData.copies) : null;
                    const copiesHtml = showAcademicPrice
                        ? `<div><input class="copies-input" type="number" min="1" step="1" value="${fileData.copies || 1}" onchange="setAcademicFileCopies('${config.service}', '${config.type}', ${index}, this.value)" /></div>`
                        : '';
                    const academicPrintPriceHtml = showAcademicPrice
                        ? `<div class="file-price">${academicPrice.printPrice} ريال</div>`
                        : '';
                    const academicBindingPriceHtml = showAcademicPrice
                        ? `<div class="file-price">${academicPrice.bindingPrice} ريال</div>`
                        : '';
                    const academicTotalPriceHtml = showAcademicPrice
                        ? `<div class="file-price">${academicPrice.total} ريال</div>`
                        : '';

                    html += `
                        <div class="files-list-item${showPrice ? ' has-price' : ''}${showAcademicPrice ? ' has-copies-price' : ''}">
                            <div class="file-name-cell">${fileData.filename}</div>
                            <div class="file-pages">${fileData.pages} صفحة</div>
                            <div class="file-size">${fileData.size}</div>
                            ${bindingHtml}
                            ${notesPrintPriceHtml}
                            ${notesBindingPriceHtml}
                            ${notesTotalPriceHtml}
                            ${copiesHtml}
                            ${academicPrintPriceHtml}
                            ${academicBindingPriceHtml}
                            ${academicTotalPriceHtml}
                            <div style="color: #047857; font-weight: 600;">✓ مرفوع</div>
                            <div class="file-remove" onclick="removeFile('${config.service}', '${config.type}', ${index})">حذف</div>
                        </div>
                    `;
                });

                listDiv.innerHTML = html;
                if (showPrice) {
                    updateNotesPricingSummary();
                } else if (showAcademicPrice) {
                    updateAcademicPricingSummary(config.service);
                }
            }

            async function deleteStoredFile(fileData) {
                if (!fileData.id) return;

                try {
                    await fetch(`/order-files/${fileData.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Accept': 'application/json'
                        }
                    });
                } catch (error) {
                    console.error('Failed to delete file', error);
                }
            }

            async function removeFile(service, type, index) {
                const fileData = uploadedFiles[service][type][index];
                await deleteStoredFile(fileData);
                uploadedFiles[service][type].splice(index, 1);
                const configKey = Object.keys(fileConfigs).find(key => fileConfigs[key].service === service && fileConfigs[key].type === type);
                updateFilesList(configKey);
                if (service === 'notes') {
                    updateNotesPricingSummary();
                } else if (service === 'thesis' || service === 'phd') {
                    updateAcademicPricingSummary(service);
                }
            }

            function setNotesFileBinding(type, index, binding) {
                const fileData = uploadedFiles.notes[type][index];
                fileData.binding = binding;
                const price = calculateNotesFilePrice(fileData.pages, binding);
                updateStoredFile(fileData, {
                    binding_type: binding,
                    print_price: price.printPrice,
                    binding_price: price.bindingPrice,
                    total_price: price.total
                });
                updateFilesList(`notes${type.charAt(0).toUpperCase() + type.slice(1)}`);
                updateNotesPricingSummary();
            }

            function setAcademicFileCopies(service, type, index, copies) {
                const fileData = uploadedFiles[service][type][index];
                fileData.copies = Math.max(1, Number(copies) || 1);
                const price = calculateAcademicFilePrice(service, fileData.pages, fileData.copies);
                updateStoredFile(fileData, {
                    copies: fileData.copies,
                    print_price: price.printPrice,
                    binding_price: price.bindingPrice,
                    total_price: price.total
                });
                updateFilesList(`${service}${type.charAt(0).toUpperCase() + type.slice(1)}`);
                updateAcademicPricingSummary(service);
            }

            function updateNotesFilesPricing() {
                updateFilesList('notesWord');
                updateFilesList('notesPdf');
            }

            function setupFileUpload(configKey) {
                const config = fileConfigs[configKey];
                const fileType = fileTypes[config.type];
                const input = document.getElementById(config.inputId);
                const box = document.getElementById(config.boxId);

                if (input.dataset.uploadReady === 'true') {
                    return;
                }
                input.dataset.uploadReady = 'true';

                input.addEventListener('change', function(e) {
                    const files = Array.from(e.target.files);
                    if (files.length > 0) {
                        uploadMultipleFiles(files, configKey);
                    }
                });

                box.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    box.classList.add('drag-over');
                });

                box.addEventListener('dragleave', () => {
                    box.classList.remove('drag-over');
                });

                box.addEventListener('drop', (e) => {
                    e.preventDefault();
                    box.classList.remove('drag-over');
                    const files = Array.from(e.dataTransfer.files);
                    if (files.length > 0) {
                        uploadMultipleFiles(files, configKey);
                    }
                });
            }

            async function uploadMultipleFiles(files, configKey) {
                const config = fileConfigs[configKey];
                const fileType = fileTypes[config.type];
                const errorDiv = document.getElementById(config.errorId);
                const progressDiv = document.getElementById(config.progressId);
                const progressBar = progressDiv.querySelector('.progress-bar-fill');
                const uploadBtn = document.getElementById(config.inputId.replace('File', 'UploadBtn'));

                const validFiles = files.filter(file => {
                    const isValidType = fileType.types.includes(file.type) || 
                                       fileType.extensions.some(ext => file.name.toLowerCase().endsWith(ext));
                    if (!isValidType) {
                        errorDiv.textContent = `${file.name}: صيغة غير صحيحة`;
                        errorDiv.style.display = 'block';
                        return false;
                    }
                    return true;
                });

                if (validFiles.length === 0) return;

                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
                uploadBtn.disabled = true;
                progressDiv.classList.add('active');

                let completed = 0;
                for (const file of validFiles) {
                    progressBar.style.width = ((completed / validFiles.length) * 100) + '%';
                    
                    let pageCount = 1;
                    try {
                        if (config.type === 'pdf') {
                            pageCount = await countPDFPages(file);
                        } else {
                            if (typeof JSZip !== 'undefined') {
                                pageCount = await countWordPages(file);
                            }
                        }
                    } catch (e) {
                        pageCount = 1;
                    }

                    await uploadFile(file, configKey, pageCount);
                    completed++;
                }

                progressBar.style.width = '100%';
                setTimeout(() => {
                    progressDiv.classList.remove('active');
                    uploadBtn.disabled = false;
                    document.getElementById(config.inputId).value = '';
                }, 500);
            }

            function uploadFile(file, configKey, pageCount) {
                return new Promise((resolve) => {
                    const config = fileConfigs[configKey];
                    const errorDiv = document.getElementById(config.errorId);
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('type', config.type);
                    formData.append('service', config.service);

                    const xhr = new XMLHttpRequest();

                    xhr.addEventListener('load', () => {
                        let response = {};
                        try {
                            response = JSON.parse(xhr.responseText);
                        } catch (error) {
                            response = {};
                        }

                        if (xhr.status === 200) {
                            if (response.success) {
                                currentOrders[config.service] = response.order_id;
                                uploadedFiles[config.service][config.type].push({
                                    id: response.file_id,
                                    filename: response.filename,
                                    pages: response.pages || pageCount,
                                    size: formatFileSize(response.size),
                                    binding: '',
                                    copies: 1
                                });
                                updateFilesList(configKey);
                            }
                        } else {
                            errorDiv.textContent = response.message || 'تعذر تحميل الملف';
                            errorDiv.style.display = 'block';
                        }
                        resolve();
                    });

                    xhr.addEventListener('error', () => {
                        errorDiv.textContent = 'تعذر الاتصال بالخادم أثناء تحميل الملف';
                        errorDiv.style.display = 'block';
                        resolve();
                    });

                    xhr.open('POST', '/upload-file');
                    xhr.setRequestHeader('X-CSRF-TOKEN', getCsrfToken());
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.send(formData);
                });
            }

            // Load JSZip library
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js';
            document.head.appendChild(script);
        </script>

        <footer class="page-footer" id="info">
            <div class="footer-content">
                <p>منصة متخصصة في خدمات الطباعة والتجليد للمذكرات والأبحاث والرسائل العلمية.</p>
                <p>&copy; 2026 خدمات الطباعة والتجليد. جميع الحقوق محفوظة.</p>
            </div>
        </footer>
    </body>
</html>
