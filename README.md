# Faramoj Advanced Search (FAS) - v1.1.5

> An ultra-high-performance, modern, and highly-optimized live search engine for WordPress, specifically crafted for technical telecommunication products, articles, documentation, and metadata. Developed by **Danial Kenarkoohi**.

---

## 📖 English Documentation

### 🌟 Key Features
- **Custom REST API Endpoint:** Bypasses the standard, slow `admin-ajax.php` bottleneck in favor of a specialized custom WP REST API endpoint (`/wp-json/fas/v1/search`), delivering instantaneous, real-time live search suggestions.
- **Bi-directional Digit Normalization:** Automatically converts and normalizes Persian (`۰-۹`), Arabic (`٠-٩`), and English (`0-9`) numeric characters. This allows queries containing localized numbers to seamlessly match database entries (e.g. searching "Phase-۳۰" matches "Phase-30").
- **Dynamic Multilingual Options Isolation:** Settings are isolated dynamically per active language (using suffixes like `_fa` or `_en`). This guarantees that saving settings in Farsi does not touch, overwrite, or reset English options, and vice versa.
- **Multi-tier Fallback Engine:** Features a smart fallback option retriever. If an option is empty or unconfigured for the active language, it falls back to the global unsuffixed option, then to the alternate language's setting, and finally to the default hardcoded parameters, preventing empty frontend containers.
- **Custom SVG & PNG Uploads:** Leverage WordPress's native Media Library to upload and set custom SVG or PNG icons for each category search tab. Custom icons render dynamically in the frontend popup and the admin settings block.
- **Drag-and-Drop Category Reordering:** Reorder category tabs (`All`, `Products`, `News & Articles`, `Documentation`) effortlessly using an interactive admin interface powered by WordPress's native `jquery-ui-sortable`. Contains auto-reset fallbacks to prevent blank custom containers.
- **Interactive Live Layout Preview:** View real-time adjustments to branding colors, dimensions, borders, and dark/light overlay theme modes inside the admin panel. The interactive live preview block has been relocated directly beneath the tab ordering settings for optimal UX.
- **Search Telemetry & Analytics:** Logs and tracks search telemetry data in real-time, displaying search counts and popular search term rankings in a dedicated "Statistics" sub-panel.
- **Premium Glassmorphic Design:** Standardized frontend modal overlay with beautiful backdrop blur, responsive iOS-style pills, animated SVGs, and high-specificity `!important` button style overrides to completely neutralize theme reset conflicts.
- **Elementor Widget Integration:** Ready-to-use Elementor search trigger widget with custom icon triggers, and a lightweight `[fas_search_trigger]` shortcode.

---

### 📂 Directory & File Structure
```plaintext
faramoj-advanced-search/
├── faramoj-advanced-search.php     # Main plugin bootstrap & metadata
├── README.txt                      # WordPress Repository documentation
├── README.md                       # Comprehensive project documentation
│
├── admin/                          # Backend Panel Modules
│   ├── class-fas-admin.php         # Admin settings registry, submenus & enqueues
│   ├── css/
│   │   └── fas-admin.php           # Admin panel styles template
│   └── views/
│       ├── settings-page.php       # HTML layout for Settings & Live Preview
│       ├── statistics-page.php     # HTML layout for Search Telemetry Metrics
│       └── about-us-page.php       # HTML layout for About Developer & Version badge
│
├── includes/                       # Core Logic & Classes
│   ├── class-fas-core.php          # Main orchestrator (Shortcodes, Hooks, Footer)
│   ├── class-fas-rest.php          # Custom WP REST API & Database Query Isolation
│   ├── class-fas-i18n.php          # Internationalization & Language handler
│   ├── class-fas-elementor.php     # Elementor Widget integration loader
│   └── class-fas-elementor-widget.php # Custom Elementor widget parameters
│
├── public/                         # Frontend UI/UX Assets
│   ├── css/
│   │   └── fas-public.css          # Frontend glassmorphism styles & overrides
│   └── js/
│       └── fas-public.js           # Vanilla JS fetch client with AbortController
│
└── templates/                      # Overridable Frontend Templates
    ├── search-modal.php            # Modal overlay layout
    └── search-result-item.php      # Single search result template card
```

---

### 🛠️ Developer Installation & Configuration
1. Upload the `faramoj-advanced-search` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin via the **Plugins** menu in WordPress.
3. Access **Faramoj Search** from the sidebar menu to configure languages, set branding colors, reorder tabs, upload custom SVG icons, and monitor real-time search statistics!

---

## 📖 مستندات فارسی

### 🌟 ویژگی‌های کلیدی
- **پایانه‌های اختصاصی REST API:** با دور زدن گلوگاه کند و منسوخ شده‌ی `admin-ajax.php` وردپرس و بهره‌گیری از یک مسیر سفارشی بهینه‌سازی شده در WP REST API (`/wp-json/fas/v1/search`)، نتایج زنده را در کسری از ثانیه لود می‌کند.
- **نرمال‌سازی دوطرفه اعداد:** اعداد فارسی (`۰-۹`) و عربی (`٠-٩`) تایپ شده توسط کاربران را به صورت خودکار به اعداد انگلیسی (`0-9`) تبدیل می‌کند تا با رکوردهای عددی دیتابیس مطابقت داده شوند (مانند جستجوی "Phase-۳۰" که کلمه‌ی "Phase-30" را با موفقیت در بر می‌گیرد).
- **تفکیک و ایزوله‌سازی تنظیمات بر اساس زبان:** تنظیمات ادمین به صورت پویا بر اساس زبان فعال در پنل (با پسوندهای `_fa` و `_en`) ثبت و ذخیره می‌شوند. این کار تضمین می‌کند که ذخیره‌ی تنظیمات فارسی کوچک‌ترین تداخلی با تنظیمات انگلیسی نداشته و هیچ تنظیماتی از دیتابیس حذف نمی‌شود.
- **سیستم بازیابی چند مرحله‌ای (Fallback):** مجهز به یک موتور هوشمند استعلام تنظیمات؛ در صورتی که تنظیمی برای زبان جاری پیکربندی نشده باشد، به ترتیب به دنبال گزینه‌ی بدون پسوند اصلی، سپس تنظیمات زبان جایگزین و نهایتاً به پارامترهای پیش‌فرض هاردکد شده رجوع می‌کند تا پاپ‌آپ فرانت همیشه بی‌نقص و زیبا باقی بماند.
- **آپلود مستقیم آیکون‌های PNG و SVG:** با استفاده از کتابخانه‌ی بومی رسانه‌ی وردپرس، برای هر تب جستجو یک آیکون اختصاصی SVG یا PNG دلخواه بارگذاری کنید. این آیکون‌ها بلافاصله در پیش‌نمایش زنده پنل ادمین و کادر جستجوی فرانت رندر می‌شوند.
- **مرتب‌سازی دسته‌ها با کشیدن و رها کردن (Sortable):** ترتیب نمایش تب‌ها (`همه نتایج`، `محصولات`، `اخبار و مقالات`، `مستندات`) را با استفاده از رابط بصری فوق‌العاده مبتنی بر `jquery-ui-sortable` بومی وردپرس جابجا کنید. این بخش دارای سیستم ریست خودکار برای جلوگیری از خالی ماندن تب‌ها در دیتابیس است.
- **پیش‌نمایش زنده و تعاملی ادمین:** تغییرات رنگ‌ها، ابعاد عرض/ارتفاع، تم تیره (زغالی مدرن) یا تم روشن (شرکتی سفید) را به صورت کاملاً آنی و زنده در پنل ادمین تماشا کنید. کادر پیش‌نمایش تعاملی برای تجربه‌ی کاربری (UX) بهتر، مستقیماً به زیر بخش مدیریت تب‌ها منتقل شده است.
- **آمارهای تحلیل کلمات کلیدی (Telemetry):** آمار جستجوی کاربران را به صورت زنده ردیابی کرده و تعداد کل جستجوها و محبوب‌ترین عبارات سرچ شده را به همراه رتبه‌بندی آماری در زیرمنوی اختصاصی "Statistics" نمایش می‌دهد.
- **طراحی شیشه‌ای و مدرن (Glassmorphism):** اورلی فرانت‌رند شیک همراه با افکت تاری پس‌زمینه (backdrop-blur)، دکمه‌های کپسولی سبک iOS، دکمه‌ی خروج شیشه‌ای دایره‌ای شکل و دکمه‌های شناور کاملاً واکنش‌گرا که با اعمال کدهای CSS با اولویت بالا (`!important`) در برابر بازنشانی استایل‌های قالب محافظت شده‌اند.
- **سازگاری کامل با المنتور (Elementor):** مجهز به ویجت اختصاصی دکمه‌ی جستجوی فراموج در المنتور با قابلیت تعریف آیکون‌ها و شورت‌کد اختصاصی `[fas_search_trigger]`.

---

### 📂 ساختار فایل‌ها و پوشه‌ها
```plaintext
faramoj-advanced-search/
├── faramoj-advanced-search.php     # فایل اصلی افزونه و متا دیتای نسخه
├── README.txt                      # مستندات مخزن وردپرس
├── README.md                       # مستندات جامع و راهنمای کامل پروژه
│
├── admin/                          # ماژول‌های پنل مدیریت (بک‌اند)
│   ├── class-fas-admin.php         # منطق ثبت تنظیمات، زیرمنوها و بارگذاری دارایی‌ها
│   ├── css/
│   │   └── fas-admin.php           # استایل‌های شخصی‌سازی شده پنل مدیریت
│   └── views/
│       ├── settings-page.php       # چیدمان HTML تنظیمات و پیش‌نمایش زنده پاپ‌آپ
│       ├── statistics-page.php     # چیدمان HTML گزارش‌ها و نمودار آمار کلمات جستجو شده
│       └── about-us-page.php       # چیدمان HTML درباره نویسنده و نشان نسخه
│
├── includes/                       # منطق هسته افزونه و کلاس‌ها
│   ├── class-fas-core.php          # هدایت‌کننده اصلی افزونه (شورت‌کدها، هوک‌ها و فوتر)
│   ├── class-fas-rest.php          # پایانه اختصاصی REST API و منطق مجزای پرس‌وجوی پایگاه‌داده
│   ├── class-fas-i18n.php          # هندلر چندزبانه و بین‌المللی‌سازی
│   ├── class-fas-elementor.php     # بارگذار سازگاری با صفحه‌ساز المنتور
│   └── class-fas-elementor-widget.php # پارامترها و کنترل‌های ویجت المنتور
│
├── public/                         # فایل‌های استاتیک فرانت‌اند (UI/UX)
│   ├── css/
│   │   └── fas-public.css          # استایل‌های شیشه‌ای مدرن و بازنویسی‌های فرانت
│   └── js/
│       └── fas-public.js           # جاوا اسکریپت خالص فرانت‌رند با استفاده از AbortController
│
└── templates/                      # قالب‌های فرانت‌اند قابل رونویسی در پوسته فرزند
    ├── search-modal.php            # ساختار کلی پاپ‌آپ جستجو
    └── search-result-item.php      # قالب تکی کارت نتایج جستجو
```

---

### 🛠️ راهنمای نصب و راه‌اندازی برای برنامه‌نویسان
۱. پوشه `faramoj-advanced-search` را به دایرکتوری `/wp-content/plugins/` هاست خود انتقال دهید.
۲. افزونه را از منوی **افزونه‌ها** در پیشخوان وردپرس فعال کنید.
۳. به منوی جدید ظاهر شده یعنی **Faramoj Search** در پیشخوان مراجعه کرده و زبان‌ها را پیکربندی، رنگ‌ها را تنظیم، آیکون‌های دلخواه بارگذاری و آمار زنده جستجوی کاربران خود را رصد کنید!
