<?php 
// Load database connection
require_once __DIR__ . '/../config/db.php';

// Handle search query and filtering
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

// Build query dynamically
$query = "SELECT * FROM books WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (title LIKE :search OR author_name LIKE :search)";
    $params['search'] = '%' . $search . '%';
}

if (!empty($category)) {
    $query .= " AND category = :category";
    $params['category'] = $category;
}

$query .= " ORDER BY id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll();

// Fetch unique categories for filter tabs
$categoriesStmt = $pdo->query("SELECT DISTINCT category FROM books WHERE category IS NOT NULL AND category != ''");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);

// Page Title Configuration
$pageTitle = "المكتبة الرقمية — مكتبة بغداد";

// Include shared header component
require_once __DIR__ . '/../includes/header.php'; 
?>

<!-- Library Custom Styles -->
<style>
.library-grid { 
    display: grid; 
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); 
    gap: 24px; 
    margin-top: 30px; 
}
.book-card { 
    background: var(--surface-1); 
    border: 1px solid var(--border-subtle); 
    border-radius: var(--radius-sm); 
    overflow: hidden; 
    transition: transform var(--transition-fast), border-color var(--transition-fast); 
    display: block; 
    text-decoration: none; 
    color: inherit; 
}
.book-card:hover { 
    transform: translateY(-4px); 
    border-color: var(--border-gold); 
}
.search-box {
    display: flex;
    gap: 12px;
    max-width: 600px;
    margin: 0 auto 30px;
}
.search-input {
    flex: 1;
    padding: 12px 18px;
    background: var(--bg-primary);
    border: 1px solid var(--border-gold);
    border-radius: var(--radius-sm);
    color: var(--text-main);
    font-family: var(--font-body);
    font-size: 0.95rem;
    outline: none;
}
.filter-tabs {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 30px;
}
.tab-btn {
    padding: 8px 16px;
    background: var(--surface-1);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-sm);
    color: var(--text-muted);
    text-decoration: none;
    font-family: var(--font-ui);
    font-size: 0.85rem;
    transition: all var(--transition-fast);
}
.tab-btn.active, .tab-btn:hover {
    border-color: var(--gold-soft);
    color: var(--gold-soft);
    background: var(--surface-2);
}
</style>

<!-- ===================== MAIN LIBRARY CONTENT ===================== -->
<main style="max-width: 1180px; margin: 0 auto; padding: 50px 24px;">
    <!-- Section Title -->
    <div style="text-align: center; margin-bottom: 32px;">
        <h1 style="font-size: 2.2rem; margin-bottom: 10px;">المكتبة الرقمية الشاملة</h1>
        <p class="font-ui" style="color: var(--text-muted); font-size: 0.95rem;">
            تصفح أحدث الكتب المترجمة والملخصات الفكرية والتراثية.
        </p>
    </div>

    <!-- Search Form -->
    <form action="" method="GET" class="search-box">
        <input type="text" name="search" class="search-input" placeholder="ابحث عن اسم كتاب أو مؤلف..." value="<?= htmlspecialchars($search) ?>" />
        <?php if (!empty($category)): ?>
            <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
        <?php endif; ?>
        <button type="submit" class="btn btn-gold" style="padding: 12px 24px;">بحث</button>
    </form>

    <!-- Category Filter Tabs -->
    <div class="filter-tabs">
        <a href="library.php<?= !empty($search) ? '?search=' . urlencode($search) : '' ?>" class="tab-btn <?= empty($category) ? 'active' : '' ?>">الكل</a>
        <?php foreach ($categories as $cat): ?>
            <a href="library.php?category=<?= urlencode($cat) ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="tab-btn <?= $category === $cat ? 'active' : '' ?>">
                <?= htmlspecialchars($cat) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Books Display Grid -->
    <div class="library-grid">
        <?php if (!empty($books)): ?>
            <?php foreach ($books as $book): 
                $coverImage = !empty($book['cover_image']) ? ltrim($book['cover_image'], './') : '';
            ?>
                <a href="../reader-module/book-details.php?id=<?= $book['id'] ?>" class="book-card illum-frame">
                    <div style="height: 240px; background: linear-gradient(135deg, #1e2740, #2c625d); display: flex; align-items: flex-end; padding: 16px; background-size: cover; background-position: center; <?= !empty($coverImage) ? "background-image: linear-gradient(to top, rgba(0,0,0,0.8), transparent), url('../" . htmlspecialchars($coverImage) . "');" : "" ?>">
                        <span class="font-title" style="font-size: 1.2rem; color: #fff; text-shadow: 0 2px 4px rgba(0,0,0,0.8);"><?= htmlspecialchars($book['title']) ?></span>
                    </div>
                    <div style="padding: 14px">
                        <div style="font-weight: 700; font-size: 0.95rem"><?= htmlspecialchars($book['author_name'] ?? '') ?></div>
                        <div class="font-ui" style="color: var(--gold-soft); font-size: 0.85rem; margin-top: 6px;">
                            ★ <?= htmlspecialchars($book['rating'] ?? '4.9') ?> (<?= htmlspecialchars($book['category'] ?? '') ?>)
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-muted);" class="font-ui">
                لا توجد كتب مطابقة لخيارات البحث الحالية.
            </div>
        <?php endif; ?>
    </div>
</main>

<?php 
// Include shared footer component
require_once __DIR__ . '/../includes/footer.php'; 
?>