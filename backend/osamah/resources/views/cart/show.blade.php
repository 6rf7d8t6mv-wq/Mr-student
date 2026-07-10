<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>السلة والدفع</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f3f4f6; color: #111827; }
        .header { background: #0f172a; color: #ffffff; padding: 18px 24px; }
        .header-inner { max-width: 1040px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .brand { font-size: 22px; font-weight: 900; }
        .home-button { color: #0f172a; background: #ffffff; text-decoration: none; font-weight: 800; padding: 10px 14px; border-radius: 9px; }
        main { max-width: 1040px; margin: 28px auto; padding: 0 20px; }
        .notice, .errors { margin-bottom: 18px; padding: 12px 14px; border-radius: 8px; font-weight: 800; }
        .notice { background: #ecfdf5; color: #047857; }
        .errors { background: #fef2f2; color: #b91c1c; }
        .panel { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 22px; box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08); margin-bottom: 18px; }
        h1 { margin: 0 0 8px; font-size: 30px; }
        h2 { margin: 0 0 16px; font-size: 22px; color: #0f172a; }
        p { margin: 0 0 18px; color: #64748b; line-height: 1.7; }
        .meta { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-top: 18px; }
        .meta-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; }
        .meta-card span { display: block; color: #64748b; font-size: 12px; font-weight: 800; margin-bottom: 6px; }
        .meta-card strong { font-size: 16px; }
        table { width: 100%; border-collapse: collapse; overflow: hidden; border-radius: 10px; }
        th, td { text-align: right; padding: 12px; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        th { background: #f8fafc; color: #334155; font-weight: 900; }
        .totals { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        .total-card { background: #0f172a; color: #ffffff; border-radius: 10px; padding: 16px; }
        .total-card span { display: block; color: #cbd5e1; font-size: 13px; margin-bottom: 8px; }
        .total-card strong { font-size: 24px; }
        .payment-options { display: grid; grid-template-columns: 1fr 1.4fr; gap: 16px; align-items: start; }
        .pay-card { border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; background: #f8fafc; }
        .apple-pay { width: 100%; background: #000000; color: #ffffff; border: 0; border-radius: 9px; padding: 14px; font-size: 17px; font-weight: 900; cursor: pointer; }
        label { display: block; color: #334155; font-weight: 800; font-size: 13px; margin: 12px 0 6px; }
        input { width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #cbd5e1; border-radius: 9px; font-size: 15px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .full { grid-column: 1 / -1; }
        .submit-card { width: 100%; margin-top: 16px; padding: 13px 16px; border: 0; border-radius: 9px; background: #0f172a; color: #ffffff; font-weight: 900; cursor: pointer; }
        .paid { background: #ecfdf5; color: #047857; padding: 14px; border-radius: 10px; font-weight: 900; }
        @media (max-width: 820px) {
            .header-inner, .payment-options { flex-direction: column; grid-template-columns: 1fr; align-items: stretch; }
            .meta, .totals, .form-grid { grid-template-columns: 1fr; }
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
        @if (session('status'))
            <div class="notice">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors">{{ $errors->first() }}</div>
        @endif

        <section class="panel">
            <h1>السلة والدفع</h1>
            <p>راجع الطلب قبل الدفع. لا يوجد دفع عند الاستلام أو كاش.</p>
            <div class="meta">
                <div class="meta-card"><span>رقم الطلب</span><strong>#{{ $order->id }}</strong></div>
                <div class="meta-card"><span>الخدمة</span><strong>{{ ['notes' => 'مذكرات', 'thesis' => 'ماجستير', 'phd' => 'دكتوراه'][$order->service_type] ?? $order->service_type }}</strong></div>
                <div class="meta-card"><span>حالة الطلب</span><strong>{{ $order->status }}</strong></div>
                <div class="meta-card"><span>الدفع</span><strong>{{ $order->payment_status === 'paid' ? 'مدفوع' : 'غير مدفوع' }}</strong></div>
            </div>
        </section>

        <section class="panel">
            <h2>الملفات</h2>
            <table>
                <thead>
                    <tr>
                        <th>الملف</th>
                        <th>النوع</th>
                        <th>الصفحات</th>
                        <th>النسخ</th>
                        <th>التغليف</th>
                        <th>الطباعة</th>
                        <th>التغليف</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->files as $file)
                        <tr>
                            <td>{{ $file->original_name }}</td>
                            <td>{{ strtoupper($file->file_type) }}</td>
                            <td>{{ $file->pages }}</td>
                            <td>{{ $file->copies }}</td>
                            <td>{{ ['tape' => 'تغليف دبوس', 'wire' => 'تغليف سلك', 'normal' => 'تغليف عادي', 'none' => 'بدون تغليف'][$file->binding_type] ?? '-' }}</td>
                            <td>{{ $file->print_price }} ريال</td>
                            <td>{{ $file->binding_price }} ريال</td>
                            <td>{{ $file->total_price }} ريال</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="panel">
            <h2>الإجمالي</h2>
            <div class="totals">
                <div class="total-card"><span>سعر الطباعة</span><strong>{{ $order->print_total }} ريال</strong></div>
                <div class="total-card"><span>سعر التغليف</span><strong>{{ $order->binding_total }} ريال</strong></div>
                <div class="total-card"><span>الإجمالي</span><strong>{{ $order->grand_total }} ريال</strong></div>
            </div>
        </section>

        <section class="panel">
            <h2>الدفع</h2>
            @if ($order->payment_status === 'paid')
                <div class="paid">تم الدفع بنجاح. رقم العملية: {{ $order->payment_reference }}</div>
            @else
                <div class="payment-options">
                    <div class="pay-card">
                        <h2>Apple Pay</h2>
                        <p>اعتماد الطلب والدفع عبر Apple Pay.</p>
                        <form method="post" action="{{ route('cart.pay', $order) }}">
                            @csrf
                            <input type="hidden" name="payment_method" value="apple_pay">
                            <button class="apple-pay" type="submit">Apple Pay</button>
                        </form>
                    </div>

                    <div class="pay-card">
                        <h2>بطاقة بنكية</h2>
                        <form method="post" action="{{ route('cart.pay', $order) }}">
                            @csrf
                            <input type="hidden" name="payment_method" value="card">
                            <div class="form-grid">
                                <div class="full">
                                    <label>اسم حامل البطاقة</label>
                                    <input name="card_name" autocomplete="cc-name" required>
                                </div>
                                <div class="full">
                                    <label>رقم البطاقة</label>
                                    <input name="card_number" inputmode="numeric" autocomplete="cc-number" placeholder="0000 0000 0000 0000" required>
                                </div>
                                <div>
                                    <label>تاريخ الانتهاء</label>
                                    <input name="card_expiry" placeholder="MM/YY" autocomplete="cc-exp" required>
                                </div>
                                <div>
                                    <label>CVV</label>
                                    <input name="card_cvc" inputmode="numeric" autocomplete="cc-csc" required>
                                </div>
                            </div>
                            <button class="submit-card" type="submit">دفع واعتماد الطلب</button>
                        </form>
                    </div>
                </div>
            @endif
        </section>
    </main>
</body>
</html>
