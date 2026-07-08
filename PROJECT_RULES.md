# PROJECT RULES — WORDPRESS THEME DEVELOPMENT v2.15

> Theme: **tmnhanphat** · Text-domain: `tmnhanphat` · Function prefix: `tmnhanphat_`
> v2.15 = v2.14 + Refactor About Company Section LẦN 2: bỏ HẲN clip-path/polygon()/skew()/SVG tự vẽ hình, chuyển sang RENDER TRỰC TIẾP 2 ảnh PNG designer xuất từ Figma (Rectangle 19.png làm `background-image` của `.about-home__shape` — thuần decoration, không phải container; ảnh thang máy PNG nền trong suốt là 1 LAYER `position:absolute` riêng `.about-home__image`, không nằm trong `.container`/Grid/Flex). Cấu trúc HTML đổi hẳn BEM prefix `about` → `about-home`. Customizer: bỏ mọi field hình học px cũ (Shape Height/Top/Bottom Offset, Layout Text/Image Width, Gap), thêm Section mới "Background Shape" (Upload Shape Image, Background Size/Position/Opacity) và "Elevator Image" (Upload, Size Desktop/Tablet/Mobile theo % chiều cao tham chiếu, Offset X/Y, Z-index) — xem mục 28 (đã viết lại hoàn toàn, bản v3, thay thế cả v1 và v2). Cần upload thủ công 2 file PNG qua wp-admin Customizer vì AI không trích xuất được ảnh nhị phân từ tin nhắn chat.
> v2.14 = v2.13 + Refactor hình học About Company Section (bám sát Figma hơn, KHÔNG đổi Typography/Setting/Content/Animation): khối đỏ đổi từ hình thang 4 điểm sang "lục giác dài" 6 điểm (2 góc vát chéo song song, phần lớn cạnh trên/dưới vẫn phẳng); Ảnh tách thành 1 LAYER `position:absolute` độc lập (không còn là cột Flex) đứng sát mép phải, đè lên khối đỏ và tràn ra nền trắng; Text căn theo tâm khối đỏ (không phải tâm section) qua kỹ thuật dùng chung `top`/`height` giữa `.about__shape`/`.about__inner`/`.about__image-wrap`; `.about` đổi sang `min-height: calc(top+height+bottom)` để khối đỏ luôn chiếm đúng ~70% chiều cao section — xem mục 28 (đã cập nhật toàn bộ, thay thế bản v1).
> v2.13 = v2.12 + About Company Section (Homepage, dưới Partners): Panel Customizer riêng "About Home" (`inc/customizer/about-customizer.php`) — General/Content/Image/Background/Layout/Responsive; layout bất đối xứng Text 55%/Ảnh 45% với khối nền góc cạnh dựng bằng `clip-path: polygon()` (không `transform:skew()`); ảnh đè lên khối đỏ qua `transform:translate()`; giữ 2 cột đến hết Tablet, chỉ xếp chồng ở Mobile — xem mục 28 (bao gồm lưu ý kỹ thuật quan trọng: `height`/`top`/`bottom` dạng % KHÔNG resolve được trên containing block có `height:auto`, phải dùng PX).
> v2.12 = v2.11 + bổ sung "Container Width (px)" vào Global Settings → Layout (mặc định 1200, gắn với `--container-max-width`) — sửa đúng nguyên nhân khi Container Padding = 0 nhưng nội dung vẫn chưa sát mép trình duyệt trên màn rộng: đó là do `--container-max-width` (chiều rộng khung, canh giữa bằng margin:auto) chứ không phải padding còn sót — 2 biến độc lập, giờ cả 2 đều nằm trong Global Settings. `--header-container-width` của Header vẫn là setting RIÊNG, không bị setting này ghi đè (đã verify).
> v2.11 = v2.10 + Global Layout System: Panel Customizer riêng "Global Settings" → Section "Layout" (`inc/customizer/global-customizer.php`) quản lý `--container-padding` (Desktop 24px/Tablet 20px/Mobile 16px, trước đây là hằng số cố định 1rem trong variables.css) — áp dụng tự động cho TOÀN site (Header, Hero, Partner, Footer, mọi Homepage Section, Archive, Single, Page, Search, 404) vì tất cả đã cùng đọc 1 biến CSS này từ trước, không phải sửa file nào khác — xem mục 27.
> v2.10 = v2.9 + Refactor UI Customizer (KHÔNG đổi key/sanitize/default/render nào): bỏ panel dùng chung "Homepage" — Hero và Partner mỗi cái giờ là 1 Panel riêng cấp cao nhất ("Hero Home", "Partner Home"), Section bên trong đặt tên thuần theo nhóm chức năng (General/Content/Buttons/Background/Overlay/Statistics 1-4/Statistics Style/Responsive/Animation cho Hero; General/Layout/Responsive/Partner 1-8 cho Partner) — xem mục 26 (quy tắc bắt buộc cho mọi feature Homepage sau này).
> v2.9 = v2.8 + Refactor Partners rendering: tách hẳn "Number of Partners" (display limit, cắt bằng `array_slice()` ở tầng dữ liệu) khỏi "Columns" (Desktop/Tablet/Mobile — setting cố định, không còn auto-shrink theo số lượng); đổi `.partners__grid` từ CSS Grid sang Flexbox (`flex-wrap` + `justify-content:center`) để hàng cuối luôn tự căn giữa mà không cần hardcode theo số dư — xem mục 25.
> v2.8 = v2.7 + Partners Section (Homepage, dưới Hero): Panel Customizer "Homepage" → Partners (`inc/customizer/partners-customizer.php`) — General/Layout/8 slot Partner; mục 24 mới (quy ước "fixed-slot repeater" khi cần danh sách item động trong Core Customizer).
> v2.7 = v2.6 + Fix Header Navigation Color: bug "Floating Menu Color không hoạt động" do `.main-navigation__panel` ghi đè `color` kế thừa — sửa bằng biến riêng `--header-nav-color/-hover/-active` theo state (Floating/Sticky độc lập); bỏ hoàn toàn hiệu ứng underline, Active/Hover chỉ đổi màu chữ.
> v2.6 = v2.5 + Hero Banner (Homepage) hoàn chỉnh: Panel Customizer "Homepage" (`inc/customizer/hero-customizer.php`) — Background/Overlay/Subtitle/Heading/Description/Buttons/4 Company Stats/Layout/Animation; `assets/js/pages/front-page.js` mới (IntersectionObserver fade-up/stagger/scale + count-up, chỉ enqueue ở trang chủ).
> v2.5 = v2.4 + Footer hoàn chỉnh: 4 vị trí menu (`footer_company/policy/product/service` thay cho `footer` chung), Panel Customizer "Footer" (`inc/customizer/footer-customizer.php`), bỏ sidebar widget `footer-widgets` (không dùng nữa — Top Footer là bố cục cố định).
> v2.4 = v2.3 + mục 23 (chốt: **theme không xử lý SEO** — toàn bộ Title/Meta/Canonical/OG/Schema/Sitemap thuộc về Plugin SEO; đã xoá `inc/seo.php` và Breadcrumb Schema JSON-LD).
> v2.3 = v2.2 + mục 22 (quy ước tổ chức Customizer + dynamic CSS variable, chốt khi xây Header).
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
├── setup.php              theme supports, image sizes, menus registration
├── enqueue.php             (đã gộp asset management — xem mục 9)
├── helpers.php
├── template-functions.php  helper đọc trạng thái/giá trị Customizer dùng trong template (mục 22)
├── menus.php
├── widgets.php
├── ajax.php
├── api.php
├── breadcrumbs.php          chỉ build danh sách item hiển thị HTML — KHÔNG in Schema (mục 23)
├── pagination.php
├── images.php
├── security.php
├── cleanup.php
├── performance.php
├── customizer.php          loader — require toàn bộ inc/customizer/*.php (mục 22)
└── customizer/
    ├── global-customizer.php    Panel "Global Settings" → Layout (container padding toàn site, mục 27)
    ├── header-customizer.php    Panel "Header" (Logo, Sticky, Transparent, màu, typography...)
    ├── footer-customizer.php    Panel "Footer" (màu, bottom bar, logo, liên hệ, social, bố cục)
    ├── hero-customizer.php      Panel "Hero Home" (mục 26)
    └── partners-customizer.php  Panel "Partner Home" (mục 26)
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

## 14. SEO Rules (đã thu hẹp phạm vi — xem mục 23)

Theme **không** chịu trách nhiệm xử lý SEO — toàn bộ Title/Meta Description/Canonical/Robots/Open Graph/Twitter Card/Schema/Sitemap là việc của Plugin SEO (Rank Math, Yoast, SEOPress...). Theme chỉ đảm bảo phần nền tảng để Plugin SEO hoạt động đúng:

- Toàn bộ HTML phải semantic: `<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<aside>`, `<footer>`. Không lạm dụng `div`.
- Mỗi trang chỉ có đúng 1 `H1`, heading hierarchy không nhảy cấp, không duplicate heading.
- Ảnh dùng `alt` (mục 13), button/link không rõ nghĩa có `aria-label`.
- Breadcrumb (nếu có) chỉ hiển thị **vị trí điều hướng dạng HTML** — không tự in Breadcrumb Schema (JSON-LD), xem mục 23.
- Không dùng `<div class="title">` thay cho `<h2>`.
- Giữ `add_theme_support( 'title-tag' )` (đã có ở `inc/setup.php`) — đây là hạ tầng bắt buộc để WordPress/Plugin SEO tự quản lý `<title>`, không phải theme tự generate title.

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

## 22. Customizer Module & Dynamic CSS Variable (quy ước, áp dụng từ Header)

**Cấu trúc Customizer:** `inc/customizer.php` chỉ là loader — `require` toàn bộ `inc/customizer/*.php` bằng `glob()`. Muốn thêm 1 Panel mới (Footer, Sidebar, Theme Options...) chỉ cần tạo file mới trong `inc/customizer/` (ví dụ `footer-customizer.php`), không sửa `customizer.php` hay `functions.php` — đúng tinh thần mục 17 (dễ mở rộng).

**Quy ước bên trong mỗi file `inc/customizer/{module}-customizer.php`:**
1. 1 hàm `tmnhanphat_get_{module}_customizer_fields()` khai báo toàn bộ setting dạng config array (id, label, section, type, default, sanitize_callback) — KHÔNG viết tay từng cặp `add_setting()`/`add_control()` (vi phạm DRY khi có > 5 field).
2. 1 hàm `tmnhanphat_{module}_customizer_register( $wp_customize )` loop qua config ở trên để add_panel/add_section/add_setting/add_control, hook vào `customize_register`.
3. Giá trị mặc định của toàn bộ field nằm trong 1 hàm `tmnhanphat_{module}_defaults()` ở `inc/template-functions.php` — dùng chung cho cả `default` của control lẫn fallback khi đọc `get_theme_mod()`, tránh lặp số liệu 2 nơi.
4. Mọi `select`/`choices` phải có whitelist sanitize_callback riêng (không dùng `sanitize_text_field` cho giá trị có tập hợp hữu hạn).

**Đọc giá trị trong template:** không gọi `get_theme_mod()` trực tiếp trong `header.php`/`template-parts/*` — luôn qua 1 hàm helper trong `inc/template-functions.php` (ví dụ `tmnhanphat_get_header_mod()`) để tập trung fallback logic, dễ đổi tên setting sau này mà không phải sửa nhiều file template.

**Dynamic CSS Variable (cho setting số/màu người dùng tự chỉnh):** Không dùng `<style>` in trực tiếp qua `wp_head` — dùng `wp_add_inline_style( $handle, $css )` gắn vào đúng stylesheet liên quan, gọi trong `inc/enqueue.php` (giữ đúng mục 9: tập trung asset ở 1 file). Hàm build chuỗi CSS (ví dụ `tmnhanphat_render_header_css_vars()`) đặt ở `inc/template-functions.php`, trả về `:root{--x:...;}` cho các giá trị dùng được trong CSS custom property, cộng thêm khối `@media` render tĩnh cho những setting KHÔNG thể đặt trong custom property (ví dụ breakpoint responsive — giới hạn của CSS thuần, không phải lỗi kiến trúc). Đây không phải "hardcode" hay "inline CSS" vi phạm mục 10/14 — đây là giá trị runtime do chính admin cấu hình qua Customizer, không có cách nào khác để áp dụng vào CSS ngoài cách này.

**An toàn dữ liệu:** mọi setting đều bắt buộc có `sanitize_callback` khi `add_setting()` (mục 18) — hàm build CSS ở trên tin tưởng giá trị đọc ra từ `get_theme_mod()` đã sạch, không cần escape lại lần 2.

## 23. Ranh giới SEO: Theme vs Plugin (Bắt buộc, đã chốt)

Theme **KHÔNG** chịu trách nhiệm xử lý SEO. Toàn bộ SEO do Plugin chuyên dụng đảm nhiệm (Rank Math, Yoast SEO, SEOPress, The SEO Framework, Slim SEO...). Theme chỉ đảm bảo tương thích, không được cạnh tranh/trùng lặp dữ liệu với plugin.

**Theme tuyệt đối không được tự generate/in ra:**
- `<title>` (không filter `document_title*`, không override `wp_title()`).
- meta description, meta keywords, meta robots.
- `rel="canonical"`.
- Open Graph (`og:*`), Twitter Card (`twitter:*`).
- JSON-LD / structured data ở bất kỳ dạng nào: WebSite, Organization, Article, Breadcrumb, FAQ, Product Schema...
- XML Sitemap, RSS SEO.
- Không hook thêm vào `wp_head()` với mục đích chèn dữ liệu SEO (theme vẫn phải gọi `wp_head()`/`wp_footer()` bình thường — đó là hạ tầng bắt buộc để Plugin SEO tự hook vào, chỉ là theme không tự thêm gì mang tính SEO vào đó).
- Không tự tạo redirect, không đụng vào permalink/rewrite rules.

**Theme chỉ cần đảm bảo (nền tảng cho Plugin SEO hoạt động tốt — xem mục 14):**
- HTML semantic đúng, heading hierarchy chuẩn, không duplicate H1/heading.
- Ảnh có `alt`, navigation semantic, breadcrumb hiển thị đúng vị trí (HTML thuần, không kèm Schema).
- `add_theme_support( 'title-tag' )` (đã có ở `inc/setup.php`) để Plugin SEO/WordPress tự quản lý `<title>` — đây là bật hạ tầng, không phải theme tự sinh nội dung title.
- Nếu sau này 1 module cần hiển thị dữ liệu structured (vd: hiển thị rating sao ngoài UI), phải kiểm tra Plugin SEO đã in Schema đó chưa (`function_exists()`/hằng số plugin, theo mẫu `tmnhanphat_has_seo_plugin()` cũ) và **không** tự in JSON-LD trùng — mặc định là KHÔNG làm, trừ khi được yêu cầu rõ ràng.

`inc/seo.php` đã bị xoá khỏi theme (không còn lý do tồn tại khi theme không tự xử lý SEO). `inc/breadcrumbs.php` chỉ giữ lại `tmnhanphat_get_breadcrumb_items()` để build danh sách hiển thị — hàm in Schema `BreadcrumbList` (JSON-LD) đã bị gỡ bỏ.

## 24. "Fixed-Slot Repeater" — mẫu chuẩn khi cần danh sách item động trong Customizer

WordPress Core Customizer **không có control kiểu Repeater** (thêm/bớt item động qua UI). Các plugin như ACF/Kirki có Repeater nhưng đó là **thư viện ngoài** — vi phạm mục 21 (chỉ PHP/CSS/JS thuần). Khi 1 section cần danh sách item số lượng biến đổi (Company Stats, Social Networks, Partners Logo...), quy ước thống nhất là **khai báo sẵn N slot cố định**, mỗi slot có ít nhất 1 field `Enable`:

1. Chọn N đủ dùng cho thực tế + dư (ví dụ Partners dùng 8 slot qua `tmnhanphat_get_partners_slot_count()`) — đặt trong 1 hàm/hằng số duy nhất, không rải số magic ở nhiều nơi.
2. Field mặc định của TỪNG slot nằm trong `tmnhanphat_{module}_defaults()` (vòng lặp `for`, không copy-paste N lần).
3. 1 hàm `tmnhanphat_get_{module}_item_fields( $index, $defaults )` định nghĩa field cho 1 slot, gọi lại N lần trong `tmnhanphat_get_{module}_customizer_fields()` (DRY — không viết tay N section giống hệt nhau).
4. 1 hàm `tmnhanphat_get_{module}s()` (số nhiều) đọc cả N slot, **lọc theo Enable + có dữ liệu thật** (vd: Partners lọc thêm `!empty($logo)`), rồi trả về mảng đã sẵn sàng render — template KHÔNG tự loop qua N slot, chỉ loop qua mảng đã lọc này.
5. Nếu cần sắp xếp thứ tự hiển thị (không có drag-and-drop UI khi chỉ dùng Core Customizer): thêm field số `Display Order`, sort bằng `usort()` trong hàm ở mục 4 — không cần JS.
6. Nếu cần giới hạn SỐ LƯỢNG item hiển thị (khác với số cột layout), thêm 1 setting số riêng (ví dụ "Number of Partners") và cắt bằng `array_slice()` **trong hàm ở mục 4**, ở tầng dữ liệu — không phải ẩn item thừa bằng CSS. Số cột (Desktop/Tablet/Mobile) là setting **độc lập, cố định** — không tự co giãn theo số lượng item đang hiển thị (bài học từ Partners, xem mục 25: auto-shrink cột theo count từng bị dùng sai, gây lệch trái ở hàng cuối khi xuống nhiều hàng).

**Cập nhật (v2.10):** không còn dùng chung 1 panel "Homepage" nữa — mỗi feature có Panel riêng cấp cao nhất ("Hero Home", "Partner Home"...). Xem mục 26 cho quy tắc Panel/Section chính thức áp dụng từ nay.

## 25. Căn giữa hàng cuối khi số item không chia hết cho số cột — dùng Flexbox, không dùng CSS Grid

**Vấn đề:** CSS Grid định nghĩa số track (`grid-template-columns`) 1 LẦN cho toàn bộ lưới — khi hàng cuối thiếu item, các track còn lại của hàng đó vẫn tồn tại nhưng trống, khiến item(s) còn lại luôn dồn về bên trái, **không có cách nào chỉ căn giữa riêng hàng cuối bằng CSS Grid thuần** mà không hardcode selector theo số dư (`n % cols`, dễ vỡ mỗi khi đổi số lượng).

**Giải pháp chuẩn của theme (đã áp dụng ở Partners Section):** dùng Flexbox thay vì Grid.

```css
.wrapper {
	display: flex;
	flex-wrap: wrap;
	justify-content: center; /* mỗi "hàng" (flex line) tự căn giữa ĐỘC LẬP, kể cả hàng cuối */
	gap: var(--gap);
}

.item {
	flex: 0 0 calc((100% - (var(--cols) - 1) * var(--gap)) / var(--cols));
	max-width: calc((100% - (var(--cols) - 1) * var(--gap)) / var(--cols));
}
```

- `flex-wrap:wrap` + `justify-content:center` trên container: mỗi hàng do trình duyệt tự ngắt xuống dòng được canh giữa main-axis riêng biệt — hàng đủ item hay hàng cuối thiếu item đều tự động đúng, không cần biết trước số dư, không cần JS.
- `flex: 0 0 calc(...)`: tính đúng bề rộng 1 cột trừ gap, dùng `var(--cols)`/`var(--gap)` nên vẫn đọc từ Customizer, không hardcode số cột trong CSS.
- Mỗi breakpoint (desktop/tablet/mobile) đổi cả `gap` của container LẪN công thức `flex-basis` của item theo đúng cặp `--cols`/`--gap` tương ứng breakpoint đó — 2 giá trị phải khớp nhau.

Áp dụng lại kỹ thuật này cho bất kỳ section nào sau này cần "N cột đều nhau, hàng cuối tự căn giữa" (ví dụ: danh sách dịch vụ, danh sách dự án...).

## 26. Cấu trúc Panel/Section Customizer (Bắt buộc — áp dụng cho mọi feature Homepage/trang từ nay)

**Giới hạn kỹ thuật phải nhớ:** WordPress Customizer core chỉ hỗ trợ **2 cấp lồng**: `Panel → Section → Control`. **Không có "panel trong panel"** — 1 Panel không thể chứa Panel khác, chỉ chứa Section. Vì vậy KHÔNG cố tạo 1 panel "Homepage" bao ngoài rồi nhét "Hero Home"/"Partner Home"/... vào trong làm panel con — WP không hỗ trợ, code sẽ chỉ tạo ra section phẳng trông giống panel giả, không thu gọn/mở độc lập được.

**Quy tắc chính thức:**

1. **Mỗi feature/trang lớn = 1 Panel riêng, độc lập, ở cấp cao nhất** của Customizer (ngang hàng Site Identity, Menus, Widgets...). Không gom nhiều feature vào 1 panel dùng chung.
2. **Đặt tên Panel thống nhất dạng `"{Feature} Home"`** cho các section thuộc trang chủ (`Hero Home`, `Partner Home`, `About Home`, `Service Home`, `Project Home`, `Process Home`, `Testimonial Home`, `CTA Home`, `News Home`, `FAQ Home`, `Contact Home`...). **Không dùng** `Hero`, `Home Hero`, `Homepage Hero`, `Section Hero`, `Hero Section` hay các biến thể khác. Với trang không phải Homepage (Product Archive, News Detail, About Page...), đặt tên Panel đúng theo tên trang đó, không thêm hậu tố "Home".
3. **Bên trong mỗi Panel, chia Section theo nhóm chức năng** (không dồn hết setting vào 1 Section). Tên Section KHÔNG lặp lại tên Panel (Panel đã cho ngữ cảnh) — ví dụ Panel "Hero Home" thì Section chỉ ghi "General", "Content", "Buttons"..., không ghi "Hero: General".
4. **Không quá 30–40 control trong 1 Section.** Nếu 1 nhóm chức năng có nhiều item lặp lại (Company Stats, Partner logos, Footer columns...), mỗi item là **1 Section riêng** đặt tên `"{Nhóm} {N}"` (ví dụ `Statistics 1`, `Partner 3`, `Column 2`) — đúng mẫu "fixed-slot repeater" ở mục 24.
5. **Ưu tiên đặt `priority` liền nhau** cho các Panel cùng nhóm khái niệm (ví dụ mọi Panel "...Home" của Homepage dùng dải `priority` 30-59) để chúng hiển thị cạnh nhau trong danh sách Customizer gốc — đây là cách "nhóm" duy nhất khả thi khi không có panel lồng panel.
6. Việc tổ chức Panel/Section **không bao giờ được đổi** setting ID, `sanitize_callback`, hay `default` value của field đang tồn tại — đây thuần là thay đổi tham số `'panel'`/`'section'`/`'priority'` khi gọi `add_panel()`/`add_section()`/`add_control()`, dữ liệu đã lưu trong `theme_mods` không bị ảnh hưởng.

**Ví dụ đã áp dụng:**
- `Hero Home` (panel) → `General`, `Content`, `Buttons`, `Background`, `Overlay`, `Statistics 1`-`Statistics 4`, `Statistics Style`, `Responsive`, `Animation` (section).
- `Partner Home` (panel) → `General`, `Layout`, `Responsive`, `Partner 1`-`Partner 8` (section).
- `Header` (panel, có sẵn từ trước) và `Footer` (panel, có sẵn từ trước) đã đúng mẫu "mỗi feature 1 panel riêng" — không cần sửa lại theo task này, có thể áp dụng cùng quy ước đặt tên Section (General/Column 1.../Bottom Bar/Style) khi có nhu cầu chỉnh sửa Footer sau này.

## 27. Global Layout System — nguồn dữ liệu DUY NHẤT cho chiều rộng & khoảng cách trái/phải toàn site

**Vấn đề cần tránh:** mỗi section tự định nghĩa `padding-left`/`padding-right`/`padding: 0 60px` riêng — khi cần đổi khoảng cách 2 bên phải sửa từng file CSS, dễ lệch nhau giữa các section, vi phạm DRY.

**2 khái niệm ĐỘC LẬP, dễ nhầm lẫn — phải phân biệt rõ:**
- **Container Padding** (`--container-padding`): khoảng đệm **BÊN TRONG** khung nội dung, giữa cạnh khung và nội dung thật.
- **Container Width** (`--container-max-width`): chính **chiều rộng tối đa** của khung đó, canh giữa bằng `margin-inline:auto`. Set Padding = 0 **không** làm nội dung chạm mép trình duyệt nếu Width vẫn nhỏ hơn viewport — đây là nguyên nhân thực tế của case "Padding=0 nhưng vẫn còn khoảng trắng 2 bên trên màn hình rộng" (không phải bug, là 2 biến khác nhau).

**Giải pháp đã áp dụng:** toàn bộ theme (từ ngày đầu scaffold) đã dùng CHUNG 2 CSS custom property này cho mọi khung nội dung — không phải setting mới thêm, mà là **kết nối biến CSS sẵn có với Customizer**:

- `--container-max-width` và `--container-padding` được `.container` (dùng ở Archive/Single/Page/Search/404/index — mọi template không phải Homepage), `.hero__inner`, `.footer-top__inner`, `.partners__inner` **đọc chung**. Riêng `.site-header__inner` chỉ đọc chung `--container-padding` — chiều rộng của Header dùng biến **RIÊNG** `--header-container-width` (setting của Header Home → Layout, đã có từ trước, CỐ Ý độc lập để Header có thể rộng/hẹp khác Body nếu admin muốn — Global Settings KHÔNG ghi đè biến này).
- Panel **"Global Settings"** (`inc/customizer/global-customizer.php`, priority thấp để nằm đầu danh sách Customizer) → Section **"Layout"**: `Container Width (px)`, `Container Padding — Desktop/Tablet/Mobile (px)`.
- `tmnhanphat_render_global_css_vars()` (trong `inc/template-functions.php`) sinh `:root{--container-max-width:...;--container-padding:...}` + 2 khối `@media` (991px/599px) redefine lại `--container-padding` — kỹ thuật giống hệt mục 22 (redefine custom property trong `@media`, không dùng nó LÀM điều kiện `@media`). `--container-max-width` không cần responsive riêng (khung tự nhỏ lại theo viewport nhờ `width:100%` + `max-width`).
- Gắn qua `wp_add_inline_style( 'tmnhanphat-variables', ... )` trong `inc/enqueue.php`, ngay sau khi enqueue `assets/css/global/variables.css` — stylesheet này load ở **mọi trang, mọi loại request**, nên override luôn có hiệu lực toàn site, không cần enqueue riêng cho từng loại trang.
- `assets/css/global/variables.css` vẫn giữ giá trị dự phòng tĩnh (phòng khi vì lý do nào đó inline style không tải được) — không phải nơi để sửa khoảng cách/chiều rộng, chỉ sửa qua Customizer.

**Quy tắc bắt buộc cho mọi section/template sau này:** nếu cần khung nội dung khớp với chuẩn chung của site, PHẢI dùng `var(--container-padding)`/`var(--container-max-width)` (trực tiếp hoặc qua class `.container` có sẵn) — **tuyệt đối không** viết `padding-left`/`padding-right`/`padding: 0 <số cứng>`/`max-width:<số cứng>` riêng cho section đó. Nếu 1 section thực sự cần kích thước KHÁC với chuẩn chung (trường hợp đặc biệt, hiếm — ví dụ Header đã làm với `--header-container-width`), phải giải thích rõ lý do trong comment và thêm setting riêng thay vì hardcode.

Mô hình "Global Settings → Layout" này là điểm khởi đầu cho các Global Setting khác trong tương lai (`Typography`, `Colors`...) — mỗi nhóm là 1 Section mới trong CÙNG Panel "Global Settings", không tạo Panel riêng cho từng nhóm nhỏ (khác với mục 26 — mục 26 áp dụng cho từng FEATURE/TRANG lớn, còn Global Settings gom các thiết lập nền tảng dùng chung toàn site vào 1 Panel duy nhất).

## 28. About Company Section — ẢNH THIẾT KẾ THẬT (PNG do designer xuất) làm nền, KHÔNG tự vẽ hình bằng CSS

Panel **"About Home"** (`inc/customizer/about-customizer.php`, priority 32, theo đúng mẫu mục 26) → Section **General/Content/Background Shape/Elevator Image/Responsive**. Helper tương ứng: `tmnhanphat_about_defaults()`/`tmnhanphat_get_about_mod()`/`tmnhanphat_render_about_css_vars()` trong `inc/template-functions.php`; template `template-parts/home/about.php`; style `assets/css/components/about.css`. **Đã refactor 2 lần** — bản v1 tự vẽ hình thang bằng `clip-path`, bản v2 đổi sang "lục giác dài" 6 điểm vẫn bằng `clip-path`, **bản v3 (hiện tại) bỏ HẲN clip-path/polygon/SVG/skew**, dùng ẢNH PNG THẬT do designer xuất từ Figma làm nền — mọi nội dung dưới đây là bản v3, thay thế hoàn toàn v1 và v2.

**Nguyên tắc cốt lõi bản v3: không "vẽ lại" thiết kế bằng CSS, mà RENDER TRỰC TIẾP asset Figma.** 2 ảnh design-cung-cấp:
- `Rectangle 19.png` (khối đỏ góc cạnh) → dùng làm `background-image` của `.about-home__shape`, một `<div>` THUẦN DECORATION (không phải container, không chứa nội dung nào bên trong).
- Ảnh thang máy PNG nền trong suốt → `.about-home__image`, một LAYER `position:absolute` ĐỘC LẬP, không nằm trong `.container`, không phải cột Grid/Flex, đè lên trên `.about-home__shape`.

Cả 2 ảnh đều upload qua Customizer (`Upload Shape Image`, `Upload Elevator Image`) — theme KHÔNG bundle sẵn file nhị phân nào, giống hệt cách Header Logo/Footer Logo/Partner Logo/Hero Background đã hoạt động (mục 22). Nếu chưa upload Shape Image, `Fallback Background Color` (mặc định `#cc322d`) vẫn hiện ra để section không trống trơn.

**Cấu trúc HTML bắt buộc** (`template-parts/home/about.php`) — Ảnh KHÔNG nằm trong `.container`, KHÔNG dùng Flex chia 2 cột:
```html
<section class="about-home">
  <div class="about-home__shape"></div>      <!-- background decoration -->
  <div class="container">
    <div class="about-home__content">…</div> <!-- Text -->
  </div>
  <div class="about-home__image">…</div>     <!-- Elevator PNG, layer riêng -->
</section>
```
Thứ tự layer (thấp→cao, khớp DOM order): nền trắng (`.about-home`) → `.about-home__shape` (z-index:1) → `.container` chứa Text (z-index:2) → `.about-home__image` (z-index mặc định 3, tuỳ chỉnh qua Customizer — luôn trên cùng).

**Vấn đề kỹ thuật cốt lõi (áp dụng khi định vị `.about-home__image` theo % chiều cao):** `.about-home` có `height:auto` (chiều cao tự nhiên theo Text, vì `.container`/`.about-home__content` vẫn nằm trong LUỒNG TÀI LIỆU bình thường ở bản v3 — không `position:absolute` như bản v2). Nếu để `.about-home__image` dùng `height:X%` trực tiếp trên `.about-home`, phần trăm đó sẽ KHÔNG resolve đúng chuẩn CSS (containing block "auto" không tính được %). **Giải pháp:** khai báo 1 custom property THAM CHIẾU cố định `--about-home-height: clamp(480px, 46vw, 640px)` ngay trên `.about-home` (thuần CSS, không qua PHP), rồi CẢ `.about-home` (`min-height`) LẪN `.about-home__image` (`height: calc(var(--about-home-height) * var(--about-elevator-ratio))`) đều dùng CHUNG biến này qua `calc()` — không phụ thuộc việc resolve % trên containing block, luôn nhất quán dù `.about-home` thực tế cao hơn giá trị tham chiếu vì Text dài.

**`.about-home__shape` dùng `inset:0`** (không phải `top:50%;transform:translateY(-50%);height:100%` như ví dụ gốc) — lý do tương tự: `inset:0` (tương đương `top:0;right:0;bottom:0;left:0`) tự phủ kín đúng kích thước `.about-home` THỰC TẾ bất kể `height:auto` hay cố định, không có vấn đề resolve `%`, đạt CÙNG hiệu ứng thị giác "phủ kín toàn bộ section" mà không có rủi ro kỹ thuật.

**"Elevator Size — Desktop/Tablet/Mobile (%)"** (giữ nguyên field ID `tmnhanphat_about_image_width_desktop/tablet/mobile` từ bản v1-v2, chỉ đổi nhãn + cách dùng): là % của `--about-home-height` (chiều cao), KHÔNG phải % chiều rộng cột — vì ảnh dùng `height: calc(...)` + `width:auto` + `object-fit:contain` để giữ nguyên tỉ lệ gốc (yêu cầu "không crop, không méo"), chiều rộng hiển thị HOÀN TOÀN do tỉ lệ khung hình gốc của PNG quyết định, không đặt cứng.

**Overlap Elevator lên Shape:** `.about-home__image` đứng ở `right:8%` (gần mép phải nhưng không sát hẳn) rồi `transform: translate(offset-x, offset-y)` tinh chỉnh thêm qua 2 setting "Horizontal/Vertical Offset" — vì không biết trước tỉ lệ vùng đỏ/vùng trong suốt CHÍNH XÁC bên trong file `Rectangle 19.png` thật (asset do designer cung cấp), giá trị `right:8%` + `offset-x:-20px` chỉ là điểm khởi đầu hợp lý — BẮT BUỘC tinh chỉnh lại Horizontal Offset qua Customizer sau khi đã upload 2 ảnh thật để đạt đúng tỉ lệ "35-40% Elevator nằm trên Shape" như Figma.

**Animation (mục 21, giữ nguyên cơ chế IntersectionObserver + progressive enhancement):** vì `.about-home__image` đã có `transform` riêng để định vị (offset + căn giữa dọc `calc(-50% + offset-y)`), hiệu ứng trượt-vào-khi-cuộn (slide-in) KHÔNG khai báo `transform` mới đè lên (sẽ mất vị trí gốc) mà CỘNG THÊM qua biến `--about-reveal-x` (đưa vào cùng biểu thức `translate()` gốc) — kỹ thuật "biến số cộng vào 1 công thức transform duy nhất" này áp dụng bất cứ khi nào 1 phần tử vừa cần `transform` để định vị vừa cần `transform` cho hiệu ứng, tránh 2 khai báo `transform` triệt tiêu lẫn nhau.

**Responsive:** giữ layer chồng lớp (Elevator đè lên Shape) xuyên suốt Desktop lẫn Tablet (chỉ giảm `--about-elevator-ratio` + cỡ chữ Heading qua `@media (max-width:991px)`). Chỉ ở Mobile (`@media (max-width:599px)`) mới bỏ `position:absolute` của `.container` và `.about-home__image` (Shape vẫn giữ `position:absolute;inset:0` với ảnh nền như cũ, chỉ "chiều cao" của nó tự co theo `.about-home` lúc này đã co lại theo luồng tài liệu — không cần đơn giản hoá gì thêm vì không còn `clip-path` để lo lỗi hiển thị), trả `.about-home` về `display:flex;flex-direction:column` bình thường. Thứ tự "Ảnh trước, Text sau" áp bằng class `.about-home--image-first { flex-direction: column-reverse }` trên chính `.about-home`. Riêng rule mobile reset `transform:none` của `.about-home__image` phải đặt SAU khối animation trong file CSS để thắng đúng theo thứ tự nguồn khi độ đặc hiệu (specificity) trùng với rule `.is-visible`.
