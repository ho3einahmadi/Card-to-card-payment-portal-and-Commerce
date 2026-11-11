# Card-to-Card Payment Gateway for WooCommerce

**English** · [فارسی](#فارسی)

A manual payment gateway for WooCommerce: after checkout the customer sees a bank-app style page with the card number, owner name, order total and a button to send the payment receipt (Telegram/Bale link is configurable). The order is set to **on-hold** until you confirm the transfer.

### Install
Copy `my_card_pyment.php` into `wp-content/plugins/bb-card-to-card/`, activate it, then go to WooCommerce → Settings → Payments → *Card to card* and fill in the card number, owner name and receipt link.

### Changes in 3.2
- All output is escaped (`esc_html`, `esc_url`, `wp_json_encode`).
- Removed the hook that forced orders back to *pending* whenever the thank-you page was opened (it could reopen completed orders).
- Order status is now *on-hold*; the thank-you page only renders for orders paid with this gateway.
- Plugin header cleaned up; WooCommerce presence is checked.

---

<a id="فارسی"></a>

<div dir="rtl">

## Card-to-Card Payment Gateway for WooCommerce

یک درگاه پرداخت دستی برای ووکامرس: بعد از ثبت سفارش، مشتری صفحه‌ای شبیه اپ بانکی می‌بیند که شماره کارت، نام صاحب حساب، مبلغ سفارش و دکمهٔ ارسال رسید (لینک تلگرام/بله قابل‌تنظیم) را نشان می‌دهد. سفارش تا تأیید واریز توسط شما در وضعیت **در انتظار بررسی (on-hold)** می‌ماند.

### نصب
فایل `my_card_pyment.php` را در `wp-content/plugins/bb-card-to-card/` کپی و افزونه را فعال کنید. سپس از ووکامرس ← تنظیمات ← پرداخت‌ها ← «کارت به کارت» شماره کارت، نام صاحب حساب و لینک رسید را وارد کنید.

### تغییرات نسخهٔ ۳.۲
- همهٔ خروجی‌ها escape می‌شوند.
- هوکی که با هر بار باز شدن صفحهٔ تشکر وضعیت سفارش را به «در انتظار پرداخت» برمی‌گرداند حذف شد (می‌توانست سفارش تکمیل‌شده را دوباره باز کند).
- وضعیت سفارش اکنون on-hold است و صفحهٔ تشکر فقط برای سفارش‌های همین درگاه نمایش داده می‌شود.
- سربرگ افزونه اصلاح شد و حضور ووکامرس بررسی می‌شود.

</div>
