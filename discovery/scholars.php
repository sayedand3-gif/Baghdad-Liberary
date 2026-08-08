<?php

require_once '../config/db.php';

$stmt = $pdo->query("SELECT * FROM scholars ORDER BY id ASC");
$scholars = $stmt->fetchAll();
?>
<!-- ======================================== -->
<!-- ======================================== -->
<!-- ======================================== -->


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>دليل العلماء والفلاسفة — مكتبة بغداد</title>
  
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

    /* Scholar Cards Grid Layout */
    .scholars-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 24px;
      margin-top: 40px;
    }

    .scholar-card {
      background: var(--surface-1);
      border: 1px solid var(--border-gold);
      border-radius: var(--radius-md);
      padding: 28px 24px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: transform var(--transition-fast), box-shadow var(--transition-fast);
    }
    .scholar-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-card);
    }

    .scholar-avatar {
      width: 64px;
      height: 64px;
      border-radius: 50%;
      background: var(--surface-2);
      border: 2px solid var(--border-gold);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: var(--font-title);
      font-size: 1.4rem;
      color: var(--gold-soft);
      margin-bottom: 16px;
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
        <a href="library.html" style="color: var(--text-muted);">المكتبة</a>
        <a href="scholars.html" style="color: var(--gold-soft);">العلماء</a>
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
    
    <div style="text-align: center; margin-bottom: 40px;">
      <h1 style="font-size: 2.2rem; margin-bottom: 10px;">روّاد الفكر والعلوم</h1>
      <p class="font-ui" style="color: var(--text-muted); font-size: 0.95rem;">
        استكشف سير العلماء والفلاسفة الذين شكلوا المعرفة الإنسانية ووضعوا لبنات العلوم الحديثة.
      </p>
    </div>

   <!-- Scholars Grid (Dynamic with Images) -->
<div class="scholars-grid">
  <?php foreach ($scholars as $scholar): ?>
    <a href="scholar-details.php?id=<?= $scholar['id'] ?>" class="scholar-card illum-frame">
      <div>
        
        <div class="scholar-avatar" style="overflow: hidden; padding: 0; width: 60px; height: 60px; border-radius: 50%;">
  <?php if (!empty($scholar['image_url'])): ?>
    <img src="<?= htmlspecialchars($scholar['image_url']) ?>" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;">
  <?php else: ?>
    <?= mb_substr($scholar['name'], 0, 1, 'UTF-8') ?>
  <?php endif; ?>
</div>

        <h2 style="font-size: 1.4rem; margin-bottom: 4px; margin-top: 12px;"><?= htmlspecialchars($scholar['name']) ?></h2>
        <div class="font-ui" style="color: var(--gold-soft); font-size: 0.85rem; margin-bottom: 12px;"><?= htmlspecialchars($scholar['specialty']) ?></div>
        <p class="font-ui" style="color: var(--text-muted); font-size: 0.85rem; line-height: 1.6;">
          <?= htmlspecialchars($scholar['bio']) ?>
        </p>
      </div>
      <div class="font-ui" style="margin-top: 20px; font-size: 0.8rem; color: var(--text-muted); border-top: 1px solid var(--border-subtle); padding-top: 12px; display: flex; justify-content: space-between;">
        <span>العصر: <?= htmlspecialchars($scholar['era']) ?></span>
        <span style="color: var(--gold-soft); font-weight: 700;">عرض التفاصيل ←</span>
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