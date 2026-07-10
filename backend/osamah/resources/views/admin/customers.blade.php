@extends('admin.layout')

@section('title', 'العملاء - لوحة المدير')

@section('content')
    <div class="page-title">
        <div>
            <h1>العملاء</h1>
            <p class="subtitle">قائمة العملاء مع تعديل من نافذة مستقلة.</p>
        </div>
        <button class="save" type="button" onclick="openAdminModal('إضافة عميل', 'create-customer-template')">إضافة عميل</button>
    </div>

    <div class="panel">
        <form class="search-form auto-search-form" method="get" action="{{ route('admin.customers') }}" style="margin-bottom: 14px;">
            <div style="flex: 1;">
                <label>بحث باسم العميل أو رقم الجوال</label>
                <input name="search" value="{{ $search }}" placeholder="اكتب الاسم أو رقم الجوال">
            </div>
            @if ($search !== '')
                <a class="ghost" href="{{ route('admin.customers') }}">مسح</a>
            @endif
        </form>

        <table>
            <thead>
                <tr>
                    <th>العميل</th>
                    <th>رقم الجوال</th>
                    <th>الطلبات</th>
                    <th>الصلاحية</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td>
                            <div class="identity">
                                <strong>{{ $customer->name }}</strong>
                                <span class="id-badge">عميل {{ $loop->iteration }}</span>
                            </div>
                        </td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->orders_count }}</td>
                        <td><span class="badge">عميل</span></td>
                        <td>
                            <div class="actions">
                                <button class="ghost" type="button" onclick="openAdminModal('تعديل عميل', 'edit-customer-{{ $customer->id }}')">تعديل</button>
                                <form method="post" action="{{ route('admin.users.destroy', $customer) }}" onsubmit="return confirm('حذف هذا العميل؟ سيتم حذف طلباته وملفاته من قاعدة البيانات.')">
                                    @csrf
                                    @method('delete')
                                    <button class="danger small-button" type="submit">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">لا يوجد عملاء.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <template id="create-customer-template">
        <form method="post" action="{{ route('admin.users.store') }}">
            @csrf
            <input type="hidden" name="role" value="customer">
            <div class="form-grid">
                <div><label>الاسم</label><input name="name" required></div>
                <div><label>رقم الجوال</label><input name="phone" required></div>
                <div class="full"><label>كلمة المرور</label><input name="password" type="password" required></div>
            </div>
            <button class="save" type="submit">إضافة عميل</button>
        </form>
    </template>

    @foreach ($customers as $customer)
        <template id="edit-customer-{{ $customer->id }}">
            <form method="post" action="{{ route('admin.users.update', $customer) }}">
                @csrf
                @method('patch')
                <input type="hidden" name="role" value="customer">
                <div class="form-grid">
                    <div><label>الاسم</label><input name="name" value="{{ $customer->name }}" required></div>
                    <div><label>رقم الجوال</label><input name="phone" value="{{ $customer->phone }}" required></div>
                    <div class="full"><label>كلمة مرور جديدة</label><input name="password" type="password" placeholder="اتركها فارغة بدون تغيير"></div>
                </div>
                <button class="save" type="submit">حفظ التعديل</button>
            </form>
        </template>
    @endforeach
@endsection
