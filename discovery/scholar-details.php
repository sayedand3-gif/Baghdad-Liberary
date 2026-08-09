<?php
require_once '../config/db.php';

// الحصول على id العالم من الرابط
$scholar_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// جلب بيانات العالم من قاعدة البيانات
$stmt = $pdo->prepare("SELECT * FROM scholars WHERE id = ?");
$stmt->execute([$scholar_id]);
$scholar = $stmt->fetch();

// في حالة عدم وجود العالم، التوجيه لصفحة العلماء
if (!$scholar) {
    header('Location: scholars.php');
    exit;
}
?>
<!-- ======================================================== -->
<!-- ======================================================== -->
<!-- ======================================================== -->



<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ابن خلدون — مكتبة بغداد</title>
  
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

    /* Scholar Profile Header */
    .scholar-header {
      display: flex;
      align-items: center;
      gap: 32px;
      background: var(--surface-1);
      border: 1px solid var(--border-gold);
      border-radius: var(--radius-md);
      padding: 40px;
      margin-bottom: 40px;
    }
    
    @media (max-width: 768px) {
      .scholar-header {
        flex-direction: column;
        text-align: center;
      }
    }

    .scholar-avatar-lg {
      width: 120px;
      height: 120px;
      min-width: 120px;
      border-radius: 50%;
      background: var(--surface-2);
      border: 3px solid var(--border-gold);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: var(--font-title);
      font-size: 3rem;
      color: var(--gold-primary);
      box-shadow: 0 0 20px rgba(212, 175, 55, 0.2);
    }

    /* Timeline Styles */
    .timeline {
      position: relative;
      padding-right: 30px;
      margin: 30px 0;
      border-right: 2px solid var(--border-subtle);
    }
    .timeline-item {
      position: relative;
      margin-bottom: 30px;
    }
    .timeline-item::before {
      content: '';
      position: absolute;
      right: -37px;
      top: 4px;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: var(--gold-primary);
      border: 3px solid var(--surface-1);
    }
    .timeline-year {
      font-family: var(--font-title);
      font-size: 1.2rem;
      color: var(--gold-soft);
      margin-bottom: 8px;
    }

    /* Books Grid (Reused style) */
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
      
      <a href="../index.php" style="display: flex; align-items: center; gap: 10px;">
        <span class="star8" style="width: 26px; height: 26px;"></span>
        <span class="font-title" style="font-size: 1.5rem; font-weight: 700;">مكتبة بغداد</span>
      </a>

      <nav class="header-nav font-ui">
        <a href="../index.php" style="color: var(--text-muted);">الرئيسية</a>
        <a href="library.php" style="color: var(--text-muted);">المكتبة</a>
        <a href="scholars.php" style="color: var(--gold-soft);">العلماء</a>
        <a href="inventions.php" style="color: var(--text-muted);">الاختراعات</a>
        <a href="../user/community.php" style="color: var(--text-muted);">المجتمع</a>
      </nav>

      <div class="header-actions">
        <div class="toggle-track" id="modeToggle" role="button">
          <div class="toggle-thumb" id="modeThumb">☾</div>
        </div>
      </div>

    </div>
  </header>

  <!-- ===================== MAIN CONTENT ===================== -->
  <main style="max-width: 900px; margin: 0 auto; padding: 50px 24px;">
    
    <!-- Scholar Profile -->
    <div class="scholar-header illum-frame">
     <div class="scholar-avatar" style="overflow: hidden; padding: 0; width: 120px; height: 120px; border-radius: 50%;">
  <?php if (!empty($scholar['image_url'])): ?>
    <img src="<?= htmlspecialchars($scholar['image_url']) ?>" alt="<?= htmlspecialchars($scholar['name']) ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;">
  <?php else: ?>
    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--gold-soft);">
      <?= mb_substr($scholar['name'], 0, 1, 'UTF-8') ?>
    </div>
  <?php endif; ?>
</div>
      <div>
        <h1 class="font-title"><?= htmlspecialchars($scholar['name']) ?></h1>        
<p class="font-ui" style="line-height: 1.8; color: var(--text-muted);">
  <?= htmlspecialchars($scholar['bio']) ?>
</p>
        <p class="font-ui" style="color: var(--gold-soft);"><?= htmlspecialchars($scholar['specialty']) ?> | <?= htmlspecialchars($scholar['era']) ?></p>
      </div>
    </div>

    <!-- Layout for Timeline and Books -->
    <div style="display: flex; flex-direction: column; gap: 50px;">
      
      <!-- Timeline Section -->
      <section>
        <h2 style="font-size: 1.6rem; border-bottom: 1px solid var(--border-gold); padding-bottom: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
          <span class="star8" style="width: 20px; height: 20px;"></span>
          محطات في حياته
        </h2>
        
        <div class="timeline">
          <div class="timeline-item">
            <div class="timeline-year">١٣٣٢ م (٧٣٢ هـ)</div>
            <h3 style="font-size: 1.1rem; margin-bottom: 6px;">مولده ونشأته</h3>
            <p class="font-ui" style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">وُلد في تونس لأسرة أندلسية عريقة، وحفظ القرآن الكريم في طفولته وتتلمذ على يد كبار علماء عصره في النحو والفقه.</p>
          </div>
          
          <div class="timeline-item">
            <div class="timeline-year">١٣٧٥ م</div>
            <h3 style="font-size: 1.1rem; margin-bottom: 6px;">كتابة المُقدمة</h3>
            <p class="font-ui" style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">اعتزل الناس في قلعة بني سلامة بالجزائر، وكتب رائعته الخالدة "مقدمة ابن خلدون" في خمسة أشهر فقط، مؤسساً بها علم العمران.</p>
          </div>

          <div class="timeline-item">
            <div class="timeline-year">١٣٨٢ م</div>
            <h3 style="font-size: 1.1rem; margin-bottom: 6px;">الرحلة إلى مصر</h3>
            <p class="font-ui" style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">انتقل إلى مصر حيث استُقبل بحفاوة، وعُيّن قاضياً لقضاة المالكية ومدرساً في الجامع الأزهر ومدرسة القمحية.</p>
          </div>
          
          <div class="timeline-item">
            <div class="timeline-year">١٤٠٦ م (٨٠٨ هـ)</div>
            <h3 style="font-size: 1.1rem; margin-bottom: 6px;">وفاته</h3>
            <p class="font-ui" style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">تُوفي في القاهرة ودُفن في مقابر الصوفية، تاركاً إرثاً فكرياً هائلاً استمر تأثيره حتى العصر الحديث.</p>
          </div>
        </div>
      </section>

      <!-- Scholar's Books Section -->
      <section>
        <h2 style="font-size: 1.6rem; border-bottom: 1px solid var(--border-gold); padding-bottom: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
          <span class="star8" style="width: 20px; height: 20px;"></span>
          مؤلفاته في المكتبة
        </h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
          
          <a href="../reader-module/book-details.php" class="book-card">
            <div style="height: 220px; background: linear-gradient(135deg, #1e2740, #2c625d); display: flex; align-items: flex-end; padding: 16px;">
              <span class="font-title" style="font-size: 1.2rem; color: #fff;">مقدمة ابن خلدون</span>
            </div>
            <div style="padding: 14px;">
              <div class="font-ui" style="color: var(--gold-soft); font-size: 0.85rem; margin-top: 6px;">★ 4.9 (تاريخ وفكر)</div>
            </div>
          </a>

          <a href="../reader-module/book-details.php" class="book-card">
            <div style="height: 220px; background: linear-gradient(135deg, #2b2418, #5b4a26); display: flex; align-items: flex-end; padding: 16px;">
              <span class="font-title" style="font-size: 1.2rem; color: #fff;">كتاب العبر</span>
            </div>
            <div style="padding: 14px;">
              <div class="font-ui" style="color: var(--gold-soft); font-size: 0.85rem; margin-top: 6px;">★ 4.8 (تاريخ)</div>
            </div>
          </a>

          <a href="../reader-module/book-details.php" class="book-card">
            <div style="height: 220px; background: linear-gradient(135deg, #241826, #4a2b4f); display: flex; align-items: flex-end; padding: 16px;">
              <span class="font-title" style="font-size: 1.2rem; color: #fff;">التعريف بابن خلدون</span>
            </div>
            <div style="padding: 14px;">
              <div class="font-ui" style="color: var(--gold-soft); font-size: 0.85rem; margin-top: 6px;">★ 4.6 (سيرة ذاتية)</div>
            </div>
          </a>

        </div>
      </section>

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