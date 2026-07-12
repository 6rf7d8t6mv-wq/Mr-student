@extends('admin.layout')

@section('title', 'الطلبات - لوحة المدير')

@section('content')
    <div class="page-title">
        <div>
            <h1>الطلبات</h1>
            <p class="subtitle">متابعة طلبات العملاء والملفات المرفوعة والأسعار.</p>
        </div>
    </div>

    <div class="panel">
        <form class="search-form auto-search-form" method="get" action="{{ route('admin.orders') }}">
            <div style="flex: 1;">
                <label>ابحث برقم الطلب أو رقم الجوال أو اسم العميل</label>
                <input name="search" value="{{ $search }}" placeholder="مثال: 12 أو 0500000000 أو محمد">
            </div>
            @if ($search !== '')
                <a class="ghost" href="{{ route('admin.orders') }}">مسح</a>
            @endif
        </form>
    </div>

    @forelse ($orders as $order)
        @php
            $bindingLabel = $order->service_type === 'notes'
                ? 'التغليف'
                : ($order->service_type === 'formatting' ? 'التنسيق' : ($order->service_type === 'research' ? 'إنشاء البحث' : 'التجليد'));
            $bindingPriceLabel = $order->service_type === 'notes'
                ? 'سعر التغليف'
                : ($order->service_type === 'formatting' ? 'سعر التنسيق' : ($order->service_type === 'research' ? 'سعر إنشاء البحث' : 'سعر التجليد'));
            $noPrintServices = ['formatting', 'research'];
            $bindingNames = [
                'tape' => $order->service_type === 'notes' ? 'تغليف دبوس' : 'تجليد دبوس',
                'wire' => $order->service_type === 'notes' ? 'تغليف سلك' : 'تجليد سلك',
                'normal' => $order->service_type === 'notes' ? 'تغليف عادي' : 'تجليد عادي',
                'none' => $order->service_type === 'notes' ? 'بدون تغليف' : 'بدون تجليد',
            ];
            $orderDotColor = in_array($order->status, ['completed', 'finished'], true)
                ? 'green'
                : (blank($order->admin_opened_at) ? 'red' : 'yellow');
            $dayNames = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
            $createdAtText = $dayNames[$order->created_at->dayOfWeek] . ' - ' . $order->created_at->format('Y-m-d H:i');
        @endphp
        <div class="order" data-order-id="{{ $order->id }}">
            <div class="order-head">
                <div><span class="label">رقم الطلب</span><span class="tiny-status-dot {{ $orderDotColor }}" data-order-status-dot></span>#{{ $order->id }}</div>
                <div><span class="label">العميل</span>{{ $order->user->name }} - {{ $order->user->phone }}</div>
                <div><span class="label">تاريخ إنشاء الطلب</span>{{ $createdAtText }}</div>
                <div><span class="label">الخدمة</span>{{ ['notes' => 'مذكرات', 'thesis' => 'ماجستير', 'phd' => 'دكتوراه', 'formatting' => 'تنسيق الرسائل الجامعية', 'research' => 'إنشاء بحث'][$order->service_type] ?? $order->service_type }}</div>
                <div><span class="label">الحالة</span>{{ $order->status }}</div>
                <div><span class="label">الدفع</span>{{ $order->payment_status === 'paid' ? 'مدفوع' : 'غير مدفوع' }}{{ $order->payment_method ? ' - ' . (['apple_pay' => 'Apple Pay', 'card' => 'بطاقة'][$order->payment_method] ?? $order->payment_method) : '' }}</div>
                <div><span class="label">الإجمالي</span>
                    @if (in_array($order->service_type, $noPrintServices, true))
                        {{ $bindingLabel }} {{ $order->binding_total }} | الكل {{ $order->grand_total }} ريال
                    @else
                        طباعة {{ $order->print_total }} | {{ $bindingLabel }} {{ $order->binding_total }} | الكل {{ $order->grand_total }} ريال
                    @endif
                </div>
                @if (auth()->user()->hasAdminPermission('orders_delete'))
                    <div>
                        <span class="label">إجراء</span>
                        <form method="post" action="{{ route('admin.orders.destroy', $order) }}" onsubmit="return confirm('حذف هذا الطلب وجميع ملفاته؟')">
                            @csrf
                            @method('delete')
                            <button class="danger small-button" type="submit">حذف الطلب</button>
                        </form>
                    </div>
                @endif
            </div>

            <table>
                <thead>
                    <tr>
                        <th>الملف</th>
                        @if ($order->service_type !== 'research')
                            <th>النوع</th>
                        @endif
                        @if (in_array($order->service_type, ['thesis', 'phd'], true))
                            <th>الجامعة/المعهد</th>
                        @endif
                        <th>الصفحات</th>
                        @if ($order->service_type !== 'research')
                            <th>النسخ</th>
                        @endif
                        @if (! in_array($order->service_type, $noPrintServices, true))
                            <th>{{ $bindingLabel }}</th>
                        @endif
                        @if (! in_array($order->service_type, $noPrintServices, true))
                            <th>سعر الطباعة</th>
                        @endif
                        <th>{{ $bindingPriceLabel }}</th>
                        <th>الإجمالي</th>
                        <th>تحميل</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->files as $file)
                        <tr>
                            <td>{{ $file->original_name }}</td>
                            @if ($order->service_type !== 'research')
                                <td>{{ strtoupper($file->file_type) }}</td>
                            @endif
                            @if (in_array($order->service_type, ['thesis', 'phd'], true))
                                <td>{{ $file->university_name ?: '-' }}</td>
                            @endif
                            <td>{{ $file->pages }}</td>
                            @if ($order->service_type !== 'research')
                                <td>{{ $file->copies }}</td>
                            @endif
                            @if (! in_array($order->service_type, $noPrintServices, true))
                                <td>{{ $bindingNames[$file->binding_type] ?? '-' }}</td>
                            @endif
                            @if (! in_array($order->service_type, $noPrintServices, true))
                                <td>{{ $file->print_price }} ريال</td>
                            @endif
                            <td>{{ $file->binding_price }} ريال</td>
                            <td>{{ $file->total_price }} ريال</td>
                            <td>
                                @if ($order->service_type === 'research')
                                    -
                                @else
                                    <a class="save small-button" href="{{ route('admin.files.download', $file) }}" data-complete-order-download>تنزيل الملف</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if (in_array($order->service_type, $noPrintServices, true))
                <div class="panel" style="margin: 14px; background: #f8fafc;">
                    <h2 style="margin-bottom: 10px;">ملفات التسليم للعميل</h2>
                    @if ($order->deliveredFiles->isNotEmpty())
                        <div class="delivered-files-list">
                            @foreach ($order->deliveredFiles as $deliveredFile)
                                <div class="delivered-file-item">
                                    <div>
                                        <div class="delivered-file-name">{{ $deliveredFile->original_name }}</div>
                                        <div class="muted">{{ $deliveredFile->created_at->format('Y-m-d H:i') }}</div>
                                    </div>
                                    <div class="delivered-file-actions">
                                        <a class="ghost" href="{{ route('admin.delivered-files.download', ['deliveredFile' => $deliveredFile, 'view' => 1]) }}" target="_blank" rel="noopener">عرض</a>
                                        <a class="save small-button" href="{{ route('admin.delivered-files.download', $deliveredFile) }}">تحميل</a>
                                        <form method="post" action="{{ route('admin.delivered-files.destroy', $deliveredFile) }}" onsubmit="return confirm('حذف ملف التسليم هذا؟')">
                                            @csrf
                                            @method('delete')
                                            <button class="danger small-button" type="submit">حذف</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="muted" style="margin: 0 0 10px;">لم يتم إرفاق ملف التسليم بعد. لن يظهر زر التحميل للعميل إلا بعد رفع الملف.</p>
                    @endif
                    <form method="post" action="{{ route('admin.orders.delivered-file.upload', $order) }}" enctype="multipart/form-data">
                        @csrf
                        <label>إضافة ملف تسليم جديد</label>
                        <input type="file" name="delivered_file" required>
                        <button class="save" type="submit">حفظ ملف التسليم</button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div class="panel empty">لا توجد طلبات حتى الآن.</div>
    @endforelse
@endsection
