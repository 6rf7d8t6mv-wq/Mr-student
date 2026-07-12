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
            <div class="form-section">
                <h3 class="form-section-title">بيانات المدير</h3>
                <div class="form-grid">
                    <div><label>اسمك</label><input name="name" value="{{ auth()->user()->name }}" required></div>
                    <div><label>رقم هاتفك</label><input name="phone" value="{{ auth()->user()->phone }}" required></div>
                </div>
            </div>
            <div class="form-section">
                <h3 class="form-section-title">تغيير كلمة المرور</h3>
                <p class="form-note">اترك حقول كلمة المرور فارغة إذا ما تبغى تغيرها.</p>
                <div class="form-grid">
                    <div class="full"><label>كلمة المرور الحالية</label><input name="current_password" type="password" placeholder="مطلوبة عند تغيير كلمة المرور"></div>
                    <div><label>كلمة المرور الجديدة</label><input name="password" type="password" placeholder="كلمة مرور جديدة"></div>
                    <div><label>تأكيد كلمة المرور الجديدة</label><input name="password_confirmation" type="password" placeholder="تأكيد كلمة المرور"></div>
                </div>
            </div>
            <button class="save" type="submit">حفظ الإعدادات</button>
        </form>
    </div>
@endsection
