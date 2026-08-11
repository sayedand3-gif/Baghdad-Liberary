<?php
// Start user session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include Database Connection
require_once __DIR__ . '/../config/db.php';

// Validate and extract Book ID & current page parameter from URL
$bookId      = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Fetch target book details along with author name from database
$stmt = $pdo->prepare("
    SELECT b.*, s.name AS author_name 
    FROM books b 
    LEFT JOIN scholars s ON b.author_id = s.id 
    WHERE b.id = :id 
    LIMIT 1
");
$stmt->execute(['id' => $bookId]);
$book = $stmt->fetch();

// Redirect to library if requested book does not exist
if (!$book) {
    header('Location: ../discovery/library.php');
    exit;
}

// Extract PDF path stored in database
$pdfPath = !empty($book['pdf_url']) ? $book['pdf_url'] : '';

// Update or insert reading progress for authenticated users
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // Check existing reading progress record in user_books
    $checkProgress = $pdo->prepare("SELECT id FROM user_books WHERE user_id = :user_id AND book_id = :book_id LIMIT 1");
    $checkProgress->execute(['user_id' => $userId, 'book_id' => $bookId]);
    
    if ($checkProgress->fetch()) {
        // Update current reading page
        $updateStmt = $pdo->prepare("
            UPDATE user_books 
            SET current_page = :current_page, updated_at = NOW() 
            WHERE user_id = :user_id AND book_id = :book_id
        ");
        $updateStmt->execute([
            'current_page' => $currentPage,
            'user_id'      => $userId,
            'book_id'      => $bookId
        ]);
    } else {
        // Insert new reading progress record
        $insertStmt = $pdo->prepare("
            INSERT INTO user_books (user_id, book_id, status, current_page, updated_at) 
            VALUES (:user_id, :book_id, 'reading', :current_page, NOW())
        ");
        $insertStmt->execute([
            'user_id'      => $userId,
            'book_id'      => $bookId,
            'current_page' => $currentPage
        ]);
    }
}

// Calculate total reading progress percentage
$totalPages = !empty($book['pages_count']) ? (int)$book['pages_count'] : 100;
$progressPercentage = min(100, round(($currentPage / $totalPages) * 100));

// Set dynamic page title
$pageTitle = "القارئ التفاعلي — " . htmlspecialchars($book['title']);

// Include shared header component
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Custom Reader Layout Styles -->
<style>
/* Reader Top Navigation Bar */
.reader-toolbar { 
    background: var(--surface-1); 
    border-bottom: 1px solid var(--border-gold); 
    padding: 12px 24px; 
    display: flex; 
    align-items: center; 
    justify-content: space-between; 
    position: sticky; 
    top: 0; 
    z-index: 100; 
}
.toolbar-actions { 
    display: flex; 
    align-items: center; 
    gap: 12px; 
}
.tool-btn { 
    background: var(--surface-2); 
    border: 1px solid var(--border-subtle); 
    color: var(--text-main); 
    padding: 6px 12px; 
    border-radius: var(--radius-sm); 
    cursor: pointer; 
    font-family: var(--font-ui); 
    font-size: 0.85rem; 
    transition: border-color var(--transition-fast), color var(--transition-fast); 
    text-decoration: none;
}
.tool-btn:hover { 
    border-color: var(--border-gold); 
    color: var(--gold-soft); 
}

/* Reader Main Layout Container */
.reader-container { 
    display: grid; 
    grid-template-columns: 280px 1fr; 
    min-height: calc(100vh - 120px); 
    max-width: 1400px; 
    margin: 0 auto; 
    width: 100%; 
}
@media (max-width: 850px) { 
    .reader-container { grid-template-columns: 1fr; } 
    .toc-sidebar { display: none; } 
}

/* Table of Contents Sidebar */
.toc-sidebar { 
    background: var(--surface-1); 
    border-left: 1px solid var(--border-subtle); 
    padding: 24px; 
    height: calc(100vh - 120px); 
    position: sticky; 
    top: 60px; 
    overflow-y: auto; 
}
.toc-item { 
    display: block; 
    padding: 10px 14px; 
    border-radius: var(--radius-sm); 
    color: var(--text-muted); 
    font-family: var(--font-ui); 
    font-size: 0.9rem; 
    margin-bottom: 6px; 
    transition: all var(--transition-fast); 
    text-decoration: none; 
}
.toc-item:hover, .toc-item.active { 
    background: var(--surface-2); 
    color: var(--gold-soft); 
    border-right: 3px solid var(--gold-primary); 
}

/* Content Frame Container */
.reader-content { 
    padding: 30px 40px; 
    width: 100%; 
    margin: 0 auto; 
}
.pdf-viewer-frame {
    width: 100%;
    height: 780px;
    border: 1px solid var(--border-gold);
    border-radius: var(--radius-sm);
    background: #ffffff;
    box-shadow: var(--shadow-main);
}
</style>

<!-- ===================== READER TOOLBAR ===================== -->
<div class="reader-toolbar font-ui">
    <div style="display: flex; align-items: center; gap: 16px">
        <a href="book-details.php?id=<?= $book['id'] ?>" class="tool-btn">← عودة لتفاصيل الكتاب</a>
        <span style="font-weight: 700; color: var(--gold-soft); font-size: 0.95rem">
            <?= htmlspecialchars($book['title']) ?>
        </span>
    </div>

    <!-- Actions Toolbar -->
    <div class="toolbar-actions">
        <button class="tool-btn" id="saveBookmarkBtn" title="إضافة علامة مرجعية">🔖 حفظ الصفحة الحالية</button>
    </div>
</div>

<!-- ===================== READER MAIN CONTAINER ===================== -->
<div class="reader-container">
    <!-- Table of Contents Sidebar -->
    <aside class="toc-sidebar">
        <h3 class="font-title" style="font-size: 1.1rem; margin-bottom: 18px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
            فصول الكتاب
        </h3>
        <nav>
            <a href="?id=<?= $bookId ?>&page=1" class="toc-item <?= $currentPage == 1 ? 'active' : '' ?>">الصفحة الرئيسية / الغلاف</a>
            <a href="?id=<?= $bookId ?>&page=10" class="toc-item <?= $currentPage == 10 ? 'active' : '' ?>">المقدمة</a>
            <a href="?id=<?= $bookId ?>&page=42" class="toc-item <?= $currentPage == 42 ? 'active' : '' ?>">الباب الأول</a>
            <a href="?id=<?= $bookId ?>&page=120" class="toc-item <?= $currentPage == 120 ? 'active' : '' ?>">الباب الثاني</a>
            <a href="?id=<?= $bookId ?>&page=250" class="toc-item <?= $currentPage == 250 ? 'active' : '' ?>">الباب الثالث</a>
            <a href="?id=<?= $bookId ?>&page=400" class="toc-item <?= $currentPage == 400 ? 'active' : '' ?>">الباب الرابع</a>
        </nav>
    </aside>

    <!-- Main Content Reader Display -->
    <main class="reader-content font-body">
        <div style="text-align: center; margin-bottom: 24px; border-bottom: 1px solid var(--border-gold); padding-bottom: 16px;">
            <h1 class="font-title" style="font-size: 1.8rem; color: var(--gold-soft)">
                <?= htmlspecialchars($book['title']) ?>
            </h1>
            <p class="font-ui" style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;">
                المؤلف: <?= htmlspecialchars($book['author_name'] ?? 'غير محدد') ?> • الصفحة <?= $currentPage ?> من <?= $totalPages ?>
            </p>
        </div>

        <!-- Render PDF File from Database Path -->
        <?php if (!empty($pdfPath)): ?>
            <iframe 
                src="<?= htmlspecialchars($pdfPath) ?>#page=<?= $currentPage ?>" 
                class="pdf-viewer-frame"
                title="<?= htmlspecialchars($book['title']) ?>"
            ></iframe>
        <?php else: ?>
            <!-- Fallback text when PDF is missing -->
            <div style="text-align: center; padding: 60px 20px; background: var(--surface-1); border: 1px solid var(--border-gold); border-radius: var(--radius-sm);" class="illum-frame">
                <span class="star8" style="width: 28px; height: 28px; margin-bottom: 12px"></span>
                <h2 class="font-title" style="color: var(--gold-soft); font-size: 1.5rem; margin-bottom: 12px;">نص الوصف والملخص</h2>
                <p class="font-ui" style="color: var(--text-muted); line-height: 1.8; max-width: 700px; margin: 0 auto 20px;">
                    <?= nl2br(htmlspecialchars($book['description'])) ?>
                </p>
                <div style="font-size: 0.85rem; color: var(--gold-soft);" class="font-ui">
                    يمكنك رفع ملف PDF لهذا الكتاب في قاعدة البيانات بعمود (pdf_url) لعرضه مباشرة هنا.
                </div>
            </div>
        <?php endif; ?>

        <!-- Pagination Controls -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px; border-top: 1px solid var(--border-subtle); padding-top: 20px;" class="font-ui">
            <?php if ($currentPage > 1): ?>
                <a href="?id=<?= $bookId ?>&page=<?= max(1, $currentPage - 1) ?>" class="btn btn-outline">← الصفحة السابقة</a>
            <?php else: ?>
                <span></span>
            <?php endif; ?>

            <span style="color: var(--text-muted); font-size: 0.85rem">
                تم إنجاز <?= $progressPercentage ?>% من الكتاب
            </span>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?id=<?= $bookId ?>&page=<?= min($totalPages, $currentPage + 1) ?>" class="btn btn-gold">الصفحة التالية →</a>
            <?php else: ?>
                <span></span>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- JavaScript Logic -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Handle bookmark save trigger
    const bookmarkBtn = document.getElementById("saveBookmarkBtn");
    if (bookmarkBtn) {
        bookmarkBtn.addEventListener("click", () => {
            alert("تم حفظ التقدم في حسابك (صفحة <?= $currentPage ?>) بنجاح!");
        });
    }
});
</script>

<?php
// Include shared layout footer component
require_once __DIR__ . '/../includes/footer.php';
?>