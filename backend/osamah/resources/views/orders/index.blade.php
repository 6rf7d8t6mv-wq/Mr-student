<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>طلباتي</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f3f4f6; color: #111827; }
        .header { background: #0f172a; color: #ffffff; padding: 18px 24px; }
        .header-inner { max-width: 1040px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .brand { font-size: 22px; font-weight: 900; }
        .home-button { color: #0f172a; background: #ffffff; text-decoration: none; font-weight: 800; padding: 10px 14px; border-radius: 9px; }
        main { max-width: 1040px; margin: 28px auto; padding: 0 20px; }
        .panel { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 22px; box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08); }
        .page-title { display: flex; justify-content: space-between; align-items: center; gap: 14px; margin-bottom: 18px; }
        h1 { margin: 0 0 8px; font-size: 30px; }
        p { margin: 0; color: #64748b; line-height: 1.7; }
        table { width: 100%; border-collapse: collapse; overflow: hidden; border-radius: 10px; }
        th, td { text-align: right; padding: 13px 12px; border-bottom: 1px solid #e5e7eb; font-size: 14px; vertical-align: middle; }
        th { background: #f8fafc; color: #334155; font-weight: 900; }
        .badge { display: inline-flex; align-items: center; justify-content: center; padding: 6px 10px; border-radius: 999px; font-weight: 900; font-size: 12px; white-space: nowrap; }
        .paid { background: #dcfce7; color: #166534; }
        .unpaid { background: #fef3c7; color: #92400e; }
        .done { background: #e0f2fe; color: #075985; }
        .open { background: #f1f5f9; color: #334155; }
        .cancelled { background: #fee2e2; color: #991b1b; }
        .action { display: inline-flex; color: #ffffff; background: #0f172a; text-decoration: none; font-weight: 900; padding: 9px 12px; border-radius: 8px; }
        .action.secondary { background: #047857; }
        .empty { text-align: center; color: #94a3b8; padding: 38px 16px; font-weight: 800; }
        @media (max-width: 820px) {
            .header-inner, .page-title { align-items: flex-start; flex-direction: column; }
            table { display: block; overflow-x: auto; white-space: nowrap; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-inner">
            <div class="brand">Mr-Student</div>
            <a class="home-button" href="{{ route('home') }}">العودة للصفحة الرئيسية</a>
        </div>
    </header>

    <main>
        <section class="panel">
            <div class="page-title">
                <div>
                    <h1>طلباتي</h1>
                    <p>تابع حالة طلباتك، الدفع، وهل اكتمل الطلب أو ما زال قيد التنفيذ.</p>
                </div>
            </div>

            @if ($orders->isEmpty())
                <div class="empty">لا توجد طلبات حتى الآن.</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>الخدمة</th>
                            <th>الملفات</th>
                            <th>حالة الدفع</th>
                            <th>حالة الطلب</th>
                            <th>الإجمالي</th>
                            <th>التاريخ</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            @php
                                $serviceNames = ['notes' => 'مذكرات', 'thesis' => 'ماجستير', 'phd' => 'دكتوراه'];
                                $statusNames = [
                                    'new' => 'بانتظار الدفع',
                                    'reviewing' => 'قيد المراجعة',
                                    'priced' => 'تم التسعير',
                                    'processing' => 'قيد التنفيذ',
                                    'completed' => 'مكتمل',
                                    'cancelled' => 'ملغي',
                                ];
                                $isPaid = $order->payment_status === 'paid';
                                $isCompleted = $order->status === 'completed';
                                $isCancelled = $order->status === 'cancelled';
                            @endphp
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $serviceNames[$order->service_type] ?? $order->service_type }}</td>
                                <td>{{ $order->files_count }}</td>
                                <td>
                                    <span class="badge {{ $isPaid ? 'paid' : 'unpaid' }}">
                                        {{ $isPaid ? 'مدفوع' : 'غير مدفوع' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $isCompleted ? 'done' : ($isCancelled ? 'cancelled' : 'open') }}">
                                        {{ $statusNames[$order->status] ?? $order->status }}
                                    </span>
                                </td>
                                <td>{{ $order->grand_total }} ريال</td>
                                <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                <td>
                                    @if (! $isPaid && $order->files_count > 0)
                                        <a class="action secondary" href="{{ route('cart.show', $order) }}">إكمال الدفع</a>
                                    @else
                                        <a class="action" href="{{ route('cart.show', $order) }}">عرض الطلب</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </main>
</body>
</html>
