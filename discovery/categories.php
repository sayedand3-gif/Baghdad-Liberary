<!doctype html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>التصنيفات والأقسام — مكتبة بغداد</title>

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

      /* Category Cards Layout */
      .category-card {
        background: var(--surface-1);
        border: 1px solid var(--border-gold);
        border-radius: var(--radius-md);
        padding: 30px 24px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition:
          transform var(--transition-fast),
          box-shadow var(--transition-fast);
        text-align: right;
      }
      .category-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-card);
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
          <a href="library.php" style="color: var(--gold-soft)">المكتبة</a>
          <a href="scholars.php" style="color: var(--text-muted)">العلماء</a>
          <a href="inventions.php" style="color: var(--text-muted)"
            >الاختراعات</a
          >
          <a href="../user/community.php" style="color: var(--text-muted)"
            >المجتمع</a
          >
        </nav>

        <div class="header-actions">
          <div class="toggle-track" id="modeToggle" role="button">
            <div class="toggle-thumb" id="modeThumb">☾</div>
          </div>
          <a href="../auth/login.php" class="btn btn-outline">تسجيل الدخول</a>
          <a href="../auth/register.php" class="btn btn-gold">ابدأ الآن</a>
        </div>
      </div>
    </header>

    <!-- ===================== MAIN CONTENT ===================== -->
    <main style="max-width: 1180px; margin: 0 auto; padding: 50px 24px">
      <div style="text-align: center; margin-bottom: 44px">
        <h1 style="font-size: 2.2rem; margin-bottom: 10px">تصنيفات الكتب</h1>
        <p class="font-ui" style="color: var(--text-muted); font-size: 0.95rem">
          استكشف محتويات بيت الحكمة الرقمي مقسمة حسب التخصصات والعلوم
        </p>
      </div>

      <!-- Categories Grid -->
      <div
        style="
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
          gap: 24px;
        "
      >
        <!-- Category 1 -->
        <a href="library.php" class="category-card illum-frame">
          <div>
            <span
              class="star8"
              style="width: 24px; height: 24px; margin-bottom: 16px"
            ></span>
            <h2 style="font-size: 1.4rem; margin-bottom: 8px">
              الفلسفة والمنطق
            </h2>
            <p
              class="font-ui"
              style="
                color: var(--text-muted);
                font-size: 0.85rem;
                line-height: 1.6;
              "
            >
              أمهات كتب الفلسفة التراثية والمعاصرة، مناهج التفكير، وأعمال كبار
              الفلاسفة.
            </p>
          </div>
          <div
            class="font-ui"
            style="
              margin-top: 24px;
              font-size: 0.85rem;
              color: var(--gold-soft);
              font-weight: 700;
            "
          >
            ٣,٤٢٠ كتابًا ←
          </div>
        </a>

        <!-- Category 2 -->
        <a href="library.php" class="category-card illum-frame">
          <div>
            <span
              class="star8"
              style="width: 24px; height: 24px; margin-bottom: 16px"
            ></span>
            <h2 style="font-size: 1.4rem; margin-bottom: 8px">
              التاريخ وعلم الاجتماع
            </h2>
            <p
              class="font-ui"
              style="
                color: var(--text-muted);
                font-size: 0.85rem;
                line-height: 1.6;
              "
            >
              دراسات الحضارات، العمارة والتاريخ الإسلامي، ونظريات عمران الشعوب.
            </p>
          </div>
          <div
            class="font-ui"
            style="
              margin-top: 24px;
              font-size: 0.85rem;
              color: var(--gold-soft);
              font-weight: 700;
            "
          >
            ٤,١٥٠ كتابًا ←
          </div>
        </a>

        <!-- Category 3 -->
        <a href="library.php" class="category-card illum-frame">
          <div>
            <span
              class="star8"
              style="width: 24px; height: 24px; margin-bottom: 16px"
            ></span>
            <h2 style="font-size: 1.4rem; margin-bottom: 8px">
              العلوم الطبيعية والطب
            </h2>
            <p
              class="font-ui"
              style="
                color: var(--text-muted);
                font-size: 0.85rem;
                line-height: 1.6;
              "
            >
              مخطوطات وبحوث الفيزياء، البصريات، الكيمياء، والفلك المترجمة.
            </p>
          </div>
          <div
            class="font-ui"
            style="
              margin-top: 24px;
              font-size: 0.85rem;
              color: var(--gold-soft);
              font-weight: 700;
            "
          >
            ٢,٨٩٠ كتابًا ←
          </div>
        </a>

        <!-- Category 4 -->
        <a href="library.php" class="category-card illum-frame">
          <div>
            <span
              class="star8"
              style="width: 24px; height: 24px; margin-bottom: 16px"
            ></span>
            <h2 style="font-size: 1.4rem; margin-bottom: 8px">الأدب والشعر</h2>
            <p
              class="font-ui"
              style="
                color: var(--text-muted);
                font-size: 0.85rem;
                line-height: 1.6;
              "
            >
              دواوين العرب، الملاحم الأدبية العالمة، والقصص الرائعة عبر التاريخ.
            </p>
          </div>
          <div
            class="font-ui"
            style="
              margin-top: 24px;
              font-size: 0.85rem;
              color: var(--gold-soft);
              font-weight: 700;
            "
          >
            ٥,٦٠٠ كتابًا ←
          </div>
        </a>
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
