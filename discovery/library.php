<?php
require_once '../config/db.php';

$stmt = $pdo->query("SELECT * FROM books ORDER BY id ASC");
$books = $stmt->fetchAll();
?>
<!-- ================================================= -->
<!-- ================================================= -->
<!-- ================================================= -->


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>المكتبة — مكتبة بغداد</title>
  
  <!-- استدعاء ملف التنسيقات الرئيسي من الفولدر الأب -->
  <link rel="stylesheet" href="../assets/css/main.css">

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

    /* Library Specific Layouts */
    .filter-btn {
      padding: 8px 18px;
      background: var(--surface-1);
      border: 1px solid var(--border-subtle);
      border-radius: 20px;
      color: var(--text-muted);
      font-family: var(--font-ui);
      font-size: 0.85rem;
      cursor: pointer;
      transition: all var(--transition-fast);
    }
    .filter-btn.active, .filter-btn:hover {
      background: var(--gold-primary);
      color: var(--text-dark);
      border-color: var(--gold-primary);
    }

    .search-bar-input {
      width: 100%;
      max-width: 480px;
      padding: 12px 20px;
      background: var(--surface-1);
      border: 1px solid var(--border-gold);
      border-radius: 30px;
      color: var(--text-main);
      font-family: var(--font-body);
      font-size: 0.95rem;
      outline: none;
    }

    .book-card {
      background: var(--surface-1);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-sm);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: transform var(--transition-fast), border-color var(--transition-fast);
    }
    .book-card:hover {
      transform: translateY(-4px);
      border-color: var(--border-gold);
    }
  </style>
</head>
<body>

  <!-- ===================== HEADER ===================== -->
  <header style="border-bottom: 1px solid var(--border-gold); background: var(--surface-1);">
    <div class="header-container">
      
      <a href="../index.html" style="display: flex; align-items: center; gap: 10px;">
        <span class="star8" style="width: 26px; height: 26px;"></span>
        <span class="font-title" style="font-size: 1.5rem; font-weight: 700;">مكتبة بغداد</span>
      </a>

      <nav class="header-nav font-ui">
        <a href="../index.html" style="color: var(--text-muted);">الرئيسية</a>
        <a href="library.html" style="color: var(--gold-soft);">المكتبة</a>
        <a href="scholars.html" style="color: var(--text-muted);">العلماء</a>
        <a href="inventions.html" style="color: var(--text-muted);">الاختراعات</a>
        <a href="../user/community.html" style="color: var(--text-muted);">المجتمع</a>
      </nav>

      <div class="header-actions">
        <div class="toggle-track" id="modeToggle" role="button">
          <div class="toggle-thumb" id="modeThumb">☾</div>
        </div>
        <a href="../auth/login.html" class="btn btn-outline">تسجيل الدخول</a>
        <a href="../auth/register.html" class="btn btn-gold">ابدأ الآن</a>
      </div>

    </div>
  </header>

  <!-- ===================== MAIN CONTENT ===================== -->
  <main style="max-width: 1180px; margin: 0 auto; padding: 50px 24px;">
    
    <!-- Title & Search Bar -->
    <div style="text-align: center; margin-bottom: 40px;">
      <h1 style="font-size: 2.2rem; margin-bottom: 12px;">المكتبة الشاملة</h1>
      <p class="font-ui" style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 24px;">
        تصفح الآلاف من الكتب المترجمة، المصنفات الفلسفية، والتراث العلمي الميسر.
      </p>
      
      <form action="search-results.html" style="display: flex; justify-content: center;">
        <input type="text" class="search-bar-input" placeholder="ابحث باسم الكتاب، المؤلف، أو الموضوع...">
      </form>
    </div>

    <!-- Category Filter Tabs -->
    <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-bottom: 40px;">
      <button class="filter-btn active">الكل</button>
      <button class="filter-btn">الفلسفة والفكر</button>
      <button class="filter-btn">التاريخ والاجتماع</button>
      <button class="filter-btn">العلوم الطبيعية</button>
      <button class="filter-btn">الأدب والشعر</button>
      <button class="filter-btn">الكتب الصوتية</button>
    </div>

   <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 24px;">
  <?php foreach ($books as $book): ?>
    <a href="../reader-module/book-details.php?id=<?= $book['id'] ?>" class="book-card">
      <div style="height: 260px; background: linear-gradient(135deg, #1e2740, #2c625d); display: flex; align-items: flex-end; padding: 18px; background-size: cover; background-position: center; <?= !empty($book['cover_image']) ? "background-image: linear-gradient(to top, rgba(0,0,0,0.8), transparent), url('" . htmlspecialchars($book['cover_image']) . "');" : "" ?>">
        <span class="font-title" style="font-size: 1.3rem; color: #fff; text-shadow: 0 2px 4px rgba(0,0,0,0.8);"><?= htmlspecialchars($book['title']) ?></span>
      </div>
      <div style="padding: 16px;">
        <div style="font-weight: 700; font-size: 0.95rem;"><?= htmlspecialchars($book['author_name'] ?? '') ?></div>
        <div class="font-ui" style="color: var(--gold-soft); font-size: 0.85rem; margin-top: 8px;">★ 4.9 (<?= htmlspecialchars($book['category'] ?? '') ?>)</div>
      </div>
    </a>
  <?php endforeach; ?>
</div>

  </main>

  <!-- ===================== FOOTER ===================== -->
  <footer style="background: var(--surface-1); border-top: 1px solid var(--border-gold); padding: 40px 24px 20px; margin-top: 60px;">
    <div style="max-width: 1180px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
      <div style="display: flex; align-items: center; gap: 10px;">
        <span class="star8" style="width: 20px; height: 20px;"></span>
        <span class="font-title" style="font-size: 1.2rem; font-weight: 700;">مكتبة بغداد</span>
      </div>
      <div class="font-ui" style="display: flex; gap: 20px; font-size: 0.85rem; color: var(--text-muted);">
        <a href="../system/about-faq.html">عن المكتبة</a>
        <a href="../system/about-faq.html">الأسئلة الشائعة</a>
        <a href="../system/about-faq.html">اتصل بنا</a>
      </div>
      <div class="font-ui" style="color: var(--text-muted); font-size: 0.85rem;">
        © 2026 مكتبة بغداد — بيت الحكمة الرقمي. جميع الحقوق محفوظة.
      </div>
    </div>
  </footer>

  <script src="../assets/js/main.js"></script>
</body>
</html>