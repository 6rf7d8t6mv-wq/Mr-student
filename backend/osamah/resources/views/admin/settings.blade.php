@extends('admin.layout')

@section('title', 'الإعدادات - لوحة المدير')

@section('content')
    <div class="page-title">
        <div>
            <h1>الإعدادات</h1>
            <p class="subtitle">تعديل بيانات حساب المدير الحالي.</p>
        </div>
    </div>

    <div class="panel">
        <form method="post" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('patch')
            <div class="form-grid">
                <div><label>اسمك</label><input name="name" value="{{ auth()->user()->name }}" required></div>
                <div><label>رقم هاتفك</label><input name="phone" value="{{ auth()->user()->phone }}" required></div>
                <div class="full"><label>كلمة مرور جديدة</label><input name="password" type="password" placeholder="اتركها فارغة بدون تغيير"></div>
            </div>
            <button class="save" type="submit">حفظ الإعدادات</button>
        </form>
    </div>
@endsection
