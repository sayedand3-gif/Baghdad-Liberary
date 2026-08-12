<?php
require_once '../config/db.php';

// دالة لمعالجة وتصحيح مسارات الصور تلقائياً
function fixImagePath($path) {
    if (empty($path)) return '';
    $cleanPath = ltrim(str_replace('../', '', $path), './');
    return '../' . $cleanPath;
}

// 1. استقبال ID العالم من الرابط
$scholar_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// 2. جلب بيانات العالم المختار
$stmt = $pdo->prepare("SELECT * FROM scholars WHERE id = ?");
$stmt->execute([$scholar_id]);
$scholar = $stmt->fetch();

// لو مفيش عالم بالـ ID ده، يجيب أول عالم متوفر في الداتا بيز
if (!$scholar) {
    $stmt = $pdo->query("SELECT * FROM scholars ORDER BY id ASC LIMIT 1");
    $scholar = $stmt->fetch();
}

// 3. جلب الكتب والمؤلفات الخاصة بهذا العالم
$books_stmt = $pdo->prepare("SELECT * FROM books WHERE scholar_id = ? OR author_id = ? OR author_name = ? ORDER BY id ASC");
$books_stmt->execute([$scholar['id'], $scholar['id'], $scholar['name']]);
$scholar_books = $books_stmt->fetchAll();

$scholarImg = fixImagePath($scholar['image_url'] ?? '');
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($scholar['name'] ?? 'تفاصيل العالم') ?> — مكتبة بغداد</title>
  
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

    .scholar-hero {
      display: flex;
      gap: 32px;
      background: var(--surface-1);
      border: 1px solid var(--border-gold);
      border-radius: var(--radius-md);
      padding: 36px;
      margin-bottom: 40px;
      align-items: flex-start;
    }

    @media (max-width: 768px) {
      .scholar-hero {
        flex-direction: column;
        align-items: center;
        text-align: center;
      }
    }

    .scholar-avatar {
      width: 180px;
      height: 180px;
      border-radius: 50%;
      border: 2px solid var(--border-gold);
      overflow: hidden;
      flex-shrink: 0;
      background: var(--surface-2);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .scholar-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .scholar-info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
      gap: 12px;
      background: var(--surface-2);
      padding: 14px 18px;
      border-radius: var(--radius-sm);
      border: 1px solid var(--border-subtle);
      margin-bottom: 20px;
    }

    .scholar-info-item {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .scholar-info-item .label {
      color: var(--text-muted);
      font-size: 0.75rem;
    }

    .scholar-info-item .value {
      color: var(--text-main);
      font-size: 0.88rem;
      font-weight: 600;
    }

    .books-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 24px;
      margin-top: 24px;
    }

    .book-card {
      width: calc(25% - 18px);
      min-width: 220px;
      background: var(--surface-1);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-sm);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      text-decoration: none;
      color: inherit;
      transition: transform var(--transition-fast), border-color var(--transition-fast);
    }

    .book-card:hover {
      transform: translateY(-4px);
      border-color: var(--border-gold);
    }

    @media (max-width: 992px) {
      .book-card { width: calc(33.333% - 16px); }
    }
    @media (max-width: 600px) {
      .book-card { width: 100%; }
    }
  </style>
</head>
<body>

  <!-- ===================== HEADER ===================== -->
  <header style="border-bottom: 1px solid var(--border-gold); background: var(--surface-1);">
    <div class="header-container">
      <a href="../index.php" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;">
        <span class="star8" style="width: 26px; height: 26px;"></span>
        <span class="font-title" style="font-size: 1.5rem; font-weight: 700;">مكتبة بغداد</span>
      </a>

      <nav class="header-nav font-ui">
        <a href="../index.php" style="color: var(--text-muted); text-decoration: none;">الرئيسية</a>
        <a href="library.php" style="color: var(--text-muted); text-decoration: none;">المكتبة</a>
        <a href="scholars.php" style="color: var(--gold-soft); text-decoration: none;">العلماء</a>
        <a href="inventions.php" style="color: var(--text-muted); text-decoration: none;">الاختراعات</a>
        <a href="../user/community.php" style="color: var(--text-muted); text-decoration: none;">المجتمع</a>
      </nav>

      <div class="header-actions">
        <a href="../auth/login.php" class="btn btn-outline" style="text-decoration: none;">تسجيل الدخول</a>
      </div>
    </div>
  </header>

  <!-- ===================== MAIN CONTENT ===================== -->
  <main style="max-width: 1180px; margin: 0 auto; padding: 50px 24px;">
    
    <!-- Scholar Bio Card -->
    <div class="scholar-hero illum-frame">
      
      <div class="scholar-avatar">
        <img src="<?= htmlspecialchars($scholarImg) ?>" 
             alt="<?= htmlspecialchars($scholar['name'] ?? '') ?>" 
             onerror="this.onerror=null; this.src='../assets/images/ibn-khaldon.jpeg';">
      </div>

      <div style="flex: 1; width: 100%;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 12px;">
          <div>
            <h1 class="font-title" style="font-size: 2.2rem; margin: 0 0 4px 0;">
              <?= htmlspecialchars($scholar['name'] ?? 'غير محدد') ?>
            </h1>
            <?php if (!empty($scholar['title_nickname'])): ?>
              <div class="font-ui" style="color: var(--gold-soft); font-size: 0.95rem;">
                <?= htmlspecialchars($scholar['title_nickname']) ?>
              </div>
            <?php endif; ?>
          </div>

          <a href="scholars.php" class="btn btn-outline" style="padding: 6px 14px; font-size: 0.85rem; text-decoration: none;">
            ← العودة لجميع العلماء
          </a>
        </div>

        <!-- شبكة التفاصيل الهيكلية (الميلاد، الوفاة، مكان الميلاد، العصر، التخصص) -->
        <div class="scholar-info-grid font-ui">
          <?php if (!empty($scholar['birth_date'])): ?>
            <div class="scholar-info-item">
              <span class="label">تاريخ الميلاد:</span>
              <span class="value"><?= htmlspecialchars($scholar['birth_date']) ?></span>
            </div>
          <?php endif; ?>

          <?php if (!empty($scholar['death_date'])): ?>
            <div class="scholar-info-item">
              <span class="label">تاريخ الوفاة:</span>
              <span class="value"><?= htmlspecialchars($scholar['death_date']) ?></span>
            </div>
          <?php endif; ?>

          <?php if (!empty($scholar['birthplace'])): ?>
            <div class="scholar-info-item">
              <span class="label">مكان الميلاد:</span>
              <span class="value"><?= htmlspecialchars($scholar['birthplace']) ?></span>
            </div>
          <?php endif; ?>

          <div class="scholar-info-item">
            <span class="label">العصر التاريخي:</span>
            <span class="value"><?= htmlspecialchars($scholar['era'] ?? 'غير محدد') ?></span>
          </div>

          <div class="scholar-info-item">
            <span class="label">التخصص:</span>
            <span class="value"><?= htmlspecialchars($scholar['specialty'] ?? 'غير محدد') ?></span>
          </div>
        </div>

        <p class="font-ui" style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.8; margin: 0;">
          <?= htmlspecialchars($scholar['bio'] ?? 'لا توجد نبذة مختصرة متاحة لهذا العالم حالياً.') ?>
        </p>

      </div>

    </div>

    <!-- Dynamic Books Section -->
    <section style="margin-top: 50px;">
      <h2 class="font-title" style="font-size: 1.6rem; border-bottom: 1px solid var(--border-gold); padding-bottom: 12px; margin-bottom: 24px;">
        أبرز مؤلفات <?= htmlspecialchars($scholar['name'] ?? '') ?> بالمكتبة
      </h2>

      <div class="books-grid">
        <?php if (!empty($scholar_books)): ?>
          <?php foreach ($scholar_books as $book): 
            $bookCover = fixImagePath($book['cover_image'] ?? '');
          ?>
            <a href="../reader-module/book-details.php?id=<?= $book['id'] ?>" class="book-card">
              
              <!-- صورة غلاف الكتاب -->
              <div style="height: 240px; background: #1a2234; display: flex; align-items: flex-end; padding: 16px; background-size: cover; background-position: center; <?= !empty($bookCover) ? "background-image: linear-gradient(to top, rgba(0,0,0,0.85), transparent), url('" . htmlspecialchars($bookCover) . "');" : "" ?>">
                <span class="font-title" style="font-size: 1.1rem; color: #fff; text-shadow: 0 2px 4px rgba(0,0,0,0.9);">
                  <?= htmlspecialchars($book['title']) ?>
                </span>
              </div>

              <!-- تفاصيل الكتاب -->
              <div style="padding: 14px;">
                <div class="font-ui" style="color: var(--gold-soft); font-size: 0.8rem;">
                  التصنيف: <?= htmlspecialchars($book['category'] ?? 'فلسفة وعلم اجتماع') ?>
                </div>
              </div>

            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <div style="width: 100%; background: var(--surface-1); border: 1px dashed var(--border-gold); padding: 30px; text-align: center; border-radius: var(--radius-sm);">
            <p class="font-ui" style="color: var(--text-muted); margin: 0;">
              لا توجد كتب مضافة حالياً لـ <?= htmlspecialchars($scholar['name'] ?? '') ?> في قاعدة البيانات.
            </p>
          </div>
        <?php endif; ?>
      </div>
    </section>

  </main>

  <!-- ===================== FOOTER ===================== -->
  <footer style="background: var(--surface-1); border-top: 1px solid var(--border-gold); padding: 30px 24px; margin-top: 60px; text-align: center;">
    <div class="font-ui" style="color: var(--text-muted); font-size: 0.85rem;">
      © 2026 مكتبة بغداد — جميع الحقوق محفوظة.
    </div>
  </footer>

</body>
</html>