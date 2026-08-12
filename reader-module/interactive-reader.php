<?php
// Start user session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include Database Connection
require_once __DIR__ . '/../config/db.php';

// Validate and extract Book ID & current user ID
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$userId = $_SESSION['user_id'] ?? 1; // Default fallback user ID

// ===================== FETCH LAST READ PAGE LOGIC =====================
// Fetch user progress record if page parameter is not explicitly passed in URL
if (!isset($_GET['page'])) {
    $progressStmt = $pdo->prepare("
        SELECT current_page 
        FROM user_books 
        WHERE user_id = :user_id AND book_id = :book_id 
        LIMIT 1
    ");
    $progressStmt->execute([
        'user_id' => $userId,
        'book_id' => $bookId
    ]);
    $userProgress = $progressStmt->fetch();

    // Set page to last saved reading position if available, else default to page 1
    $currentPage = (!empty($userProgress) && (int)$userProgress['current_page'] > 0) 
        ? (int)$userProgress['current_page'] 
        : 1;
} else {
    $currentPage = (int)$_GET['page'];
}

// ===================== AJAX BOOKMARK HANDLERS =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    // 1. Save new bookmark
    if ($_POST['action'] === 'save_bookmark') {
        $bookmarkTitle = !empty($_POST['title']) ? trim($_POST['title']) : "Bookmark - Page " . $currentPage;
        try {
            $stmt = $pdo->prepare("
                INSERT INTO user_bookmarks (user_id, book_id, page_number, title) 
                VALUES (:user_id, :book_id, :page_number, :title)
            ");
            $stmt->execute([
                'user_id'     => $userId,
                'book_id'     => $bookId,
                'page_number' => $currentPage,
                'title'       => $bookmarkTitle
            ]);
            echo json_encode(['status' => 'success', 'message' => 'Bookmark saved successfully']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save bookmark']);
        }
        exit;
    }

    // 2. Delete bookmark
    if ($_POST['action'] === 'delete_bookmark') {
        $bookmarkId = (int)$_POST['bookmark_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM user_bookmarks WHERE id = :id AND user_id = :user_id");
            $stmt->execute(['id' => $bookmarkId, 'user_id' => $userId]);
            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error']);
        }
        exit;
    }
}

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

// Fetch Chapters Dynamically
$chaptersStmt = $pdo->prepare("SELECT * FROM book_chapters WHERE book_id = :book_id ORDER BY sort_order ASC, page_number ASC");
$chaptersStmt->execute(['book_id' => $bookId]);
$chapters = $chaptersStmt->fetchAll();

// ===================== CALCULATE ACTIVE CHAPTER LOGIC =====================
// Determine active chapter based on the highest page_number <= $currentPage
$activeChapterId = null;
if (!empty($chapters)) {
    foreach ($chapters as $chapter) {
        if ($currentPage >= (int)$chapter['page_number']) {
            $activeChapterId = $chapter['id'];
        } else {
            break;
        }
    }
    if ($activeChapterId === null && isset($chapters[0])) {
        $activeChapterId = $chapters[0]['id'];
    }
}

// Fetch Bookmarks Dynamically for current user and book
$bookmarksStmt = $pdo->prepare("
    SELECT * FROM user_bookmarks 
    WHERE user_id = :user_id AND book_id = :book_id 
    ORDER BY page_number ASC
");
$bookmarksStmt->execute(['user_id' => $userId, 'book_id' => $bookId]);
$userBookmarks = $bookmarksStmt->fetchAll();

// Extract PDF path stored in database
$pdfPath = !empty($book['pdf_url']) ? $book['pdf_url'] : '';

// Update or insert reading progress for authenticated users
$checkProgress = $pdo->prepare("SELECT id FROM user_books WHERE user_id = :user_id AND book_id = :book_id LIMIT 1");
$checkProgress->execute(['user_id' => $userId, 'book_id' => $bookId]);

if ($checkProgress->fetch()) {
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
.toolbar-actions { display: flex; align-items: center; gap: 12px; }
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
.tool-btn:hover { border-color: var(--border-gold); color: var(--gold-soft); }

/* Main Reader Grid Container */
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

/* Sidebar Styling */
.toc-sidebar {
  background: var(--surface-1);
  border-left: 1px solid var(--border-subtle);
  padding: 20px;
  height: calc(100vh - 120px);
  position: sticky;
  top: 60px;
  overflow-y: auto;
}
.toc-item {
  display: block;
  padding: 10px 12px;
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
  font-weight: bold;
}

/* Reader View Container & PDF iFrame */
.reader-content { padding: 30px 40px; width: 100%; margin: 0 auto; }
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
    <!-- Updated Back Button Logic using JavaScript history back -->
    <button onclick="goBackToPreviousPage()" class="tool-btn">← عودة لتفاصيل الكتاب</button>
    <span style="font-weight: 700; color: var(--gold-soft); font-size: 0.95rem">
      <?= htmlspecialchars($book['title']) ?>
    </span>
  </div>
  
  <div class="toolbar-actions">
    <button class="tool-btn" id="saveBookmarkBtn" title="إضافة علامة مرجعية">🔖 حفظ الصفحة الحالية</button>
  </div>
</div>

<!-- ===================== READER MAIN CONTAINER ===================== -->
<div class="reader-container">
  <!-- Table of Contents & Bookmarks Sidebar -->
  <aside class="toc-sidebar font-ui">
    <!-- Sidebar Navigation Switcher -->
    <div style="display: flex; gap: 6px; border-bottom: 1px solid var(--border-subtle); margin-bottom: 16px; padding-bottom: 8px;">
      <button id="btnShowChapters" class="tool-btn" style="flex: 1; border: none; background: transparent; font-weight: bold; color: var(--gold-soft);">
        📚 الفصول
      </button>
      <button id="btnShowBookmarks" class="tool-btn" style="flex: 1; border: none; background: transparent; color: var(--text-muted);">
        🔖 علاماتي (<?= count($userBookmarks) ?>)
      </button>
    </div>

    <!-- 1. Dynamic Chapters List Section -->
    <div id="chaptersList">
      <nav>
        <?php if (!empty($chapters)): ?>
          <?php foreach ($chapters as $chapter): ?>
            <a href="?id=<?= $bookId ?>&page=<?= $chapter['page_number'] ?>" 
               class="toc-item <?= $activeChapterId == $chapter['id'] ? 'active' : '' ?>">
               <?= htmlspecialchars($chapter['title']) ?>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="color: var(--text-muted); font-size: 0.85rem;">لا توجد فصول مضافة بعد.</p>
        <?php endif; ?>
      </nav>
    </div>

    <!-- 2. Dynamic Bookmarks List Section -->
    <div id="bookmarksList" style="display: none;">
      <nav>
        <?php if (!empty($userBookmarks)): ?>
          <?php foreach ($userBookmarks as $bm): ?>
            <div class="toc-item <?= $currentPage == $bm['page_number'] ? 'active' : '' ?>" style="display: flex; align-items: center; justify-content: space-between;">
              <a href="?id=<?= $bookId ?>&page=<?= $bm['page_number'] ?>" style="color: inherit; text-decoration: none; flex: 1;">
                📌 <?= htmlspecialchars($bm['title']) ?> (صـ <?= $bm['page_number'] ?>)
              </a>
              <button onclick="deleteBookmark(<?= $bm['id'] ?>)" style="background: none; border: none; color: #e74c3c; cursor: pointer; font-size: 0.9rem;" title="حذف العلامة">✕</button>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="color: var(--text-muted); font-size: 0.85rem;">لا توجد علامات مرجعية محفوظة لهذا الكتاب.</p>
        <?php endif; ?>
      </nav>
    </div>
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

    <!-- Original PDF iFrame View -->
    <?php if (!empty($pdfPath)): ?>
      <iframe 
        src="<?= htmlspecialchars($pdfPath) ?>#page=<?= $currentPage ?>" 
        class="pdf-viewer-frame" 
        title="<?= htmlspecialchars($book['title']) ?>"
      ></iframe>
    <?php else: ?>
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

<!-- Dynamic JavaScript Logic -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  // Replace current history state so internal page flips don't stack up in browser history
  const currentUrl = new URL(window.location.href);
  if (!currentUrl.searchParams.has('page')) {
    currentUrl.searchParams.set('page', '<?= $currentPage ?>');
    window.history.replaceState({}, '', currentUrl);
  }

  // 1. Toggle between Chapters and Bookmarks tabs in sidebar
  const btnShowChapters = document.getElementById("btnShowChapters");
  const btnShowBookmarks = document.getElementById("btnShowBookmarks");
  const chaptersList = document.getElementById("chaptersList");
  const bookmarksList = document.getElementById("bookmarksList");

  if (btnShowChapters && btnShowBookmarks) {
    btnShowChapters.addEventListener("click", () => {
      chaptersList.style.display = "block";
      bookmarksList.style.display = "none";
      btnShowChapters.style.color = "var(--gold-soft)";
      btnShowChapters.style.fontWeight = "bold";
      btnShowBookmarks.style.color = "var(--text-muted)";
      btnShowBookmarks.style.fontWeight = "normal";
    });

    btnShowBookmarks.addEventListener("click", () => {
      chaptersList.style.display = "none";
      bookmarksList.style.display = "block";
      btnShowBookmarks.style.color = "var(--gold-soft)";
      btnShowBookmarks.style.fontWeight = "bold";
      btnShowChapters.style.color = "var(--text-muted)";
      btnShowChapters.style.fontWeight = "normal";
    });
  }

  // 2. Handle saving bookmark via AJAX
  const bookmarkBtn = document.getElementById("saveBookmarkBtn");
  if (bookmarkBtn) {
    bookmarkBtn.addEventListener("click", () => {
      const title = prompt("أدخل اسمًا للعلامة المرجعية (اختياري):", "علامة - صفحة <?= $currentPage ?>");
      if (title === null) return; // Action canceled by user

      const formData = new FormData();
      formData.append('action', 'save_bookmark');
      formData.append('title', title);

      fetch(window.location.href, {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          location.reload(); // Refresh page to display newly created bookmark
        } else {
          alert("حدث خطأ أثناء حفظ العلامة.");
        }
      })
      .catch(() => alert("حدث خطأ في الاتصال بالخادم."));
    });
  }
});

// 3. Handle Back Navigation to outside pages smoothly
function goBackToPreviousPage() {
  if (document.referrer && !document.referrer.includes("interactive-reader.php")) {
    window.location.href = document.referrer;
  } else {
    window.location.href = "my-library.php";
  }
}

// 4. Delete Bookmark Handler
function deleteBookmark(bookmarkId) {
  if (confirm("هل أنت تأكد من حذف هذه العلامة المرجعية؟")) {
    const formData = new FormData();
    formData.append('action', 'delete_bookmark');
    formData.append('bookmark_id', bookmarkId);

    fetch(window.location.href, {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        location.reload();
      } else {
        alert("تعذر حذف العلامة المرجعية.");
      }
    });
  }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>