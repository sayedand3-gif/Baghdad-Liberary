<?php
require_once '../config/db.php';

$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// جلب بيانات الكتاب مع اسم العالم في استعلام واحد متكامل
$stmt = $pdo->prepare("SELECT books.*, COALESCE(books.author_name, scholars.name) AS author_display 
                       FROM books 
                       LEFT JOIN scholars ON books.author_id = scholars.id 
                       WHERE books.id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

if (!$book) {
    header('Location: ../discovery/library.php');
    exit;
}

$authorName = !empty($book['author_name']) ? $book['author_name'] : ($book['author_display'] ?? 'غير محدد');
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($book['title']) ?> — مكتبة بغداد</title>
  
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

    .book-hero {
      display: grid;
      grid-template-columns: 280px 1fr;
      gap: 40px;
      background: var(--surface-1);
      border: 1px solid var(--border-gold);
      border-radius: var(--radius-md);
      padding: 36px;
      margin-bottom: 40px;
    }

    @media (max-width: 768px) {
      .book-hero {
        grid-template-columns: 1fr;
        text-align: center;
      }
    }

    .book-cover-lg {
      height: 380px;
      background: linear-gradient(135deg, #1e2740, #2c625d);
      border-radius: var(--radius-sm);
      border: 1px solid var(--border-gold);
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      padding: 24px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.3);
      background-size: cover;
      background-position: center;
    }

    .book-meta-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
      gap: 16px;
      margin: 24px 0;
      padding: 16px 0;
      border-top: 1px solid var(--border-subtle);
      border-bottom: 1px solid var(--border-subtle);
    }

    .meta-item-label {
      font-family: var(--font-ui);
      font-size: 0.8rem;
      color: var(--text-muted);
      margin-bottom: 4px;
    }
    .meta-item-value {
      font-family: var(--font-ui);
      font-size: 0.95rem;
      font-weight: 700;
      color: var(--gold-soft);
    }

    /* ================= Premium Modal Styles ================= */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(10, 15, 29, 0.82);
      backdrop-filter: blur(6px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .modal-overlay.active {
      opacity: 1;
      visibility: visible;
    }

    .premium-card {
      background: var(--surface-1);
      border: 1px solid var(--border-gold);
      border-radius: var(--radius-md);
      width: 90%;
      max-width: 440px;
      padding: 32px 28px;
      text-align: center;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
      transform: scale(0.9);
      transition: transform 0.3s ease;
      position: relative;
    }

    .modal-overlay.active .premium-card {
      transform: scale(1);
    }

    .premium-badge {
      display: inline-block;
      background: rgba(212, 175, 55, 0.15);
      border: 1px solid var(--border-gold);
      color: var(--gold-soft);
      font-family: var(--font-ui);
      font-size: 0.8rem;
      padding: 4px 14px;
      border-radius: 20px;
      margin-bottom: 16px;
    }

    .premium-price {
      font-family: var(--font-title);
      font-size: 2.2rem;
      color: var(--gold-soft);
      margin: 14px 0 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }

    .premium-price span {
      font-size: 1rem;
      color: var(--text-muted);
      font-family: var(--font-ui);
    }
  </style>
</head>
<body>

  <header style="border-bottom: 1px solid var(--border-gold); background: var(--surface-1);">
    <div class="header-container">
      
      <a href="../index.php" style="display: flex; align-items: center; gap: 10px;">
        <span class="star8" style="width: 26px; height: 26px;"></span>
        <span class="font-title" style="font-size: 1.5rem; font-weight: 700;">مكتبة بغداد</span>
      </a>

      <nav class="header-nav font-ui">
        <a href="../index.php" style="color: var(--text-muted);">الرئيسية</a>
        <a href="../discovery/library.php" style="color: var(--gold-soft);">المكتبة</a>
        <a href="../discovery/scholars.php" style="color: var(--text-muted);">العلماء</a>
        <a href="../discovery/inventions.php" style="color: var(--text-muted);">الاختراعات</a>
        <a href="../user/community.php" style="color: var(--text-muted);">المجتمع</a>
      </nav>

      <div class="header-actions">
        <div class="toggle-track" id="modeToggle" role="button">
          <div class="toggle-thumb" id="modeThumb">☾</div>
        </div>
        <a href="../user/profile.php" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.85rem;">حسابي</a>
      </div>

    </div>
  </header>

  <main style="max-width: 1180px; margin: 0 auto; padding: 40px 24px;">
    
    <div class="book-hero illum-frame">
      
      <?php 
        $coverImg = !empty($book['cover_image']) ? ltrim($book['cover_image'], './') : '';
      ?>
      <div class="book-cover-lg" style="<?= !empty($coverImg) ? "background-image: linear-gradient(to top, rgba(0,0,0,0.8), transparent), url('../" . htmlspecialchars($coverImg) . "');" : "" ?>">
        <span class="star8" style="width: 32px; height: 32px; margin-bottom: 12px;"></span>
        <h1 class="font-title"><?= htmlspecialchars($book['title']) ?></h1>
        <p class="font-ui" style="color: var(--gold-soft);"><?= htmlspecialchars($authorName) ?> | <?= htmlspecialchars($book['category'] ?? '') ?></p>
      </div>

      <div style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
          <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
            <div>
              <h2 style="font-size: 2rem; margin-bottom: 6px;"><?= htmlspecialchars($book['title']) ?></h2>
              <span class="font-ui" style="color: var(--gold-soft); font-size: 1.1rem;">
                تأليف: <?= htmlspecialchars($authorName) ?>
              </span>
            </div>
            <span class="font-ui" style="background: var(--surface-2); border: 1px solid var(--border-gold); padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; color: var(--gold-soft);">
              ★ <?= htmlspecialchars($book['rating'] ?? '4.85') ?>
            </span>
          </div>

          <div class="book-meta-grid">
            <div>
              <div class="meta-item-label">التصنيف</div>
              <div class="meta-item-value"><?= htmlspecialchars($book['category'] ?? 'عام') ?></div>
            </div>
            <div>
              <div class="meta-item-label">عدد الصفحات</div>
              <div class="meta-item-value"><?= htmlspecialchars($book['pages_count'] ?? $book['pages'] ?? 'غير محدد') ?> صفحة</div>
            </div>
            <div>
              <div class="meta-item-label">سنة التأليف</div>
              <div class="meta-item-value"><?= htmlspecialchars($book['publish_year'] ?? $book['year'] ?? 'غير محدد') ?></div>
            </div>
            <div>
              <div class="meta-item-label">اللغة</div>
              <div class="meta-item-value"><?= htmlspecialchars($book['language'] ?? 'العربية') ?></div>
            </div>
          </div>

          <p class="font-ui" style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.8; margin-bottom: 24px;">
            <?= htmlspecialchars($book['description'] ?? 'لا يوجد وصف متاح لهذا الكتاب حالياً.') ?>
          </p>
        </div>

        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
          <a href="interactive-reader.php?id=<?= $book['id'] ?>" class="btn btn-gold" style="padding: 12px 28px; font-size: 1rem;">
            📖 ابدأ القراءة الآن
          </a>
          
          <!-- زر الكتاب الصوتي -->
          <button type="button" class="btn btn-outline" onclick="showPremiumModal('الكتاب الصوتي')">
            🎧 استمع للكتاب الصوتي
          </button>

          <!-- زر المساعد الذكي -->
          <button type="button" class="btn btn-outline" onclick="showPremiumModal('المساعد الذكي')">
            ✨ المساعد الذكي
          </button>
        </div>

      </div>

    </div>

  </main>

  <!-- ================= Premium Feature Modal ================= -->
  <div class="modal-overlay" id="premiumModal" onclick="closePremiumModal(event)">
    <div class="premium-card illum-frame">
      <span class="star8" style="width: 32px; height: 32px; margin: 0 auto 16px;"></span>
      <div class="premium-badge">اشتراك متميز (Premium)</div>
      <h3 style="font-size: 1.5rem; margin-bottom: 8px;">ميزة <span id="featureTitleName" style="color: var(--gold-soft);"></span></h3>
      <p class="font-ui" style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
        هذه الميزة الفريدة متوفرة حصرياً لأعضاء العضوية الذهبية الفاخرة في بيت الحكمة الرقمي.
      </p>

      <div class="premium-price">
        120$ <span>/ سنوياً</span>
      </div>

      <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 24px;">
        <button type="button" class="btn btn-gold" style="padding: 12px; width: 100%;" onclick="alert('سيتم تحويلك لبوابة الدفع قريباً!')">
          اشترك الآن وسجل الوصول
        </button>
        <button type="button" class="btn btn-outline" style="padding: 10px; width: 100%; border: none;" onclick="closePremiumModalDirect()">
          إغلاق
        </button>
      </div>
    </div>
  </div>

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

  <!-- ملف JS الأساسي -->
  <script src="../assets/js/main.js"></script>

  <!-- كود تشغيل النوافذ المنبثقة -->
  <script>
    function showPremiumModal(featureName) {
      document.getElementById('featureTitleName').innerText = featureName;
      document.getElementById('premiumModal').classList.add('active');
    }

    function closePremiumModalDirect() {
      document.getElementById('premiumModal').classList.remove('active');
    }

    function closePremiumModal(event) {
      if (event.target === document.getElementById('premiumModal')) {
        closePremiumModalDirect();
      }
    }
  </script>
</body>
</html>