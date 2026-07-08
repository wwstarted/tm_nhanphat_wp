# PROJECT RULES — WORDPRESS THEME DEVELOPMENT v2.2

> Theme: **tmnhanphat** · Text-domain: `tmnhanphat` · Function prefix: `tmnhanphat_`
> v2.2 = v2.0 (gốc do chủ dự án cung cấp) + mục 21 (đã chốt lại: **không dùng build tool, không thêm thư viện ngoài — PHP/HTML/CSS/JS thuần**).

## 1. Mục tiêu dự án

Xây dựng một WordPress Theme chuyên nghiệp, có kiến trúc rõ ràng, tối ưu SEO, hiệu năng cao, dễ mở rộng và dễ bảo trì.

Theme phải tuân thủ:

- WordPress Coding Standards
- WordPress Template Hierarchy
- SEO Best Practices
- Core Web Vitals
- Modular Architecture
- Component-based Development
- Clean Code
- DRY (Don't Repeat Yourself)
- KISS (Keep It Simple)
- SOLID (áp dụng ở mức phù hợp với WordPress)

Đây là một dự án mới hoàn toàn. AI không được kế thừa bất kỳ cấu trúc cũ nào ngoài các quy tắc trong tài liệu này.

## 2. Kiến trúc tổng quát

Theme phải được chia theo từng layer rõ ràng.

```
theme/
├── assets/
├── inc/
├── template-parts/
├── templates/
├── languages/
├── woocommerce/        (chỉ tạo khi bật WooCommerce — xem mục 17)
├── functions.php
├── style.css
├── index.php
├── front-page.php
├── single.php
├── archive.php
├── page.php
├── search.php
├── category.php        (chỉ thêm khi cần markup khác archive.php — xem mục 3)
├── tag.php              (chỉ thêm khi cần markup khác archive.php — xem mục 3)
├── author.php           (chỉ thêm khi cần markup khác archive.php — xem mục 3)
├── 404.php
├── comments.php
├── header.php
├── footer.php
└── sidebar.php
```

Không được tạo cấu trúc lộn xộn. Mỗi folder chỉ có đúng một nhiệm vụ.

## 3. WordPress Template Hierarchy

Bắt buộc tuân thủ Template Hierarchy (front-page → home → single → page → archive → category → tag → author → search → 404 → index).

Không render toàn bộ website bằng index.php. Không dùng switch-case để xử lý nhiều template trong một file.

**Tối ưu đã áp dụng (KISS/DRY — mục 1 & 11):** không tạo sẵn `category.php`, `tag.php`, `author.php` nếu nội dung giống hệt `archive.php`. WordPress Template Hierarchy tự động fallback `category.php`/`tag.php`/`author.php` → `archive.php` khi file không tồn tại, nên 3 file rỗng chỉ gọi lại `archive.php` là dư thừa, vi phạm DRY và tăng rủi ro quên đồng bộ khi sửa sau này. Chỉ tạo riêng `category.php` / `tag.php` / `author.php` khi loại archive đó thật sự cần markup khác biệt (ví dụ: trang tác giả cần thêm author bio) — khi đó vẫn tái sử dụng `get_template_part( 'template-parts/archive/content' )` để không lặp lại markup từng item.

## 4. Template Parts

Toàn bộ template lớn phải chia nhỏ.

```
template-parts/
├── header/
├── footer/
├── home/
├── single/
│   ├── content.php
│   ├── author-box.php
│   ├── related-posts.php
│   ├── share.php
│   └── toc.php
├── archive/
├── page/
├── search/
└── components/
```

Không để một file dài hàng nghìn dòng HTML.

## 5. Components Architecture

Mỗi UI Component phải độc lập: Button, Card, Hero, CTA, FAQ, Post Card, Pagination, Breadcrumb, Modal, Accordion, Slider...

Một component chỉ định nghĩa một lần. Không copy HTML giữa nhiều template.

## 6. Assets Organization

### CSS

Không dùng một file CSS rất lớn. Chia theo:

```
assets/css/
├── global/     (reset, variables, typography, helpers, animation)
├── layout/     (header, footer, sidebar)
├── components/ (button, hero, card, faq, modal...)
├── pages/      (front-page, single, archive, page, search, contact)
└── vendor/
```

`style.css` (ở root theme) chỉ dùng cho Theme Header bắt buộc của WordPress (Theme Name, Author, Version...) và tối thiểu CSS nền tảng nếu cần. Không viết toàn bộ CSS trong `style.css`.

### JavaScript

Tương tự CSS:

```
assets/js/
├── global/     (app.js, helpers.js)
├── layout/     (header.js, mobile-menu.js)
├── pages/      (front-page.js, single.js, archive.js — chỉ tạo khi trang đó thật sự cần JS riêng)
├── components/ (slider.js, accordion.js, tabs.js, modal.js)
└── vendor/
```

Không tạo file `app.js` vài nghìn dòng.

## 7. Conditional Asset Loading

Bắt buộc: chỉ load asset khi cần.

- `is_front_page()` → front-page bundle (css/js)
- `is_single()` → single bundle
- `is_archive()` → archive bundle

Không load `archive.css`, `404.css`, `contact.css`, `single.css`... cho mọi request.

**Shared assets** — chỉ load global cho mọi trang: `reset`, `variables`, `helpers`, `header`/`footer` layout CSS, `header.js`, `mobile-menu.js`.

## 8. Theme Functions Organization

Không được viết toàn bộ logic trong `functions.php`. `functions.php` chỉ có nhiệm vụ bootstrap (require các file trong `inc/`).

```
inc/
├── setup.php        theme supports, image sizes, menus registration
├── enqueue.php       (đã gộp asset management — xem mục 9)
├── helpers.php
├── menus.php
├── widgets.php
├── ajax.php
├── api.php
├── seo.php
├── breadcrumbs.php
├── pagination.php
├── images.php
├── security.php
├── cleanup.php
└── performance.php
```

## 9. Asset Management

Toàn bộ CSS/JS phải được quản lý tập trung tại **`inc/enqueue.php`** (không tạo thêm `assets.php` riêng để tránh trùng vai trò với `enqueue.php`). Không enqueue lung tung ở nhiều file.

## 10. Không hardcode

Không hardcode `/wp-content/themes/theme-name/`, `assets/css/`, `assets/js/`...

Luôn dùng: `get_template_directory_uri()`, `get_stylesheet_directory_uri()`, `get_theme_file_uri()`, `get_template_directory()`.

## 11. Query Rules

Không query trực tiếp trong HTML. Logic (`$data = ...`, `WP_Query`, `get_template_part(...)`) và giao diện phải tách biệt tối đa.

## 12. Helper Functions

Function dùng nhiều lần phải đưa vào `inc/helpers.php`. Không copy code.

## 13. Images

Bắt buộc dùng `the_post_thumbnail()`, `wp_get_attachment_image()`, `srcset`, `sizes`. Không hardcode `<img src="">` nếu ảnh thuộc Media Library.

## 14. SEO Rules

Toàn bộ HTML phải semantic: `<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<aside>`, `<footer>`. Không lạm dụng `div`.

Mỗi trang: chỉ có 1 `H1`, title rõ ràng, meta description, canonical, breadcrumb, schema nếu phù hợp, alt cho ảnh, aria-label cho button nếu cần.

Không dùng `<div class="title">` thay cho `<h2>`.

## 15. Performance Rules

Ưu tiên Core Web Vitals. Bắt buộc: Lazy Loading, Responsive Images, preload font khi cần, defer JS phù hợp, tránh render-blocking, tối ưu DOM, không tải tài nguyên dư thừa.

Không thêm thư viện nếu có thể giải quyết bằng JavaScript thuần.

## 16. Naming Convention

- File template: `front-page.php`, `single.php`, `archive.php`, `page-contact.php`. Không đặt `homepage.php`, `news.php`, `abc.php` trừ khi WordPress hỗ trợ.
- CSS: `single.css`, `archive.css`, `button.css`.
- JS: `single.js`, `hero.js`, `slider.js`.
- Tên hàm PHP theo chuẩn WordPress, có prefix `tmnhanphat_`.

## 17. Future Scalability

Kiến trúc phải hỗ trợ dễ dàng: WooCommerce, CPT, Custom Taxonomy, REST API, AJAX, Theme Options, Multilingual, Block Editor, Child Theme — mà không cần refactor toàn bộ dự án.

Hiện tại (giai đoạn khởi tạo): **chưa bật WooCommerce**. Khi cần, thêm thư mục `woocommerce/` + `inc/woocommerce.php` để override template, không đụng vào các phần còn lại.

## 18. Security Rules

Luôn escape dữ liệu đầu ra: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`.

Luôn sanitize dữ liệu đầu vào: `sanitize_text_field()`, `sanitize_email()`, `absint()`, `sanitize_key()`.

Kiểm tra nonce cho mọi form và AJAX. Không truy vấn SQL trực tiếp nếu có API WordPress tương đương.

## 19. Documentation Rules

Mỗi module lớn cần có comment mô tả mục đích. Các hàm public cần PHPDoc ngắn gọn. Khi tạo thư mục hoặc module mới, AI phải giải thích ngắn gọn vai trò của module đó.

## 20. AI Development Workflow (Bắt buộc)

Trong suốt quá trình phát triển, AI phải tuân thủ quy trình sau:

1. Phân tích yêu cầu trước khi viết mã.
2. Đề xuất kiến trúc hoặc vị trí file phù hợp với cấu trúc hiện có.
3. Không tạo file trùng chức năng hoặc logic.
4. Ưu tiên tái sử dụng component, helper và module có sẵn.
5. Mọi mã mới phải tuân thủ các quy tắc trong tài liệu này.
6. Sau mỗi tính năng hoàn thành, tự kiểm tra:
   - Đúng WordPress Coding Standards.
   - Không phát sinh mã trùng lặp.
   - Đúng kiến trúc thư mục.
   - Không ảnh hưởng SEO và hiệu năng.
   - Không tải CSS/JS không cần thiết.
7. Chỉ chuyển sang bước tiếp theo khi phần hiện tại đã đạt yêu cầu.

## 21. Không dùng build tool / thư viện ngoài (đã chốt)

Dự án chỉ dùng **PHP (HTML) + CSS + JS thuần**. Không Node.js, không npm, không Vite/Webpack/Gulp, không jQuery hay bất kỳ thư viện JS/CSS ngoài nào (Bootstrap, Tailwind...), trừ khi chủ dự án yêu cầu thêm sau này. Điều này áp dụng xuyên suốt mục 6/7/9/15.

Cách enqueue (không qua build/manifest):

- Mỗi file trong `assets/css/**` và `assets/js/**` được enqueue **trực tiếp** bằng `wp_enqueue_style()` / `wp_enqueue_script()` ngay trong `inc/enqueue.php`, gọi từ 1 hàm duy nhất `tmnhanphat_enqueue_assets()` hook vào `wp_enqueue_scripts` (đúng yêu cầu: enqueue có điều kiện viết trong `inc/enqueue.php`, gọi qua `functions.php`).
- Cache-busting: dùng `filemtime()` của từng file làm version (`tmnhanphat_asset_version()`) thay vì sửa tay version mỗi lần đổi CSS/JS.
- Conditional loading (mục 7) giữ nguyên: global CSS/JS load mọi trang; CSS/JS riêng theo `is_front_page()` / `is_singular('post')` / `is_archive() || is_home()` / `is_search()` / `is_page()`.
- JS thuần không dùng `import`/`export` (không có bundler để gộp module). Hàm dùng chung gắn vào 1 namespace toàn cục duy nhất `window.tmnhanphat` (định nghĩa ở `assets/js/global/helpers.js`, load trước các file phụ thuộc qua tham số `$deps` của `wp_enqueue_script()`).
- Không tạo file `assets/js/pages/*.js` hoặc `assets/js/components/*.js` nếu chưa có hành vi thực tế cần xử lý (tránh enqueue file rỗng — vi phạm mục 15 "không tải tài nguyên dư thừa"). Chỉ tạo khi trang/component đó thật sự cần JS riêng.
