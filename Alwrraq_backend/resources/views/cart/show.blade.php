<!DOCTYPE html>
<html lang="{{ session('ui_locale', 'ar') === 'en' ? 'en' : 'ar' }}" dir="{{ session('ui_locale', 'ar') === 'en' ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ request()->routeIs('cart.payment') ? 'صفحة الدفع' : 'السلة' }}</title>
    <style>
        * { box-sizing: border-box; }
        :root { --sidebar-width: clamp(180px, 20vw, 240px); --page-gap: clamp(14px, 3vw, 40px); }
        body { margin: 0; padding: 0 calc(var(--sidebar-width) + var(--page-gap)) 0 var(--page-gap); font-family: Arial, sans-serif; background: #f3f4f6; color: #111827; }
        .header { width: var(--sidebar-width); min-height: 100vh; max-height: 100vh; overflow-y: auto; background: #0f172a; color: #ffffff; padding: clamp(16px, 2vw, 24px) clamp(12px, 1.6vw, 18px); position: fixed; top: 0; right: 0; z-index: 20; box-shadow: -10px 0 30px rgba(15, 23, 42, 0.15); }
        .header-inner { height: 100%; display: flex; flex-direction: column; justify-content: flex-start; align-items: stretch; gap: 0; }
        .brand { font-size: clamp(18px, 2vw, 24px); font-weight: 700; letter-spacing: 0.02em; overflow-wrap: anywhere; margin-bottom: 4px; }
        .brand-logo { width: 46px; height: 46px; border-radius: 14px; object-fit: cover; background: #ffffff; border: 1px solid rgba(255,255,255,0.18); box-shadow: 0 12px 26px rgba(0,0,0,0.18); margin-bottom: 10px; display: block; }
        .header-actions { display: flex; flex-direction: column; align-items: stretch; gap: clamp(8px, 1.2vw, 12px); color: #cbd5e1; font-size: clamp(12px, 1.15vw, 14px); margin-top: 24px; }
        .header-actions a { color: #f8fafc; text-decoration: none; }
        .header-user { display: block; color: #cbd5e1; font-size: clamp(12px, 1.15vw, 14px); margin: 0 0 12px; line-height: 1.6; }
        .home-button { display: flex; align-items: center; gap: 8px; width: 100%; color: #f8fafc; background: rgba(255, 255, 255, 0.06); text-decoration: none; font-weight: 800; padding: 10px 12px; border-radius: 10px; border: 1px solid transparent; text-align: right; line-height: 1.5; }
        .home-button:hover { background: #1e293b; border-color: #334155; }
        .settings-button { display: flex; align-items: center; gap: 8px; width: 100%; color: #ffffff; background: #0f4c81; text-decoration: none; font-weight: 800; padding: 10px 12px; border-radius: 10px; border: 1px solid rgba(96, 165, 250, 0.35); text-align: right; line-height: 1.5; }
        .settings-button:hover { background: #1d6fa5; border-color: #60a5fa; }
        .header-form { margin: 0; }
        .logout-button { width: 100%; color: #ffffff; background: #b91c1c; border: 1px solid rgba(248, 113, 113, 0.5); font-weight: 800; padding: 10px 12px; border-radius: 10px; text-align: center; line-height: 1.5; cursor: pointer; }
        .logout-button:hover { background: #dc2626; border-color: #f87171; }
        .mobile-menu-toggle { display: none; align-items: center; justify-content: center; gap: 7px; border: 1px solid rgba(148, 163, 184, 0.28); border-radius: 10px; background: rgba(255, 255, 255, 0.08); color: #ffffff; padding: 9px 11px; font-weight: 900; font-family: inherit; cursor: pointer; }
        main { width: min(1180px, 100%); margin: clamp(16px, 4vw, 28px) auto; padding: 0 clamp(12px, 4vw, 20px); }
        .notice, .errors { margin-bottom: 18px; padding: 12px 14px; border-radius: 8px; font-weight: 800; }
        .notice { background: #ecfdf5; color: #047857; }
        .errors { background: #fef2f2; color: #b91c1c; }
        .panel { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: clamp(16px, 4vw, 22px); box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08); margin-bottom: 18px; }
        body.cart-index main { margin-top: 8px; }
        body.cart-index .panel:first-child { padding: 0; background: transparent; border: 0; box-shadow: none; }
        h1 { margin: 0 0 8px; font-size: clamp(24px, 6vw, 30px); }
        h2 { margin: 0 0 16px; font-size: clamp(19px, 4.5vw, 22px); color: #0f172a; }
        p { margin: 0 0 18px; color: #64748b; line-height: 1.7; }
        .meta { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-top: 18px; }
        .meta-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; }
        .meta-card.full { grid-column: 1 / -1; }
        .meta-card span { display: block; color: #64748b; font-size: 12px; font-weight: 800; margin-bottom: 6px; }
        .meta-card strong { font-size: 16px; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; overflow: hidden; border-radius: 10px; }
        th, td { text-align: right; padding: 12px; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        th { background: #f8fafc; color: #334155; font-weight: 900; }
        .files-panel { width: 100%; padding: 16px; box-sizing: border-box; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; }
        .detail-table-wrap { width: 100%; overflow-x: auto; border: 1px solid #e5e7eb; border-radius: 10px; background: #ffffff; }
        .detail-table-wrap table { width: 100%; min-width: 720px; table-layout: auto; }
        .detail-table-wrap th,
        .detail-table-wrap td { padding: 11px 10px; white-space: normal; }
        .detail-table-wrap td:first-child { min-width: 180px; word-break: break-word; }
        .price-cell { color: #0f172a; font-weight: 900; white-space: nowrap; background: #f8fafc; }
        .totals { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        .total-card { background: #0f172a; color: #ffffff; border-radius: 10px; padding: 16px; }
        .total-card span { display: block; color: #cbd5e1; font-size: 13px; margin-bottom: 8px; }
        .total-card strong { font-size: 24px; }
        .payment-heading-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 10px; }
        .payment-heading-row h2 { margin: 0; }
        .payment-options { display: grid; grid-template-columns: 0.9fr 1.25fr; gap: 8px; align-items: start; }
        .pay-card { border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px; background: #f8fafc; }
        .pay-card h2 { margin: 0 0 8px; font-size: 16px; }
        .wallet-heading-row { display: grid; grid-template-columns: auto minmax(0, 1fr); align-items: center; gap: 8px; }
        .wallet-heading-row h2 { margin: 0; white-space: nowrap; }
        .apple-pay { width: 100%; background: #000000; color: #ffffff; border: 0; border-radius: 8px; padding: 9px 8px; font-size: 13px; font-weight: 900; cursor: pointer; }
        .wallet-buttons { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 6px; }
        .wallet-buttons form { min-width: 0; }
        .google-pay { width: 100%; background: #ffffff; color: #111827; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 8px; font-size: 13px; font-weight: 900; cursor: pointer; }
        .pay-card .form-grid { gap: 7px; }
        .pay-card label { margin: 4px 0 3px; font-size: 11px; }
        .pay-card input { padding: 8px 9px; border-radius: 8px; font-size: 13px; }
        .pay-card .submit-card { margin-top: 8px; padding: 10px 12px; }
        label { display: block; color: #334155; font-weight: 800; font-size: 13px; margin: 12px 0 6px; }
        input, select { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 9px; font-size: 16px; background: #ffffff; }
        .english-number-warning { display: none; margin-top: 5px; color: #b91c1c; font-size: 12px; font-weight: 800; }
        .english-number-warning.active { display: block; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .full { grid-column: 1 / -1; }
        .submit-card { width: 100%; margin-top: 16px; padding: 13px 16px; border: 0; border-radius: 9px; background: #0f172a; color: #ffffff; font-weight: 900; cursor: pointer; }
        .submit-card.disabled { background: #cbd5e1; color: #64748b; cursor: not-allowed; }
        .paid { background: #ecfdf5; color: #047857; padding: 14px; border-radius: 10px; font-weight: 900; }
        .close-to-orders { display: inline-flex; align-items: center; justify-content: center; margin-top: 14px; min-width: 150px; padding: 11px 16px; border-radius: 9px; background: #0f172a; color: #ffffff; text-decoration: none; font-weight: 900; }
        .close-to-orders:hover { background: #1e293b; }
        .cart-top-actions { display: flex; align-items: center; justify-content: flex-start; margin-bottom: 10px; }
        .add-service-button { gap: 10px; margin-top: 0; min-width: 230px; padding: 9px 13px; border-radius: 12px; background: linear-gradient(135deg, #fffdf2 0%, #fef3c7 100%); color: #78350f; border: 1px solid #f6d365; box-shadow: 0 10px 24px rgba(146, 64, 14, 0.12); transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease; }
        .add-service-button:hover { background: linear-gradient(135deg, #fef9c3 0%, #fde68a 100%); color: #78350f; transform: translateY(-1px); box-shadow: 0 14px 28px rgba(146, 64, 14, 0.16); }
        .add-service-icon { width: 30px; height: 30px; flex: 0 0 auto; display: inline-grid; place-items: center; border-radius: 9px; background: #f59e0b; color: #ffffff; font-size: 21px; font-weight: 900; line-height: 1; box-shadow: 0 5px 12px rgba(245, 158, 11, 0.24); }
        .add-service-copy { min-width: 0; display: grid; gap: 1px; text-align: start; }
        .add-service-copy strong { font-size: 13px; line-height: 1.35; }
        .add-service-copy small { color: #a16207; font-size: 10px; font-weight: 800; line-height: 1.35; }
        .missing-info { margin-top: 16px; padding: 14px 16px; background: #fffbeb; color: #92400e; border: 1px solid #fde68a; border-radius: 10px; font-weight: 900; line-height: 1.8; }
        .missing-info ul { margin: 8px 0 0; padding: 0 18px 0 0; }
        .delivery-options { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 8px; align-items: stretch; }
        .delivery-option { display: flex; align-items: flex-start; gap: 8px; min-width: 0; padding: 9px 10px; border: 1px solid #cbd5e1; border-radius: 10px; background: #ffffff; font-size: 13px; font-weight: 800; line-height: 1.45; overflow-wrap: anywhere; }
        .delivery-option input { flex: 0 0 auto; width: auto; margin: 4px 0 0; }
        .delivery-option small { display: block; color: #64748b; font-size: 11px; font-weight: 700; margin-top: 2px; overflow-wrap: anywhere; }
        .delivery-fields { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; margin-top: 10px; }
        .delivery-fields.address-fields { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .delivery-note { margin-top: 8px; padding: 8px 10px; border-radius: 9px; background: #ecfdf5; color: #047857; font-size: 12px; font-weight: 900; line-height: 1.5; }
        .delivery-auto-status { min-height: 20px; margin-top: 8px; color: #64748b; font-size: 11px; font-weight: 900; line-height: 1.5; }
        .delivery-auto-status.saving { color: #0369a1; }
        .delivery-auto-status.saved { color: #047857; }
        .delivery-auto-status.error { color: #b91c1c; }
        .delivery-auto-status:empty { display: none; }
        .delivery-section-box { padding: 9px 10px; }
        .delivery-section-box h3 { margin-bottom: 7px; }
        .delivery-section-box .delivery-options { gap: 6px; }
        .delivery-section-box .delivery-option { padding: 7px 8px; gap: 6px; line-height: 1.35; }
        .delivery-section-box .cart-form-grid { gap: 6px; margin-top: 6px; }
        .delivery-section-box .cart-form-grid label { margin: 4px 0 3px; font-size: 11px; }
        .delivery-section-box .cart-form-grid input { padding: 8px 9px; font-size: 12px; }
        .discount-form { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 8px; align-items: end; }
        .discount-form label { display: block; color: #9d174d; font-size: 12px; font-weight: 900; margin-bottom: 5px; }
        .discount-form input { width: 100%; padding: 9px 10px; border: 1px solid #f9a8d4; border-radius: 8px; background: #ffffff; color: #111827; font-size: 13px; font-weight: 800; }
        .discount-button { padding: 9px 12px; border: 0; border-radius: 8px; background: #db2777; color: #ffffff; font-size: 13px; font-weight: 900; cursor: pointer; }
        .discount-button:hover { background: #be185d; }
        .payment-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 8px; margin-top: 10px; }
        .cart-orders-list { display: grid; gap: 12px; width: 100%; }
        .summary-item { display: flex; align-items: center; justify-content: space-between; gap: 6px; min-width: 0; padding: 8px 9px; border: 1px solid #e2e8f0; border-radius: 9px; background: #f8fafc; }
        .summary-item span { color: #64748b; font-size: 12px; font-weight: 900; line-height: 1.35; }
        .summary-item strong { color: #0f172a; font-size: 13px; font-weight: 900; line-height: 1.35; text-align: left; white-space: nowrap; }
        .summary-item.total-before strong { color: #92400e; }
        .summary-item.total-after { background: #0f172a; border-color: #0f172a; color: #ffffff; }
        .summary-item.total-after span { color: #cbd5e1; }
        .summary-item.total-after strong { color: #ffffff; font-size: 14px; }
        .summary-files { display: grid; gap: 7px; margin: 0; padding: 0; list-style: none; }
        .summary-files li { padding: 9px 10px; border: 1px solid #e2e8f0; border-radius: 9px; background: #ffffff; color: #0f172a; font-size: 14px; font-weight: 900; line-height: 1.7; word-break: break-word; }
        .cart-order-detail { display: grid; gap: 14px; padding: 16px; border: 1px solid #dbe3ef; border-radius: 14px; background: #ffffff; box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06); }
        .cart-order-head { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; border-bottom: 1px solid #e5e7eb; padding-bottom: 12px; }
        .cart-order-title { margin: 0; color: #0f172a; font-size: 18px; font-weight: 900; line-height: 1.6; }
        .cart-order-meta { margin: 0; color: #64748b; font-size: 13px; font-weight: 800; }
        .cart-order-heading { display: grid; grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr); align-items: center; gap: 10px; width: 100%; }
        .cart-order-label { color: #64748b; font-size: 14px; font-weight: 900; }
        .cart-order-heading .cart-order-title { text-align: center; }
        .cart-order-heading .cart-order-meta { text-align: left; }
        .cart-order-actions { display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end; }
        .cart-section-box { padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; }
        .cart-section-box h3 { margin: 0 0 10px; color: #0f172a; font-size: 15px; font-weight: 900; }
        .cart-form-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; }
        .cart-form-grid .full { grid-column: 1 / -1; }
        .cart-page-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 8px; align-items: stretch; margin-top: 8px; }
        .cart-page-actions .close-to-orders,
        .cart-page-actions .cart-pay-link { margin-top: 0; min-height: 36px; padding: 8px 10px; border-radius: 9px; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; text-align: center; }
        .cart-page-actions .cart-pay-link { background: #047857; }
        .cart-page-actions .cart-pay-link:hover { background: #065f46; }
        .payment-back-link { display: inline-flex; align-items: center; justify-content: center; width: auto; min-width: 105px; margin: 0; padding: 7px 10px; border-radius: 8px; background: #047857; color: #ffffff; text-decoration: none; font-size: 12px; font-weight: 900; }
        .payment-back-link:hover { background: #065f46; }
        .delete-file-button { width: auto; min-width: 86px; margin: 0; padding: 7px 10px; border: 0; border-radius: 8px; background: #b91c1c; color: #ffffff; font-size: 12px; font-weight: 900; cursor: pointer; }
        .delete-file-button:hover { background: #991b1b; }
        .file-name-cell { display: flex; align-items: center; justify-content: space-between; gap: 8px; min-width: 0; }
        .file-name-text { min-width: 0; overflow-wrap: anywhere; }
        .file-service-type { display: block; margin-top: 4px; color: #64748b; font-size: 12px; font-weight: 900; line-height: 1.5; }
        .file-format-badge { display: inline-flex; align-items: center; justify-content: center; margin-inline-start: 6px; padding: 3px 7px; border-radius: 999px; background: #e0f2fe; color: #0369a1; font-size: 11px; font-weight: 900; vertical-align: middle; }
        .file-actions-inline { display: inline-flex; flex: 0 0 auto; align-items: center; gap: 6px; }
        .edit-file-button { display: inline-flex; flex: 0 0 auto; align-items: center; justify-content: center; width: auto; min-width: 64px; padding: 6px 9px; border-radius: 8px; background: #2563eb; color: #ffffff; text-decoration: none; font-size: 12px; font-weight: 900; }
        .edit-file-button:hover { background: #1d4ed8; }
        .view-file-button { display: inline-flex; flex: 0 0 auto; align-items: center; justify-content: center; width: auto; min-width: 74px; padding: 6px 9px; border-radius: 8px; background: #38bdf8; color: #ffffff; text-decoration: none; font-size: 12px; font-weight: 900; }
        .view-file-button:hover { background: #0ea5e9; }
        @media (max-width: 1100px) {
            .detail-table-wrap { border: 0; background: transparent; overflow: visible; }
            .detail-table-wrap table { min-width: 0; display: block; }
            .detail-table-wrap thead { display: none; }
            .detail-table-wrap tbody { display: grid; gap: 12px; }
            .detail-table-wrap tr { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06); }
            .detail-table-wrap td { display: grid; grid-template-columns: minmax(92px, 34%) minmax(0, 1fr); align-items: center; gap: 8px; min-width: 0; padding: 9px 10px; border: 1px solid #edf2f7; border-radius: 9px; background: #f8fafc; white-space: normal; overflow-wrap: anywhere; word-break: normal; }
            .detail-table-wrap td:first-child { min-width: 0; grid-column: 1 / -1; }
            .detail-table-wrap td.academic-word-usage { grid-column: 1 / -1; width: 100%; }
            .detail-table-wrap td::before { content: attr(data-label); color: #64748b; font-size: 12px; font-weight: 900; line-height: 1.5; }
            .detail-table-wrap .price-cell { white-space: normal; }
        }
        @media (max-width: 820px) {
            :root { --sidebar-width: 0px; --page-gap: 10px; }
            body { padding: 0; }
            .header { position: sticky; top: 0; width: 100%; min-height: 0; max-height: none; padding: 8px 10px; box-shadow: 0 10px 24px rgba(15, 23, 42, 0.16); }
            .header-inner { height: auto; display: grid; grid-template-columns: auto minmax(0, 1fr) auto; align-items: center; gap: 8px; }
            .brand-logo { width: 34px; height: 34px; border-radius: 10px; margin: 0; }
            .brand { margin: 0; font-size: 17px; line-height: 1.2; }
            .mobile-menu-toggle { display: inline-flex; min-width: 96px; padding: 7px 14px; border-radius: 8px; font-size: 12px; line-height: 1.2; white-space: nowrap; background: #22c55e; border-color: #86efac; color: #052e16; }
            .mobile-menu-toggle:hover { background: #4ade80; }
            .header-actions { grid-column: 1 / -1; margin-top: 0; display: none; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
            .header.menu-open .header-actions { display: grid; }
            .header-user { grid-column: 1 / -1; margin: 0; }
            main { width: calc(100% - 20px); margin: 8px auto 24px; padding: 0; }
            .payment-options { grid-template-columns: 1fr; align-items: stretch; }
            .meta, .totals, .form-grid, .delivery-options, .delivery-fields, .delivery-fields.address-fields, .discount-form { grid-template-columns: 1fr; }
            .cart-order-head { flex-direction: column; }
            .cart-order-actions { width: 100%; justify-content: stretch; }
            .cart-order-actions .close-to-orders,
            .cart-order-actions .inline-form { width: 100%; }
            .cart-form-grid { grid-template-columns: 1fr; }
            .cart-page-actions { grid-template-columns: 1fr; }
            .meta-card.full { grid-column: auto; }
            .home-button, .settings-button, .logout-button, .submit-card, .apple-pay, .google-pay, .close-to-orders { width: 100%; text-align: center; }
            .delivery-options { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .delivery-fields,
            .delivery-fields.address-fields,
            .cart-form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .cart-form-grid .full { grid-column: 1 / -1; }
            .discount-form { grid-template-columns: minmax(0, 1fr) auto; }
        }
        @media (max-width: 640px) {
            .panel, .files-panel { padding: 12px; }
            body.cart-index main { margin-top: 6px; }
            .cart-order-detail { gap: 10px; padding: 10px; border-radius: 12px; }
            .cart-section-box { padding: 10px; border-radius: 10px; }
            .cart-section-box h3 { font-size: 14px; margin-bottom: 8px; }
            .cart-order-title { font-size: 16px; }
            .cart-order-meta { font-size: 12px; }
            .cart-order-heading { grid-template-columns: minmax(0, 0.8fr) auto minmax(0, 1.2fr); gap: 6px; }
            .cart-order-label { font-size: 12px; }
            .cart-order-heading .cart-order-title { font-size: 13px; white-space: normal; line-height: 1.4; }
            .cart-order-heading .cart-order-meta { font-size: 10.5px; }
            .detail-table-wrap tbody { gap: 8px; }
            .detail-table-wrap tr { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 7px; padding: 9px; border-radius: 11px; }
            .detail-table-wrap td { display: flex; align-items: center; justify-content: space-between; gap: 5px; min-width: 0; padding: 8px 9px; border-radius: 9px; font-size: 12px; line-height: 1.45; background: #ffffff; }
            .detail-table-wrap td:first-child { grid-column: 1 / -1; padding: 8px; }
            .detail-table-wrap td::before { flex: 0 0 auto; display: inline; margin: 0; font-size: 10.5px; line-height: 1.35; }
            .detail-table-wrap td:first-child::before { display: none; }
            .detail-table-wrap td:not(:first-child) { text-align: left; }
            .file-name-cell { align-items: flex-start; gap: 6px; }
            .file-actions-inline { flex-wrap: wrap; justify-content: flex-end; gap: 5px; }
            .view-file-button, .delete-file-button { min-width: 64px; padding: 6px 8px; font-size: 11px; }
            .file-format-badge { padding: 2px 6px; font-size: 10px; }
            .payment-summary,
            .cart-page-actions { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 6px; }
            .summary-item { padding: 7px 8px; border-radius: 8px; }
            .summary-item span { font-size: 10.5px; }
            .summary-item strong { font-size: 11.5px; }
            .summary-item.total-after strong { font-size: 12px; }
            .cart-page-actions .close-to-orders,
            .cart-page-actions .cart-pay-link { min-height: 32px; padding: 7px 8px; font-size: 11px; }
            .delivery-options { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 7px; }
            .delivery-option { padding: 8px 9px; gap: 7px; font-size: 12px; border-radius: 9px; }
            .delivery-option small { font-size: 10.5px; line-height: 1.35; }
            .delivery-fields,
            .delivery-fields.address-fields,
            .cart-form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 6px; }
            .cart-form-grid .full { grid-column: 1 / -1; }
            .cart-form-grid label { font-size: 11px; }
            .cart-form-grid input { padding: 8px 9px; font-size: 12px; border-radius: 8px; }
            .discount-form { grid-template-columns: minmax(0, 1fr) auto; gap: 7px; }
            .discount-form label { font-size: 11px; }
            .discount-form input { padding: 8px 9px; font-size: 12px; }
            .discount-button { padding: 8px 10px; font-size: 12px; }
        }
        @media (min-width: 641px) and (max-width: 1100px) {
            .detail-table-wrap tr { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }
        @media (max-width: 420px) {
            .detail-table-wrap tr { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .payment-summary,
            .cart-page-actions { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .delivery-options { grid-template-columns: 1fr; }
            .delivery-fields,
            .delivery-fields.address-fields,
            .cart-form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="{{ ($paymentPage ?? false) ? 'cart-payment' : 'cart-index' }}">
    <header class="header">
        <div class="header-inner">
            <img class="brand-logo" src="{{ asset('images/alwrraq-logo.jpeg') }}" alt="شعار الورّاق">
            <div class="brand">الورّاق</div>
            <button class="mobile-menu-toggle" type="button" onclick="toggleMobileHeader(this, event)" aria-expanded="false">☰ القائمة</button>
            <div class="header-actions">
                <span class="header-user">👤 {{ auth()->user()->name }}</span>
                <a class="home-button" href="{{ route('home') }}">🏠 الصفحة الرئيسية</a>
                <a class="home-button" href="{{ route('orders.index') }}">🧾 طلباتي</a>
                <a class="settings-button" href="{{ route('account.settings') }}">⚙️ إعداداتي</a>
                <form class="header-form" method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-button" type="submit">🚪 خروج</button>
                </form>
                @include('shared.language-switcher')
            </div>
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
            @if ($paymentPage ?? false)
                <h1>صفحة الدفع</h1>
                <p>راجع المجموع النهائي ثم اختر طريقة الدفع المناسبة.</p>
            @endif
            @php
                $serviceNames = [
                    'notes' => 'طباعة المذكرات وملفات ال PDF',
                    'books' => 'طباعة وتجليد كتب كعب جلد طبيعي',
                    'color_printing' => 'طباعة الملفات بالألوان',
                    'thesis' => 'طباعة وتجليد رسالة ماجستير أو بحث تكميلي أو بحث تخرج',
                    'phd' => 'طباعة وتجليد رسالة دكتوراه',
                    'formatting' => 'تنسيق الرسائل الجامعية',
                    'research' => 'إنشاء بحث',
                ];
                $noPrintServices = ['formatting', 'research'];
                $projectNames = [
                    'thesis' => 'رسالة ماجستير',
                    'supplementary' => 'بحث تكميلي',
                    'graduation' => 'بحث تخرج',
                ];
                $coverColorNames = [
                    'black' => 'أسود',
                    'light_blue' => 'أزرق فاتح',
                    'navy' => 'أزرق كحلي',
                    'dark_green' => 'الأخضر الداكن',
                    'light_green' => 'الأخضر الفاتح',
                    'burgundy' => 'العنابي',
                    'beige' => 'البيج',
                    'white' => 'الأبيض',
                ];
                $writingColorNames = [
                    'gold' => 'كتابة باللون الذهبي',
                    'black' => 'كتابة باللون الأسود',
                ];
                $printSideNames = [
                    'one_side' => 'وجه واحد',
                    'two_sides' => 'وجهين',
                ];
                $pageSizeNames = [
                    'A4' => 'A4',
                    'A3' => 'A3',
                    'A5' => 'A5',
                    'B5' => 'B5',
                ];
                $paperColorNames = [
                    'white' => 'أبيض',
                    'yellow' => 'أصفر',
                ];
                $statusNames = [
                    'new' => 'بانتظار الدفع',
                    'reviewing' => 'قيد المراجعة',
                    'priced' => 'تم التسعير',
                    'processing' => 'قيد التنفيذ',
                    'completed' => 'مكتمل',
                    'cancelled' => 'ملغي',
                ];
                $deliveryMethodNames = [
                    'branch_pickup' => 'استلام من الفرع',
                    'islamic_university_delivery' => 'توصيل داخل الجامعة الإسلامية',
                    'madinah_delivery' => 'توصيل داخل المدينة المنورة',
                    'redbox_delivery' => 'خارج المدينة المنورة عبر RedBox',
                ];
                $dayNames = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
                $cartOrders = $cartOrders ?? collect();
                $cartSummary = $cartSummary ?? [
                    'orders_count' => 0,
                    'files_count' => 0,
                    'print_total' => 0,
                    'binding_total' => 0,
                    'discount_amount' => 0,
                    'delivery_fee' => 0,
                    'grand_total' => 0,
                ];
                $paymentPage = $paymentPage ?? false;
                $deliveryServiceTypes = ['notes', 'books', 'color_printing', 'thesis', 'phd'];
                $deliveryOrders = $cartOrders->filter(fn ($order) => in_array($order->service_type, $deliveryServiceTypes, true));
                $cartDeliveryOrder = $deliveryOrders->first(fn ($order) => filled($order->delivery_method)) ?? $deliveryOrders->first();
                $cartDiscountOrder = $cartOrders->first(fn ($order) => filled($order->discount_code)) ?? $cartOrders->first();
                $missingRequirements = collect();
                foreach ($cartOrders as $cartOrder) {
                    $serviceLabel = $serviceNames[$cartOrder->service_type] ?? $cartOrder->service_type;
                    if (in_array($cartOrder->service_type, ['notes', 'books', 'color_printing'], true) && $cartOrder->files->contains(fn ($file) => blank($file->binding_type))) {
                        $missingRequirements->push($serviceLabel . ': اختيار نوع التغليف لكل ملف.');
                    }
                    $academicPdfFiles = in_array($cartOrder->service_type, ['thesis', 'phd'], true)
                        ? $cartOrder->files->where('file_type', 'pdf')
                        : collect();
                    if (in_array($cartOrder->service_type, ['thesis', 'phd'], true) && $academicPdfFiles->isEmpty()) {
                        $missingRequirements->push($serviceLabel . ': رفع ملف PDF.');
                    }
                    if (in_array($cartOrder->service_type, ['thesis', 'phd'], true) && $academicPdfFiles->contains(fn ($file) => blank($file->cover_color) || blank($file->writing_color))) {
                        $missingRequirements->push($serviceLabel . ': اختيار لون الرسالة ولون الكتابة لكل ملف PDF.');
                    }
                    if ($cartOrder->service_type === 'thesis' && $academicPdfFiles->contains(fn ($file) => blank($file->thesis_project_type))) {
                        $missingRequirements->push($serviceLabel . ': اختيار نوع مشروع الرسالة لكل ملف PDF.');
                    }
                }
                if ($deliveryOrders->isNotEmpty() && ! $deliveryOrders->contains(fn ($order) => filled($order->delivery_method))) {
                    $missingRequirements->push('اختيار طريقة الاستلام أو التوصيل للسلة.');
                }
                $missingRequirements = $missingRequirements->unique()->values();
                $totalBeforeDiscount = (float) $cartSummary['print_total'] + (float) $cartSummary['binding_total'] + (float) $cartSummary['delivery_fee'];
                $totalAfterDiscount = (float) $cartSummary['grand_total'];
            @endphp

            @if ($cartOrders->isEmpty())
                <div class="missing-info">السلة فارغة. لا توجد طلبات غير مدفوعة حاليًا.</div>
                <a class="close-to-orders add-service-button" href="{{ route('home') }}" target="_top">
                    <span class="add-service-icon">+</span>
                    <span class="add-service-copy"><strong>إضافة خدمة جديدة</strong><small>اختر خدمة أخرى وأضفها للسلة</small></span>
                </a>
            @else
                @unless ($paymentPage)
                <div class="cart-top-actions">
                    <a class="close-to-orders add-service-button" href="{{ route('home') }}" target="_top">
                        <span class="add-service-icon">+</span>
                        <span class="add-service-copy"><strong>إضافة خدمة جديدة</strong><small>اختر خدمة أخرى وأضفها للسلة</small></span>
                    </a>
                </div>
                <div class="files-panel" style="margin-top:16px;">
                    <div class="cart-orders-list">
                        @foreach ($cartOrders as $cartOrder)
                            @php
                                $createdAtText = $dayNames[$cartOrder->created_at->dayOfWeek] . ' - ' . $cartOrder->created_at->format('Y-m-d H:i');
                                $cartBindingNames = $cartOrder->service_type === 'books'
                                    ? [
                                        'tape' => 'تجليد كعب جلد طبيعي',
                                        'wire' => 'تجليد كعب جلد طبيعي',
                                        'normal' => 'تجليد كعب جلد طبيعي',
                                        'none' => 'تجليد كعب جلد طبيعي',
                                    ]
                                    : ($cartOrder->service_type === 'color_printing'
                                        ? [
                                            'tape' => 'تغليف دبوس',
                                            'wire' => 'تغليف سلك',
                                            'normal' => 'تغليف عادي',
                                            'thermal' => 'تغليف حراري',
                                            'none' => 'بدون تغليف',
                                        ]
                                    : [
                                        'tape' => $cartOrder->service_type === 'notes' ? 'تغليف دبوس' : 'تجليد دبوس',
                                        'wire' => $cartOrder->service_type === 'notes' ? 'تغليف سلك' : 'تجليد سلك',
                                        'normal' => $cartOrder->service_type === 'notes' ? 'تغليف عادي' : 'تجليد عادي',
                                        'none' => $cartOrder->service_type === 'notes' ? 'بدون تغليف' : 'بدون تجليد',
                                    ]);
                                $cartBindingLabel = match ($cartOrder->service_type) {
                                    'books' => 'التجليد',
                                    'color_printing' => 'التغليف',
                                    'notes' => 'التغليف',
                                    'formatting' => 'التنسيق',
                                    'research' => 'إنشاء البحث',
                                    default => 'التجليد',
                                };
                                $cartBindingPriceLabel = match ($cartOrder->service_type) {
                                    'books' => 'سعر التجليد',
                                    'color_printing' => 'سعر التغليف',
                                    'notes' => 'سعر التغليف',
                                    'formatting' => 'سعر التنسيق',
                                    'research' => 'سعر إنشاء البحث',
                                    default => 'سعر التجليد',
                                };
                                $cartProjectTypes = $cartOrder->service_type === 'thesis'
                                    ? $cartOrder->files
                                        ->pluck('thesis_project_type')
                                        ->filter()
                                        ->unique()
                                        ->map(fn ($type) => $projectNames[$type] ?? $type)
                                        ->values()
                                    : collect();
                            @endphp
                            <div class="cart-order-detail">
                                <div class="cart-order-head">
                                    <div class="cart-order-heading">
                                        <span class="cart-order-label">الخدمة المختارة</span>
                                        <h3 class="cart-order-title">{{ $serviceNames[$cartOrder->service_type] ?? $cartOrder->service_type }}</h3>
                                        <div class="cart-order-meta">
                                            <span data-local-datetime="{{ $cartOrder->created_at->toIso8601String() }}">{{ $createdAtText }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="cart-section-box">
                                    <h3>الملفات والتفاصيل والأسعار</h3>
                                    <div class="detail-table-wrap">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>الملف</th>
                                                    @if ($cartOrder->service_type === 'thesis')
                                                        <th>مشروع الرسالة</th>
                                                    @endif
                                                    @if (in_array($cartOrder->service_type, ['thesis', 'phd'], true))
                                                        <th>الجامعة/المعهد</th>
                                                        <th>لون الرسالة</th>
                                                        <th>لون الكتابة</th>
                                                    @endif
                                                    <th>الصفحات</th>
                                                    @if ($cartOrder->service_type !== 'research')
                                                        <th>النسخ</th>
                                                    @endif
                                                    @if (in_array($cartOrder->service_type, ['notes', 'books', 'color_printing', 'thesis', 'phd'], true))
                                                        <th>نوع الطباعة</th>
                                                    @endif
                                                    @if (in_array($cartOrder->service_type, ['notes', 'books', 'color_printing'], true))
                                                        <th>حجم الصفحة</th>
                                                    @endif
                                                    @if (in_array($cartOrder->service_type, ['notes', 'books'], true))
                                                        <th>لون الورق</th>
                                                    @endif
                                                    @if (! in_array($cartOrder->service_type, $noPrintServices, true))
                                                        <th>{{ $cartBindingLabel }}</th>
                                                        <th>سعر الطباعة</th>
                                                    @endif
                                                    <th>{{ $cartBindingPriceLabel }}</th>
                                                    <th>إجمالي الملف</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($cartOrder->files as $file)
                                                    @php($isAcademicWord = in_array($cartOrder->service_type, ['thesis', 'phd'], true) && $file->file_type === 'word')
                                                    <tr>
                                                        <td data-label="الملف">
                                                            <div class="file-name-cell">
                                                                <span class="file-name-text">
                                                                    {{ $file->original_name }} <span class="file-format-badge">{{ strtoupper($file->file_type) }}</span>
                                                                    <span class="file-service-type">{{ $serviceNames[$cartOrder->service_type] ?? $cartOrder->service_type }}</span>
                                                                </span>
                                                                <div class="file-actions-inline">
                                                                    <a class="edit-file-button" href="{{ route('home', ['service' => $cartOrder->service_type, 'order' => $cartOrder->id]) }}" target="_top">تعديل</a>
                                                                    @if ($cartOrder->service_type !== 'research')
                                                                        <a class="view-file-button" href="{{ route('orders.file.view', ['order' => $cartOrder, 'file' => $file, 'from' => 'cart', 'v' => $file->updated_at?->timestamp]) }}">عرض {{ strtoupper(pathinfo($file->original_name, PATHINFO_EXTENSION) ?: $file->file_type) }}</a>
                                                                    @endif
                                                                    <form method="post" action="{{ url('/order-files/' . $file->id) }}">
                                                                        @csrf
                                                                        @method('delete')
                                                                        <button class="delete-file-button" type="submit">حذف الملف</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        @if ($isAcademicWord)
                                                            <td class="academic-word-usage" colspan="20" data-label="الاستخدام">ملف Word للعرض فقط، ولا يدخل ضمن الطباعة أو التجليد أو التسعير.</td>
                                                        @else
                                                        @if ($cartOrder->service_type === 'thesis')
                                                            <td data-label="مشروع الرسالة">{{ $projectNames[$file->thesis_project_type] ?? '-' }}</td>
                                                        @endif
                                                        @if (in_array($cartOrder->service_type, ['thesis', 'phd'], true))
                                                            <td data-label="الجامعة/المعهد">{{ $file->university_name ?: '-' }}</td>
                                                            <td data-label="لون الرسالة">{{ $coverColorNames[$file->cover_color] ?? '-' }}</td>
                                                            <td data-label="لون الكتابة">{{ $writingColorNames[$file->writing_color] ?? '-' }}</td>
                                                        @endif
                                                        <td data-label="الصفحات">{{ $file->pages }}</td>
                                                        @if ($cartOrder->service_type !== 'research')
                                                            <td data-label="النسخ">{{ $file->copies }}</td>
                                                        @endif
                                                        @if (in_array($cartOrder->service_type, ['notes', 'books', 'color_printing', 'thesis', 'phd'], true))
                                                            <td data-label="نوع الطباعة">{{ in_array($cartOrder->service_type, ['thesis', 'phd'], true) && $file->file_type === 'word' ? 'للعرض فقط' : ($printSideNames[$file->print_sides] ?? 'وجهين') }}</td>
                                                        @endif
                                                        @if (in_array($cartOrder->service_type, ['notes', 'books', 'color_printing'], true))
                                                            <td data-label="حجم الصفحة">{{ $pageSizeNames[$file->page_size] ?? 'A4' }}</td>
                                                        @endif
                                                        @if (in_array($cartOrder->service_type, ['notes', 'books'], true))
                                                            <td data-label="لون الورق">{{ $paperColorNames[$file->paper_color] ?? 'أبيض' }}</td>
                                                        @endif
                                                        @if (! in_array($cartOrder->service_type, $noPrintServices, true))
                                                            <td data-label="{{ $cartBindingLabel }}">{{ $cartBindingNames[$file->binding_type] ?? '-' }}</td>
                                                            <td class="price-cell" data-label="سعر الطباعة">{{ $file->print_price }} ريال</td>
                                                        @endif
                                                        <td class="price-cell" data-label="{{ $cartBindingPriceLabel }}">{{ $file->binding_price }} ريال</td>
                                                        <td class="price-cell" data-label="إجمالي الملف">{{ $file->total_price }} ريال</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        @endforeach

                        @if ($cartDeliveryOrder)
                            <div class="cart-order-detail">
                                <div class="cart-section-box delivery-section-box">
                                    <h3>الاستلام والتوصيل لكامل الطلب</h3>
                                    <form method="post" action="{{ route('cart.delivery.update', $cartDeliveryOrder) }}" data-delivery-form>
                                        @csrf
                                        @method('patch')
                                        <div class="delivery-options">
                                            <label class="delivery-option"><input type="radio" name="delivery_method" value="branch_pickup" @checked($cartDeliveryOrder->delivery_method === 'branch_pickup')> استلام من الفرع <small>مجاني</small></label>
                                            <label class="delivery-option"><input type="radio" name="delivery_method" value="islamic_university_delivery" @checked($cartDeliveryOrder->delivery_method === 'islamic_university_delivery')> داخل الجامعة الإسلامية <small>٥ ريال، ومجاني إذا الطلب فوق ٣٥ ريال</small></label>
                                            <label class="delivery-option"><input type="radio" name="delivery_method" value="madinah_delivery" @checked($cartDeliveryOrder->delivery_method === 'madinah_delivery')> داخل المدينة المنورة <small>٢٠ ريال</small></label>
                                            <label class="delivery-option"><input type="radio" name="delivery_method" value="redbox_delivery" @checked($cartDeliveryOrder->delivery_method === 'redbox_delivery')> خارج المدينة المنورة <small>٣٠ ريال عبر RedBox</small></label>
                                        </div>
                                        <div class="cart-form-grid" data-delivery-fields="islamic_university_delivery">
                                            <div><label>رقم الوحدة</label><input name="delivery_unit" value="{{ $cartDeliveryOrder->delivery_unit }}"></div>
                                            <div><label>رقم الدور</label><input name="delivery_floor" value="{{ $cartDeliveryOrder->delivery_floor }}"></div>
                                            <div><label>رقم الغرفة</label><input name="delivery_room" value="{{ $cartDeliveryOrder->delivery_room }}"></div>
                                        </div>
                                        <div class="cart-form-grid" data-delivery-fields="madinah_delivery redbox_delivery">
                                            <div data-redbox-city><label>المدينة</label><input name="delivery_city" value="{{ $cartDeliveryOrder->delivery_city }}"></div>
                                            <div><label>الحي</label><input name="delivery_district" value="{{ $cartDeliveryOrder->delivery_district }}"></div>
                                            <div><label>الشارع</label><input name="delivery_street" value="{{ $cartDeliveryOrder->delivery_street }}"></div>
                                            <div class="full"><label>رابط الموقع من خرائط Google</label><input name="delivery_map_url" value="{{ $cartDeliveryOrder->delivery_map_url }}" placeholder="الصق رابط موقعك من خرائط Google هنا"></div>
                                        </div>
                                        <div class="delivery-auto-status" data-delivery-auto-status></div>
                                    </form>
                                </div>
                            </div>
                        @endif

                        @if ($cartDiscountOrder)
                            <div class="cart-order-detail">
                                <div class="cart-section-box">
                                    <h3>كود الخصم لكامل الطلب</h3>
                                    <form class="discount-form" method="post" action="{{ route('cart.discount.apply', $cartDiscountOrder) }}">
                                        @csrf
                                        @method('patch')
                                        <div>
                                            <label>كود الخصم قبل الدفع</label>
                                            <input name="discount_code" value="{{ $cartDiscountOrder->discount_code }}" placeholder="اكتب كود الخصم">
                                        </div>
                                        <button class="discount-button" type="submit">تطبيق الخصم</button>
                                    </form>
                                    @if ($cartSummary['discount_amount'] > 0)
                                        <div class="delivery-note">تم تطبيق {{ $cartDiscountOrder->discount_code }}: خصم {{ $cartSummary['discount_amount'] }} ريال على كامل الطلب</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @endunless

                <div class="payment-summary">
                    <div class="summary-item"><span>عدد الخدمات في الطلب</span><strong>{{ $cartSummary['orders_count'] }} خدمة</strong></div>
                    <div class="summary-item"><span>عدد الملفات</span><strong>{{ $cartSummary['files_count'] }} ملف</strong></div>
                    <div class="summary-item total-before"><span>المبلغ الإجمالي قبل الخصم</span><strong>{{ $totalBeforeDiscount }} ريال</strong></div>
                    <div class="summary-item"><span>الخصم</span><strong>{{ $cartSummary['discount_amount'] }} ريال</strong></div>
                    <div class="summary-item"><span>رسوم التوصيل</span><strong>{{ $cartSummary['delivery_fee'] }} ريال</strong></div>
                    <div class="summary-item total-after"><span>المبلغ الإجمالي بعد الخصم</span><strong>{{ $totalAfterDiscount }} ريال</strong></div>
                </div>

                @if ($missingRequirements->isNotEmpty())
                    <div class="missing-info">
                        لا يمكن اعتماد السلة قبل إكمال المعلومات التالية:
                        <ul>
                            @foreach ($missingRequirements as $requirement)
                                <li>{{ $requirement }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @unless ($paymentPage)
                    <div class="cart-page-actions">
                        @if ($missingRequirements->isEmpty())
                            <a class="submit-card cart-pay-link" href="{{ route('cart.payment') }}">الانتقال للدفع</a>
                        @else
                            <span class="submit-card cart-pay-link disabled">أكمل بيانات السلة قبل الدفع</span>
                        @endif
                    </div>
                @endunless
            @endif
        </section>

        @if ($paymentPage)
        <section class="panel">
            <div class="payment-heading-row">
                <h2>الدفع</h2>
                <a class="payment-back-link" href="{{ route('cart.index') }}">الرجوع للسلة</a>
            </div>
            @if ($cartOrders->isEmpty())
                <div class="paid">لا توجد طلبات بانتظار الدفع.</div>
            @elseif ($missingRequirements->isNotEmpty())
                <div class="missing-info">أكمل المعلومات المطلوبة من صفحة الخدمة قبل الدفع. الطلب محفوظ وسيظهر في صفحة طلباتي.</div>
            @else
                <div class="payment-options">
                    <div class="pay-card">
                        <div class="wallet-heading-row">
                            <h2>المحافظ الرقمية</h2>
                            <div class="wallet-buttons">
                                <form method="post" action="{{ route('cart.pay-all') }}">
                                    @csrf
                                    <input type="hidden" name="payment_method" value="apple_pay">
                                    <button class="apple-pay" type="submit">Apple Pay</button>
                                </form>
                                <form method="post" action="{{ route('cart.pay-all') }}">
                                    @csrf
                                    <input type="hidden" name="payment_method" value="google_pay">
                                    <button class="google-pay" type="submit">Google Pay</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="pay-card">
                        <h2>بطاقة بنكية</h2>
                        <form method="post" action="{{ route('cart.pay-all') }}">
                            @csrf
                            <input type="hidden" name="payment_method" value="mada">
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
                                    <input name="card_expiry" inputmode="numeric" placeholder="MM/YY" autocomplete="cc-exp" required>
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
        @endif
    </main>
    <script>
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                window.location.reload();
            }
        });

        function toggleMobileHeader(button, event) {
            event?.stopPropagation();
            const header = button.closest('.header');
            const isOpen = header.classList.toggle('menu-open');
            button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }

        document.addEventListener('click', (event) => {
            const header = document.querySelector('.header.menu-open');
            if (!header || header.contains(event.target)) return;

            header.classList.remove('menu-open');
            header.querySelector('.mobile-menu-toggle')?.setAttribute('aria-expanded', 'false');
        });

        document.querySelectorAll('[data-local-datetime]').forEach((element) => {
            const date = new Date(element.dataset.localDatetime);
            if (Number.isNaN(date.getTime())) return;

            element.textContent = new Intl.DateTimeFormat('ar-SA-u-ca-gregory', {
                weekday: 'long',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
                timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            }).format(date).replace('،', ' -');
        });

        document.querySelectorAll('[data-delivery-form]').forEach((form) => {
            const status = form.querySelector('[data-delivery-auto-status]');
            let saveTimeout = null;
            let isSaving = false;

            const setStatus = (message, state = '') => {
                if (!status) return;
                status.textContent = message;
                status.className = `delivery-auto-status ${state}`.trim();
            };

            const updateDeliveryFields = () => {
                const selected = form.querySelector('input[name="delivery_method"]:checked')?.value || '';

                form.querySelectorAll('[data-delivery-fields]').forEach((group) => {
                    const allowed = group.dataset.deliveryFields.split(' ');
                    const isVisible = allowed.includes(selected);
                    group.style.display = isVisible ? 'grid' : 'none';
                    group.querySelectorAll('input').forEach((input) => {
                        input.disabled = !isVisible;
                    });
                });

                form.querySelectorAll('[data-redbox-city]').forEach((field) => {
                    const isVisible = selected === 'redbox_delivery';
                    field.style.display = isVisible ? 'block' : 'none';
                    field.querySelectorAll('input').forEach((input) => {
                        input.disabled = !isVisible;
                    });
                });
            };

            const requiredFieldsAreComplete = () => {
                const selected = form.querySelector('input[name="delivery_method"]:checked')?.value || '';
                const requiredByMethod = {
                    branch_pickup: [],
                    islamic_university_delivery: ['delivery_unit', 'delivery_floor', 'delivery_room'],
                    madinah_delivery: ['delivery_district', 'delivery_street', 'delivery_map_url'],
                    redbox_delivery: ['delivery_city', 'delivery_district', 'delivery_street', 'delivery_map_url'],
                };
                const fields = requiredByMethod[selected];

                return fields !== undefined && fields.every((name) => {
                    const input = form.querySelector(`[name="${name}"]`);
                    return input && input.value.trim() !== '';
                });
            };

            const saveDelivery = async () => {
                if (isSaving) return;
                if (!requiredFieldsAreComplete()) {
                    setStatus('أكمل بيانات التوصيل المطلوبة وسيتم الحفظ تلقائيًا.');
                    return;
                }

                isSaving = true;
                setStatus('جارٍ حفظ اختيار الاستلام والتوصيل...', 'saving');

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: new FormData(form),
                    });
                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        const validationMessage = result.errors
                            ? Object.values(result.errors).flat()[0]
                            : result.message;
                        throw new Error(validationMessage || 'تعذر حفظ التوصيل.');
                    }

                    setStatus('تم الحفظ وتحديث إجمالي السلة.', 'saved');
                    setTimeout(() => window.location.reload(), 250);
                } catch (error) {
                    isSaving = false;
                    setStatus(error.message || 'تعذر حفظ التوصيل. حاول مرة أخرى.', 'error');
                }
            };

            const scheduleSave = (delay = 0) => {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(saveDelivery, delay);
            };

            form.querySelectorAll('input[name="delivery_method"]').forEach((input) => {
                input.addEventListener('change', () => {
                    updateDeliveryFields();
                    scheduleSave();
                });
            });
            form.querySelectorAll('input:not([name="delivery_method"]):not([type="hidden"])').forEach((input) => {
                input.addEventListener('input', () => scheduleSave(650));
                input.addEventListener('change', () => scheduleSave());
            });
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                scheduleSave();
            });
            updateDeliveryFields();
        });

        const paymentRules = [
            { selector: 'input[name="card_number"]', pattern: /^[0-9 ]+$/, message: 'تنبيه: رقم البطاقة يقبل الأرقام الإنجليزية والمسافات فقط.' },
            { selector: 'input[name="card_expiry"]', pattern: /^(0[1-9]|1[0-2])\/[0-9]{2}$/, message: 'تنبيه: اكتب تاريخ الانتهاء بالأرقام الإنجليزية بصيغة MM/YY.' },
            { selector: 'input[name="card_cvc"]', pattern: /^[0-9]+$/, message: 'تنبيه: لا يقبل هذا الحقل إلا الأرقام الإنجليزية فقط 0-9.' },
        ];

        document.querySelectorAll(paymentRules.map((rule) => rule.selector).join(', ')).forEach((input) => {
            const showWarning = () => {
                const rule = paymentRules.find((item) => input.matches(item.selector));
                if (!rule) return;

                let warning = input.nextElementSibling;
                if (!warning || !warning.classList.contains('english-number-warning')) {
                    warning = document.createElement('div');
                    warning.className = 'english-number-warning';
                    input.insertAdjacentElement('afterend', warning);
                }

                const invalid = input.value !== '' && !rule.pattern.test(input.value);
                warning.textContent = rule.message;
                warning.classList.toggle('active', invalid);
                input.setCustomValidity(invalid ? rule.message : '');
            };

            input.addEventListener('input', showWarning);
            showWarning();
        });
    </script>
    @include('shared.chat-widget')
    @include('shared.language-tools')
</body>
</html>
