<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Ensure user is authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$error   = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $content  = trim($_POST['content'] ?? '');
    $userId   = $_SESSION['user_id'];

    if (empty($title) || empty($content)) {
        $error = 'يرجى كتابة عنوان الموضوع والمحتوى.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO topics (user_id, title, content, category, created_at) 
            VALUES (:user_id, :title, :content, :category, NOW())
        ");
        
        $result = $stmt->execute([
            'user_id'  => $userId,
            'title'    => $title,
            'content'  => $content,
            'category' => $category
        ]);

        if ($result) {
            header('Location: community.php');
            exit;
        } else {
            $error = 'حدث خطأ أثناء إضافة الموضوع، يرجى المحاولة لاحقاً.';
        }
    }
}

// Set dynamic page title
$pageTitle = "إضافة موضوع جديد — مكتبة بغداد";

// Include shared navigation header
require_once __DIR__ . '/../includes/header.php';
?>

<style>
.form-card {
    background: var(--surface-1);
    border: 1px solid var(--border-gold);
    border-radius: var(--radius-md);
    padding: 36px;
    max-width: 680px;
    margin: 40px auto;
    box-shadow: var(--shadow-main);
}
.form-group {
    margin-bottom: 20px;
    text-align: right;
}
.form-group label {
    display: block;
    font-family: var(--font-ui);
    font-size: 0.88rem;
    color: var(--text-muted);
    margin-bottom: 8px;
}
.form-input, .form-textarea, .form-select {
    width: 100%;
    padding: 12px 16px;
    background: var(--bg-primary);
    border: 1px solid var(--border-gold);
    border-radius: var(--radius-sm);
    color: var(--text-main);
    font-family: var(--font-body);
    font-size: 0.95rem;
    outline: none;
}
.form-textarea {
    resize: vertical;
    min-height: 150px;
}
.alert-error {
    background: rgba(234, 67, 53, 0.15);
    border: 1px solid #ea4335;
    color: #ff6b6b;
    padding: 12px 16px;
    border-radius: var(--radius-sm);
    font-size: 0.88rem;
    margin-bottom: 20px;
    text-align: center;
}
</style>

<!-- ===================== MAIN FORM CONTENT ===================== -->
<main style="max-width: 1180px; margin: 0 auto; padding: 20px 24px 60px">
    <div class="form-card illum-frame">
        <h1 class="font-title" style="font-size: 1.6rem; margin-bottom: 12px; border-bottom: 1px solid var(--border-gold); padding-bottom: 12px;">
            إضافة موضوع ونقاش جديد
        </h1>

        <?php if (!empty($error)): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <!-- Topic Title -->
            <div class="form-group">
                <label for="title">عنوان الموضوع</label>
                <input type="text" id="title" name="title" class="form-input" placeholder="اكتب عنواناً يعبر عن الفكرة..." required />
            </div>

            <!-- Topic Category -->
            <div class="form-group">
                <label for="category">التصنيف</label>
                <select id="category" name="category" class="form-select">
                    <option value="فلسفة وتاريخ">فلسفة وتاريخ</option>
                    <option value="مقتبسات">مقتبسات</option>
                    <option value="تراث علمي">تراث علمي</option>
                    <option value="عام">عام</option>
                </select>
            </div>

            <!-- Topic Content -->
            <div class="form-group">
                <label for="content">تفاصيل الموضوع والنقاش</label>
                <textarea id="content" name="content" class="form-textarea" placeholder="شارك أفكارك بالتفصيل..." required></textarea>
            </div>

            <!-- Actions -->
            <div style="display: flex; gap: 12px; margin-top: 28px;">
                <button type="submit" class="btn btn-gold" style="flex: 1; padding: 12px;">نشر الموضوع</button>
                <a href="community.php" class="btn btn-outline" style="padding: 12px 24px; text-align: center; text-decoration: none;">إلغاء</a>
            </div>
        </form>
    </div>
</main>

<?php
// Include shared layout footer
require_once __DIR__ . '/../includes/footer.php';
?>