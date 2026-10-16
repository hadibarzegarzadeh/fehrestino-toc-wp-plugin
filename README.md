# Fehrestino - Table of Contents for WordPress

[English](#english) | [فارسی](#فارسی)

---

## English

Fehrestino is a WordPress plugin that automatically builds a clean, interactive table of contents from the headings of your posts and pages.

### Features

- Automatic table of contents generated from H2 to H6 headings
- Choose exactly which heading levels are included
- Respects heading hierarchy with nested lists
- Four ready-made styles: Neo, Minimal, Gradient and Card
- Custom accent color
- Optional heading numbering
- Smooth scrolling and scrollspy that highlights the section you are reading
- Collapsible box with a configurable default state (open or collapsed)
- Minimum number of headings required before the table is shown
- Automatic insertion into post content, or manual placement with a shortcode
- Works on posts, with an option to enable it on pages
- Admin settings page with live theme previews
- Interface languages: Persian and English, with translation files included

### Installation

1. Download this repository as a ZIP file.
2. In WordPress, go to Plugins > Add New > Upload Plugin and upload the ZIP.
3. Activate the plugin.
4. Open the Fehrestino settings page in the admin menu to configure it.

You can also copy the plugin files into `wp-content/plugins/fehrestino/` manually.

### Usage

By default the table of contents is inserted automatically into single posts that contain enough headings.

To place it manually, turn off automatic insertion in the settings and add the shortcode where you want it:

```
[fehrestino]
```

### Settings

| Option | Description |
| --- | --- |
| Title | Heading shown above the table of contents |
| Heading levels | Which of H2-H6 are listed |
| Theme | Neo, Minimal, Gradient or Card |
| Accent color | Main color of the box |
| Numbering | Show numbers next to entries |
| Default state | Open or collapsed on page load |
| Smooth scroll | Animated scrolling to the heading |
| Scrollspy | Highlight the active section while scrolling |
| Enable on pages | Also show the table on static pages |
| Auto insert | Insert automatically or use the shortcode |
| Minimum headings | Hide the table when there are fewer headings |
| Language | Plugin interface language |

### Requirements

- WordPress 5.0 or later
- PHP 7.2 or later

### Structure

```
fehrestino.php
admin/
assets/
includes/
languages/
public/
```

---

## فارسی

<div dir="rtl">

فهرستینو یک افزونه وردپرس است که به صورت خودکار از عنوان‌های نوشته‌ها و برگه‌ها یک فهرست مطالب تمیز و تعاملی می‌سازد.

### امکانات

- ساخت خودکار فهرست مطالب از عنوان‌های H2 تا H6
- انتخاب دقیق سطح عنوان‌هایی که در فهرست نمایش داده می‌شوند
- رعایت سلسله‌مراتب عنوان‌ها با لیست‌های تو در تو
- چهار قالب آماده: نئو، مینیمال، گرادیان و کارت
- انتخاب رنگ اصلی دلخواه
- شماره‌گذاری اختیاری عنوان‌ها
- اسکرول نرم و اسکرول‌اسپای برای مشخص کردن بخشی که در حال خواندن آن هستید
- جعبه قابل جمع شدن با تعیین حالت پیش‌فرض (باز یا بسته)
- تعیین حداقل تعداد عنوان برای نمایش فهرست
- درج خودکار در محتوای نوشته یا قرار دادن دستی با شورت‌کد
- فعال روی نوشته‌ها و امکان فعال‌سازی روی برگه‌ها
- صفحه تنظیمات مدیریت همراه با پیش‌نمایش قالب‌ها
- زبان رابط کاربری فارسی و انگلیسی همراه با فایل‌های ترجمه

### نصب

1. این مخزن را به صورت فایل ZIP دانلود کنید.
2. در وردپرس به مسیر افزونه‌ها > افزودن > بارگذاری افزونه بروید و فایل ZIP را آپلود کنید.
3. افزونه را فعال کنید.
4. از منوی مدیریت، صفحه تنظیمات فهرستینو را باز کنید و آن را پیکربندی کنید.

همچنین می‌توانید فایل‌های افزونه را به صورت دستی در مسیر `wp-content/plugins/fehrestino/` کپی کنید.

### نحوه استفاده

به صورت پیش‌فرض، فهرست مطالب به طور خودکار در نوشته‌هایی که تعداد عنوان کافی دارند درج می‌شود.

برای قرار دادن دستی، درج خودکار را در تنظیمات خاموش کنید و شورت‌کد زیر را در جای دلخواه بگذارید:

```
[fehrestino]
```

### تنظیمات

| گزینه | توضیح |
| --- | --- |
| عنوان | متنی که بالای فهرست مطالب نمایش داده می‌شود |
| سطح عنوان‌ها | کدام یک از H2 تا H6 در فهرست بیایند |
| قالب | نئو، مینیمال، گرادیان یا کارت |
| رنگ اصلی | رنگ اصلی جعبه فهرست |
| شماره‌گذاری | نمایش شماره کنار هر مورد |
| حالت پیش‌فرض | باز یا بسته بودن فهرست هنگام بارگذاری صفحه |
| اسکرول نرم | حرکت انیمیشنی تا عنوان انتخاب‌شده |
| اسکرول‌اسپای | برجسته کردن بخش فعال هنگام اسکرول |
| فعال‌سازی روی برگه‌ها | نمایش فهرست در برگه‌های ثابت |
| درج خودکار | درج خودکار یا استفاده از شورت‌کد |
| حداقل عنوان | پنهان کردن فهرست وقتی عنوان‌ها کمتر از این تعداد باشند |
| زبان | زبان رابط کاربری افزونه |

### پیش‌نیازها

- وردپرس نسخه 5.0 یا بالاتر
- PHP نسخه 7.2 یا بالاتر

### ساختار پوشه‌ها

```
fehrestino.php
admin/
assets/
includes/
languages/
public/
```

</div>
