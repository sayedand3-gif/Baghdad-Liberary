<!doctype html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>مكتبتي الخاصة — مكتبة بغداد</title>

    <!-- استدعاء ملف التنسيقات الرئيسي -->
    <link rel="stylesheet" href="../assets/css/main.css" />

    <style>
      .header-container {
        max-width: 1180px;
        margin: 0 auto;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
      }
      .header-nav {
        display: flex;
        gap: 28px;
        font-size: 0.95rem;
      }
      .header-actions {
        display: flex;
        align-items: center;
        gap: 16px;
      }

      .toggle-track {
        width: 50px;
        height: 26px;
        border-radius: 20px;
        background: var(--surface-2);
        border: 1px solid var(--border-gold);
        position: relative;
        cursor: pointer;
      }
      .toggle-thumb {
        position: absolute;
        top: 2px;
        right: 2px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--gold-primary);
        transition: transform 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        color: var(--text-dark);
      }
      html.light .toggle-thumb {
        transform: translateX(-24px);
      }

      /* Tabs Filter Bar */
      .tabs-bar {
        display: flex;
        gap: 12px;
        border-bottom: 1px solid var(--border-subtle);
        margin-top: 30px;
        margin-bottom: 36px;
      }

      .tab-btn {
        padding: 12px 20px;
        background: none;
        border: none;
        color: var(--text-muted);
        font-family: var(--font-ui);
        font-size: 0.95rem;
        cursor: pointer;
        position: relative;
        transition: color var(--transition-fast);
      }
      .tab-btn:hover,
      .tab-btn.active {
        color: var(--gold-soft);
        font-weight: 700;
      }
      .tab-btn.active::after {
        content: "";
        position: absolute;
        bottom: -1px;
        right: 0;
        left: 0;
        height: 2px;
        background: var(--gold-primary);
      }

      /* Personal Library Grid */
      .library-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
      }

      .my-book-card {
        background: var(--surface-1);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition:
          transform var(--transition-fast),
          border-color var(--transition-fast);
      }
      .my-book-card:hover {
        transform: translateY(-4px);
        border-color: var(--border-gold);
      }

      .book-thumb-small {
        height: 140px;
        background: linear-gradient(135deg, #1e2740, #2c625d);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: flex-end;
        padding: 12px;
        margin-bottom: 16px;
      }

      .progress-bar-sm {
        height: 4px;
        background: var(--surface-2);
        border-radius: 2px;
        overflow: hidden;
        margin-top: 8px;
      }
      .progress-fill-sm {
        height: 100%;
        background: var(--gold-primary);
      }
    </style>
  </head>
  <body>
    <!-- ===================== HEADER ===================== -->
    <header
      style="
        border-bottom: 1px solid var(--border-gold);
        background: var(--surface-1);
      "
    >
      <div class="header-container">
        <a
          href="../index.php"
          style="display: flex; align-items: center; gap: 10px"
        >
          <span class="star8" style="width: 26px; height: 26px"></span>
          <span class="font-title" style="font-size: 1.5rem; font-weight: 700"
            >مكتبة بغداد</span
          >
        </a>

        <nav class="header-nav font-ui">
          <a href="../index.php" style="color: var(--text-muted)">الرئيسية</a>
          <a href="../discovery/library.php" style="color: var(--text-muted)"
            >المكتبة</a
          >
          <a href="../discovery/scholars.php" style="color: var(--text-muted)"
            >العلماء</a
          >
          <a href="../discovery/inventions.php" style="color: var(--text-muted)"
            >الاختراعات</a
          >
          <a href="community.php" style="color: var(--text-muted)">المجتمع</a>
        </nav>

        <div class="header-actions">
          <div class="toggle-track" id="modeToggle" role="button">
            <div class="toggle-thumb" id="modeThumb">☾</div>
          </div>
          <a
            href="profile.php"
            class="btn btn-outline"
            style="font-size: 0.85rem; padding: 6px 14px"
            >حسابي</a
          >
        </div>
      </div>
    </header>

    <!-- ===================== MAIN CONTENT ===================== -->
    <main style="max-width: 1180px; margin: 0 auto; padding: 40px 24px">
      <div
        style="
          display: flex;
          justify-content: space-between;
          align-items: center;
          flex-wrap: wrap;
          gap: 16px;
        "
      >
        <div>
          <h1 style="font-size: 2rem; margin-bottom: 6px">
            مكتبتي الخاصة ورفوفي
          </h1>
          <p
            class="font-ui"
            style="color: var(--text-muted); font-size: 0.95rem"
          >
            مساحتك الشخصية لتتبع تقدم القراءة وإدارة كتبك المحفوظة.
          </p>
        </div>
      </div>

      <!-- Shelf Tabs -->
      <div class="tabs-bar font-ui">
        <button class="tab-btn active">أقرأ حالياً (٢)</button>
        <button class="tab-btn">المكتملة (١٤)</button>
        <button class="tab-btn">قائمة القراءة لاحقاً (٥)</button>
      </div>

      <!-- Personal Library Grid -->
      <div class="library-grid">
        <!-- Book 1 -->
        <div class="my-book-card illum-frame">
          <div>
            <div class="book-thumb-small">
              <span class="font-title" style="color: #fff; font-size: 1.1rem"
                >مقدمة ابن خلدون</span
              >
            </div>
            <h2 style="font-size: 1.15rem; margin-bottom: 4px">
              مقدمة ابن خلدون
            </h2>
            <div
              class="font-ui"
              style="
                color: var(--gold-soft);
                font-size: 0.85rem;
                margin-bottom: 12px;
              "
            >
              ابن خلدون
            </div>

            <div
              class="font-ui"
              style="font-size: 0.8rem; color: var(--text-muted)"
            >
              <span>تم إنجاز ٧٪ (صفحة ٤٢ من ٦٤٠)</span>
              <div class="progress-bar-sm">
                <div class="progress-fill-sm" style="width: 7%"></div>
              </div>
            </div>
          </div>

          <div
            style="margin-top: 20px; display: flex; gap: 8px"
            class="font-ui"
          >
            <a
              href="../reader-module/interactive-reader.php"
              class="btn btn-gold"
              style="
                flex: 1;
                text-align: center;
                font-size: 0.85rem;
                padding: 8px;
              "
              >متابعة القراءة</a
            >
          </div>
        </div>

        <!-- Book 2 -->
        <div class="my-book-card illum-frame">
          <div>
            <div
              class="book-thumb-small"
              style="background: linear-gradient(135deg, #2b2418, #5b4a26)"
            >
              <span class="font-title" style="color: #fff; font-size: 1.1rem"
                >القانون في الطب</span
              >
            </div>
            <h2 style="font-size: 1.15rem; margin-bottom: 4px">
              القانون في الطب
            </h2>
            <div
              class="font-ui"
              style="
                color: var(--gold-soft);
                font-size: 0.85rem;
                margin-bottom: 12px;
              "
            >
              ابن سينا
            </div>

            <div
              class="font-ui"
              style="font-size: 0.8rem; color: var(--text-muted)"
            >
              <span>تم إنجاز ٤٥٪ (المجلد الثاني)</span>
              <div class="progress-bar-sm">
                <div class="progress-fill-sm" style="width: 45%"></div>
              </div>
            </div>
          </div>

          <div
            style="margin-top: 20px; display: flex; gap: 8px"
            class="font-ui"
          >
            <a
              href="../reader-module/interactive-reader.php"
              class="btn btn-gold"
              style="
                flex: 1;
                text-align: center;
                font-size: 0.85rem;
                padding: 8px;
              "
              >متابعة القراءة</a
            >
          </div>
        </div>
      </div>
    </main>

    <!-- ===================== FOOTER ===================== -->
    <footer
      style="
        background: var(--surface-1);
        border-top: 1px solid var(--border-gold);
        padding: 40px 24px 20px;
        margin-top: 60px;
      "
    >
      <div
        style="
          max-width: 1180px;
          margin: 0 auto;
          display: flex;
          justify-content: space-between;
          align-items: center;
          flex-wrap: wrap;
          gap: 20px;
        "
      >
        <div style="display: flex; align-items: center; gap: 10px">
          <span class="star8" style="width: 20px; height: 20px"></span>
          <span class="font-title" style="font-size: 1.2rem; font-weight: 700"
            >مكتبة بغداد</span
          >
        </div>
        <div
          class="font-ui"
          style="
            display: flex;
            gap: 20px;
            font-size: 0.85rem;
            color: var(--text-muted);
          "
        >
          <a href="../system/about-faq.php">عن المكتبة</a>
          <a href="../system/about-faq.php">الأسئلة الشائعة</a>
          <a href="../system/about-faq.php">اتصل بنا</a>
        </div>
        <div
          class="font-ui"
          style="color: var(--text-muted); font-size: 0.85rem"
        >
          © 2026 مكتبة بغداد — بيت الحكمة الرقمي. جميع الحقوق محفوظة.
        </div>
      </div>
    </footer>

    <script src="../assets/js/main.js"></script>
  </body>
</html>
