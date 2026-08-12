<?php
session_start();

// إعدادات الاتصال بقاعدة البيانات
$host = 'localhost';
$db   = 'baghdad_library';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}

// المستخدم الحالي (افتراضي ID = 1 بناءً على بياناتك)
$current_user_id = $_SESSION['user_id'] ?? 1;

// التبويب النشط (افتراضي reading)
$status = $_GET['status'] ?? 'reading';

// استعلام جلب كتب المستخدم وحساب نسبة التقدم تلقائياً
$query = "
    SELECT 
        b.id AS book_id,
        b.title,
        b.cover_image,
        b.pages_count,
        s.name AS author_name,
        ub.current_page,
        ub.status,
        ROUND((ub.current_page / NULLIF(b.pages_count, 0)) * 100) AS progress_percentage
    FROM user_books ub
    JOIN books b ON ub.book_id = b.id
    LEFT JOIN scholars s ON b.author_id = s.id
    WHERE ub.user_id = :user_id AND ub.status = :status
";

$stmt = $pdo->prepare($query);
$stmt->execute([
    'user_id' => $current_user_id,
    'status'  => $status
]);
$books = $stmt->fetchAll();

// استعلام العدادات لأزرار التبويبات فوق
$count_stmt = $pdo->prepare("
    SELECT status, COUNT(*) as count 
    FROM user_books 
    WHERE user_id = :user_id 
    GROUP BY status
");
$count_stmt->execute(['user_id' => $current_user_id]);
$counts_raw = $count_stmt->fetchAll();

$counts = ['reading' => 0, 'completed' => 0, 'plan_to_read' => 0, 'wishlist' => 0];
foreach ($counts_raw as $row) {
    $counts[$row['status']] = $row['count'];
}

$page_title = "مكتبتي الخاصة — مكتبة بغداد";
require_once __DIR__ . '/../includes/header.php';
?>

<style>
  .tabs-bar { display: flex; gap: 12px; border-bottom: 1px solid var(--border-subtle); margin-top: 30px; margin-bottom: 36px; }
  .tab-btn { padding: 12px 20px; background: none; border: none; color: var(--text-muted); font-family: var(--font-ui); font-size: 0.95rem; cursor: pointer; position: relative; transition: color var(--transition-fast); text-decoration: none; }
  .tab-btn:hover, .tab-btn.active { color: var(--gold-soft); font-weight: 700; }
  .tab-btn.active::after { content: ""; position: absolute; bottom: -1px; right: 0; left: 0; height: 2px; background: var(--gold-primary); }
  
  .library-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; }
  .my-book-card { background: var(--surface-1); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 20px; display: flex; flex-direction: column; justify-content: space-between; transition: transform var(--transition-fast), border-color var(--transition-fast); }
  .my-book-card:hover { transform: translateY(-4px); border-color: var(--border-gold); }
  .book-thumb-small { height: 140px; background-size: cover; background-position: center; border-radius: var(--radius-sm); display: flex; align-items: flex-end; padding: 12px; margin-bottom: 16px; position: relative; }
  .book-thumb-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); border-radius: var(--radius-sm); }
  .progress-bar-sm { height: 4px; background: var(--surface-2); border-radius: 2px; overflow: hidden; margin-top: 8px; }
  .progress-fill-sm { height: 100%; background: var(--gold-primary); }
</style>

<!-- MAIN CONTENT -->
<main style="max-width: 1180px; margin: 0 auto; padding: 40px 24px">
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
      <h1 style="font-size: 2rem; margin-bottom: 6px">مكتبتي الخاصة ورفوفي</h1>
      <p class="font-ui" style="color: var(--text-muted); font-size: 0.95rem">
        مساحتك الشخصية لتتبع تقدم القراءة وإدارة كتبك المحفوظة.
      </p>
    </div>
  </div>

  <!-- Tabs Navigation -->
  <div class="tabs-bar font-ui">
    <a href="?status=reading" class="tab-btn <?= $status === 'reading' ? 'active' : '' ?>">
      أقرأ حالياً (<?= $counts['reading'] ?>)
    </a>
    <a href="?status=completed" class="tab-btn <?= $status === 'completed' ? 'active' : '' ?>">
      المكتملة (<?= $counts['completed'] ?>)
    </a>
    <a href="?status=plan_to_read" class="tab-btn <?= $status === 'plan_to_read' || $status === 'wishlist' ? 'active' : '' ?>">
      قائمة القراءة لاحقاً (<?= $counts['plan_to_read'] + $counts['wishlist'] ?>)
    </a>
  </div>

  <!-- Books Grid -->
  <div class="library-grid">
    <?php if (!empty($books)): ?>
      <?php foreach ($books as $book): ?>
        <div class="my-book-card illum-frame">
          <div>
            <div class="book-thumb-small" style="background-image: url('<?= htmlspecialchars($book['cover_image'] ?? '') ?>');">
              <div class="book-thumb-overlay"></div>
              <span class="font-title" style="color: #fff; font-size: 1.1rem; position: relative; z-index: 1;">
                <?= htmlspecialchars($book['title']) ?>
              </span>
            </div>
            <h2 style="font-size: 1.15rem; margin-bottom: 4px">
              <?= htmlspecialchars($book['title']) ?>
            </h2>
            <div class="font-ui" style="color: var(--gold-soft); font-size: 0.85rem; margin-bottom: 12px;">
              <?= htmlspecialchars($book['author_name'] ?? 'مؤلف غير معروف') ?>
            </div>
            <div class="font-ui" style="font-size: 0.8rem; color: var(--text-muted)">
              <span>تم إنجاز <?= (int)$book['progress_percentage'] ?>% (صفحة <?= (int)$book['current_page'] ?> من <?= (int)$book['pages_count'] ?>)</span>
              <div class="progress-bar-sm">
                <div class="progress-fill-sm" style="width: <?= min(100, (int)$book['progress_percentage']) ?>%"></div>
              </div>
            </div>
          </div>
          <div style="margin-top: 20px; display: flex; gap: 8px" class="font-ui">
            <a href="../reader-module/interactive-reader.php?book_id=<?= $book['book_id'] ?>" class="btn btn-gold" style="flex: 1; text-align: center; font-size: 0.85rem; padding: 8px;">
              متابعة القراءة
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="font-ui" style="color: var(--text-muted);">لا توجد كتب في هذه القائمة حالياً.</p>
    <?php endif; ?>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>