<?php 
// Include Database connection
require_once __DIR__ . '/../config/db.php';

// Fetch inventions list from database
$stmt = $pdo->query("SELECT * FROM inventions ORDER BY id ASC");
$inventions = $stmt->fetchAll();
?>
<!-- ================================================== -->
<!-- ================================================== -->
<!-- ================================================== -->


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>معرض الاختراعات والأدوات — مكتبة بغداد</title>
  
  <!-- استدعاء ملف التنسيقات الرئيسي -->
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

    /* Inventions Grid Layout */
    .inventions-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 28px;
      margin-top: 40px;
    }

    .invention-card {
      background: var(--surface-1);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-md);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: transform var(--transition-fast), border-color var(--transition-fast);
    }
    .invention-card:hover {
      transform: translateY(-5px);
      border-color: var(--border-gold);
    }

    .invention-media {
      height: 200px;
      background: linear-gradient(135deg, var(--surface-2), #2c2214);
      display: flex;
      align-items: center;
      justify-content: center;
      border-bottom: 1px solid var(--border-subtle);
      position: relative;
    }

    .invention-badge {
      position: absolute;
      top: 16px;
      right: 16px;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(4px);
      border: 1px solid var(--border-gold);
      border-radius: 20px;
      padding: 4px 12px;
      font-family: var(--font-ui);
      font-size: 0.75rem;
      color: var(--gold-soft);
    }
  </style>
</head>
<body>

  <!-- ===================== HEADER ===================== -->
  <header style="border-bottom: 1px solid var(--border-gold); background: var(--surface-1);">
    <div class="header-container">
      
      <a href="../index.php" style="display: flex; align-items: center; gap: 10px;">
        <span class="star8" style="width: 26px; height: 26px;"></span>
        <span class="font-title" style="font-size: 1.5rem; font-weight: 700;">مكتبة بغداد</span>
      </a>

      <nav class="header-nav font-ui">
        <a href="../index.php" style="color: var(--text-muted);">الرئيسية</a>
        <a href="library.php" style="color: var(--text-muted);">المكتبة</a>
        <a href="scholars.php" style="color: var(--text-muted);">العلماء</a>
        <a href="inventions.php" style="color: var(--gold-soft);">الاختراعات</a>
        <a href="../user/community.php" style="color: var(--text-muted);">المجتمع</a>
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
  <main style="max-width: 1180px; margin: 0 auto; padding: 50px 24px;">
    
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-size: 2.2rem; margin-bottom: 10px;">روائع الاختراعات والابتكارات</h1>
        <p class="font-ui" style="color: var(--text-muted); font-size: 0.95rem;">
            تصفح الأجهزة والآلات التراثية التي ابتكرها علماء الحضارة الإسلامية وغيرت مجرى التاريخ.
        </p>
    </div>

  <div class="inventions-grid">
  <?php foreach ($inventions as $invention): ?>
    <div class="invention-card illum-frame">
      <div class="invention-image">
        <?php if (!empty($invention['image_url'])): ?>
          <img src="<?= htmlspecialchars($invention['image_url']) ?>" alt="<?= htmlspecialchars($invention['title']) ?>">
        <?php endif; ?>
      </div>
      <h3><?= htmlspecialchars($invention['title']) ?></h3>
      <p><?= htmlspecialchars($invention['description']) ?></p>
      <span class="inventor">المخترع: <?= htmlspecialchars($invention['inventor']) ?></span>
    </div>
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
        <a href="../system/about-faq.php">عن المكتبة</a>
        <a href="../system/about-faq.php">الأسئلة الشائعة</a>
        <a href="../system/about-faq.php">اتصل بنا</a>
      </div>
      <div class="font-ui" style="color: var(--text-muted); font-size: 0.85rem;">
        © 2026 مكتبة بغداد — بيت الحكمة الرقمي. جميع الحقوق محفوظة.
      </div>
    </div>
  </footer>

  <script src="../assets/js/main.js"></script>
</body>
</html>