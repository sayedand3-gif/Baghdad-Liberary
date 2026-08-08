<?php
require_once '../config/db.php';

$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

if (!$book) {
    header('Location: ../discovery/library.php');
    exit;
}
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
  </style>
</head>
<body>

  <header style="border-bottom: 1px solid var(--border-gold); background: var(--surface-1);">
    <div class="header-container">
      
      <a href="../index.html" style="display: flex; align-items: center; gap: 10px;">
        <span class="star8" style="width: 26px; height: 26px;"></span>
        <span class="font-title" style="font-size: 1.5rem; font-weight: 700;">مكتبة بغداد</span>
      </a>

      <nav class="header-nav font-ui">
        <a href="../index.html" style="color: var(--text-muted);">الرئيسية</a>
        <a href="../discovery/library.php" style="color: var(--gold-soft);">المكتبة</a>
        <a href="../discovery/scholars.php" style="color: var(--text-muted);">العلماء</a>
        <a href="../discovery/inventions.php" style="color: var(--text-muted);">الاختراعات</a>
        <a href="../user/community.html" style="color: var(--text-muted);">المجتمع</a>
      </nav>

      <div class="header-actions">
        <div class="toggle-track" id="modeToggle" role="button">
          <div class="toggle-thumb" id="modeThumb">☾</div>
        </div>
        <a href="../user/profile.html" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.85rem;">حسابي</a>
      </div>

    </div>
  </header>

  <main style="max-width: 1180px; margin: 0 auto; padding: 40px 24px;">
    
    <div class="book-hero illum-frame">
      
      <div class="book-cover-lg" style="<?= !empty($book['cover_image']) ? "background-image: linear-gradient(to top, rgba(0,0,0,0.8), transparent), url('" . htmlspecialchars($book['cover_image']) . "');" : "" ?>">
        <span class="star8" style="width: 32px; height: 32px; margin-bottom: 12px;"></span>
        <h1 class="font-title"><?= htmlspecialchars($book['title']) ?></h1>
        <p class="font-ui" style="color: var(--gold-soft);"><?= htmlspecialchars($book['author_name'] ?? '') ?> | <?= htmlspecialchars($book['category'] ?? '') ?></p>
      </div>

      <div style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
          <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
            <div>
              <h2 style="font-size: 2rem; margin-bottom: 6px;"><?= htmlspecialchars($book['title']) ?></h2>
              <span class="font-ui" style="color: var(--gold-soft); font-size: 1.1rem;">
                تأليف: <?= htmlspecialchars($book['author_name'] ?? 'غير محدد') ?>
              </span>
            </div>
            <span class="font-ui" style="background: var(--surface-2); border: 1px solid var(--border-gold); padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; color: var(--gold-soft);">
              ★ <?= htmlspecialchars($book['rating'] ?? '4.8') ?>
            </span>
          </div>

          <div class="book-meta-grid">
            <div>
              <div class="meta-item-label">التصنيف</div>
              <div class="meta-item-value"><?= htmlspecialchars($book['category'] ?? 'عام') ?></div>
            </div>
            <div>
              <div class="meta-item-label">عدد الصفحات</div>
              <div class="meta-item-value"><?= htmlspecialchars($book['pages'] ?? $book['pages_count'] ?? 'غير محدد') ?> صفحة</div>
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
          <a href="interactive-reader.html?id=<?= $book['id'] ?>" class="btn btn-gold" style="padding: 12px 28px; font-size: 1rem;">
            📖 ابدأ القراءة الآن
          </a>
          <a href="audiobook-player.html?id=<?= $book['id'] ?>" class="btn btn-outline" style="padding: 12px 24px; font-size: 1rem;">
            🎧 استمع للكتاب الصوتي
          </a>
          <a href="ai-copilot.html?id=<?= $book['id'] ?>" class="btn btn-outline" style="padding: 12px 20px; border-color: var(--border-gold); color: var(--gold-soft);">
            ✨ المساعد الذكي
          </a>
        </div>

      </div>

    </div>

  </main>

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