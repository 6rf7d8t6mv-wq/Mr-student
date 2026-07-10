@extends('admin.layout')

@section('title', 'المستخدمين - لوحة المدير')

@section('content')
    <div class="page-title">
        <div>
            <h1>المستخدمين</h1>
            <p class="subtitle">إدارة المدراء بدون ازدحام داخل الجدول.</p>
        </div>
        <button class="save" type="button" onclick="openAdminModal('إضافة مدير', 'create-admin-template')">إضافة مدير</button>
    </div>

    <div class="panel">
        <form class="search-form auto-search-form" method="get" action="{{ route('admin.users') }}" style="margin-bottom: 14px;">
            <div style="flex: 1;">
                <label>بحث باسم المستخدم أو رقم الجوال</label>
                <input name="search" value="{{ $search }}" placeholder="اكتب الاسم أو رقم الجوال">
            </div>
            @if ($search !== '')
                <a class="ghost" href="{{ route('admin.users') }}">مسح</a>
            @endif
        </form>

        <table>
            <thead>
                <tr>
                    <th>المدير</th>
                    <th>رقم الجوال</th>
                    <th>الطلبات</th>
                    <th>الصلاحية</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>
                            <div class="identity">
                                <strong>{{ $user->name }}</strong>
                                <span class="id-badge">مدير {{ $loop->iteration }}</span>
                            </div>
                        </td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->orders_count }}</td>
                        <td><span class="badge">مدير</span></td>
                        <td>
                            <div class="actions">
                                <button class="ghost" type="button" onclick="openAdminModal('تعديل مدير', 'edit-admin-{{ $user->id }}')">تعديل</button>
                                @if (!auth()->user()->is($user))
                                    <form method="post" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('حذف هذا المدير؟')">
                                        @csrf
                                        @method('delete')
                                        <button class="danger small-button" type="submit">حذف</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">لا يوجد مدراء.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <template id="create-admin-template">
        <form method="post" action="{{ route('admin.users.store') }}">
            @csrf
            <input type="hidden" name="role" value="admin">
            <div class="form-grid">
                <div><label>الاسم</label><input name="name" required></div>
                <div><label>رقم الجوال</label><input name="phone" required></div>
                <div class="full"><label>كلمة المرور</label><input name="password" type="password" required></div>
            </div>
            <button class="save" type="submit">إضافة مدير</button>
        </form>
    </template>

    @foreach ($users as $user)
        <template id="edit-admin-{{ $user->id }}">
            <form method="post" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('patch')
                <input type="hidden" name="role" value="admin">
                <div class="form-grid">
                    <div><label>الاسم</label><input name="name" value="{{ $user->name }}" required></div>
                    <div><label>رقم الجوال</label><input name="phone" value="{{ $user->phone }}" required></div>
                    <div class="full"><label>كلمة مرور جديدة</label><input name="password" type="password" placeholder="اتركها فارغة بدون تغيير"></div>
                </div>
                <button class="save" type="submit">حفظ التعديل</button>
            </form>
        </template>
    @endforeach
@endsection
