<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// التأكد من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// 1. جلب بيانات المستخدم من قاعدة البيانات
$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
$userStmt->execute(['id' => $userId]);
$user = $userStmt->fetch();

if (!$user) {
    header('Location: ../auth/login.php');
    exit;
}

// 2. حساب عدد الكتب المكتملة من جدول user_books
$completedStmt = $pdo->prepare("SELECT COUNT(*) FROM user_books WHERE user_id = :user_id AND status = 'completed'");
$completedStmt->execute(['user_id' => $userId]);
$completedBooksCount = $completedStmt->fetchColumn();

// 3. حساب إجمالي الصفحات المقروءة للتقدير
$pagesStmt = $pdo->prepare("SELECT SUM(current_page) FROM user_books WHERE user_id = :user_id");
$pagesStmt->execute(['user_id' => $userId]);
$totalPagesRead = $pagesStmt->fetchColumn() ?: 0;

// 4. جلب الأنشطة الأخيرة للمستخدم (الكتب التي يتفاعل معها)
$activitiesStmt = $pdo->prepare("
    SELECT ub.*, b.title 
    FROM user_books ub 
    JOIN books b ON ub.book_id = b.id 
    WHERE ub.user_id = :user_id 
    ORDER BY ub.updated_at DESC 
    LIMIT 5
");
$activitiesStmt->execute(['user_id' => $userId]);
$recentActivities = $activitiesStmt->fetchAll();

// تحضير حرف الحرف الأول للافتار في حالة عدم وجود صورة
$firstChar = mb_substr($user['name'] ?? 'م', 0, 1, 'UTF-8');
$createdYear = !empty($user['created_at']) ? date('Y', strtotime($user['created_at'])) : date('Y');

// عنوان الصفحة لاستدعائه في الهيدر
$pageTitle = "الملف الشخصي — " . htmlspecialchars($user['name']);

// استدعاء الهيدر الموحد
require_once __DIR__ . '/../includes/header.php';
?>

<style>
.profile-card { background: var(--surface-1); border: 1px solid var(--border-gold); border-radius: var(--radius-md); padding: 36px; display: flex; align-items: center; gap: 32px; margin-top: 30px; margin-bottom: 36px; }
@media (max-width: 768px) { .profile-card { flex-direction: column; text-align: center; } }
.avatar-lg { width: 110px; height: 110px; min-width: 110px; border-radius: 50%; background: var(--surface-2); border: 3px solid var(--border-gold); display: flex; align-items: center; justify-content: center; font-family: var(--font-title); font-size: 2.8rem; color: var(--gold-soft); box-shadow: 0 0 20px rgba(212, 175, 55, 0.2); overflow: hidden; }
.avatar-lg img { width: 100%; height: 100%; object-fit: cover; }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
.stat-card { background: var(--surface-1); border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 20px; text-align: center; }
.stat-number { font-family: var(--font-title); font-size: 2.2rem; color: var(--gold-primary); margin-bottom: 4px; }
.stat-label { font-family: var(--font-ui); font-size: 0.85rem; color: var(--text-muted); }
.profile-grid { display: grid; grid-template-columns: 1fr 320px; gap: 32px; }
@media (max-width: 850px) { .profile-grid { grid-template-columns: 1fr; } }
.activity-item { padding: 16px; border-bottom: 1px solid var(--border-subtle); display: flex; gap: 16px; align-items: flex-start; }
.activity-item:last-child { border-bottom: none; }
</style>

<!-- ===================== MAIN CONTENT ===================== -->
<main style="max-width: 1180px; margin: 0 auto; padding: 20px 24px 60px">
    <!-- User Profile Hero Header -->
    <div class="profile-card illum-frame">
        <div class="avatar-lg">
            <?php if (!empty($user['avatar_url'])): ?>
                <img src="<?= htmlspecialchars($user['avatar_url']) ?>" alt="<?= htmlspecialchars($user['name']) ?>" />
            <?php else: ?>
                <?= htmlspecialchars($firstChar) ?>
            <?php endif; ?>
        </div>
        <div style="flex: 1">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h1 style="font-size: 1.8rem; margin-bottom: 6px"><?= htmlspecialchars($user['name']) ?></h1>
                    <div class="font-ui" style="color: var(--gold-soft); font-size: 0.95rem; margin-bottom: 10px;">
                        <?= htmlspecialchars($user['interests'] ?? 'مُطالع وتراثي') ?> • عضو منذ <?= $createdYear ?>
                    </div>
                    <p class="font-ui" style="color: var(--text-muted); font-size: 0.85rem; max-width: 600px; line-height: 1.6;">
                        <?= htmlspecialchars($user['bio'] ?? 'لا توجد نبذة شخصية مضافة حتى الآن.') ?>
                    </p>
                </div>
                <a href="edit-profile.php" class="btn btn-outline" style="font-size: 0.85rem; padding: 8px 18px">
    تعديل الملف الشخصي
</a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Section -->
    <div class="stats-grid">
        <div class="stat-card illum-frame">
            <div class="stat-number"><?= $completedBooksCount ?></div>
            <div class="stat-label">كتب تم إكمالها</div>
        </div>
        <div class="stat-card illum-frame">
            <div class="stat-number"><?= $totalPagesRead ?></div>
            <div class="stat-label">صفحة تم قراءتها</div>
        </div>
        <div class="stat-card illum-frame">
            <div class="stat-number"><?= count($recentActivities) ?></div>
            <div class="stat-label">نشاط تفاعلي</div>
        </div>
        <div class="stat-card illum-frame">
            <div class="stat-number"><?= $completedBooksCount * 100 + 50 ?></div>
            <div class="stat-label">نقطة حكمة</div>
        </div>
    </div>

    <!-- Activity & Details Grid -->
    <div class="profile-grid">
        <!-- Main Feed: Recent Activities -->
        <div style="background: var(--surface-1); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 24px;">
            <h3 class="font-title" style="font-size: 1.3rem; margin-bottom: 20px; border-bottom: 1px solid var(--border-gold); padding-bottom: 10px;">
                النشاطات الأخيرة
            </h3>
            <div class="font-ui">
                <?php if (!empty($recentActivities)): ?>
                    <?php foreach ($recentActivities as $activity): ?>
                        <div class="activity-item">
                            <span style="font-size: 1.2rem">
                                <?= $activity['status'] === 'completed' ? '✅' : '📖' ?>
                            </span>
                            <div>
                                <div style="font-size: 0.9rem; margin-bottom: 4px">
                                    <?= $activity['status'] === 'completed' ? 'أنهى قراءة' : 'وصل إلى الصفحة ' . $activity['current_page'] . ' في' ?> 
                                    <strong><?= htmlspecialchars($activity['title']) ?></strong>
                                </div>
                                <div style="font-size: 0.78rem; color: var(--text-muted)">
                                    <?= date('Y-m-d H:i', strtotime($activity['updated_at'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">لا توجد أنشطة قراءة حتى الآن.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar Column: Quick Links -->
        <aside style="display: flex; flex-direction: column; gap: 24px">
            <div style="background: var(--surface-1); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 24px;">
                <h3 class="font-title" style="font-size: 1.1rem; margin-bottom: 16px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                    وصول سريع
                </h3>
                <div style="display: flex; flex-direction: column; gap: 10px" class="font-ui">
                    <a href="my-library.php" class="btn btn-outline" style="text-align: right; justify-content: flex-start">📚 رفوفي ومكتبتي الخاصة</a>
                    <a href="../auth/logout.php" class="btn btn-outline" style="text-align: right; justify-content: flex-start; color: #e74c3c; border-color: rgba(231, 76, 60, 0.3);">🚪 تسجيل الخروج</a>
                </div>
            </div>
        </aside>
    </div>
</main>

<?php
// استدعاء الفُوتر الموحد
require_once __DIR__ . '/../includes/footer.php';
?>