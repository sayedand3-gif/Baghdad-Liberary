<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// 1. التأكد من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$userId  = $_SESSION['user_id'];
$error   = '';
$success = '';

// 2. جلب بيانات المستخدم الحالية من قاعدة البيانات
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: ../auth/login.php');
    exit;
}

// 3. معالجة إرسال النموذج (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = trim($_POST['name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $bio       = trim($_POST['bio'] ?? '');
    $interests = trim($_POST['interests'] ?? '');
    $avatarUrl = $user['avatar_url']; // المسار الحالي افتراضياً

    // التحقق من الحقول الأساسية
    if (empty($name) || empty($email)) {
        $error = 'يرجى إدخال الاسم والبريد الإلكتروني.';
    } else {
        // التحقق مما إذا كان البريد مُستخدم من شخص آخر
        $emailCheck = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1");
        $emailCheck->execute(['email' => $email, 'id' => $userId]);

        if ($emailCheck->fetch()) {
            $error = 'البريد الإلكتروني مُستخدم بالفعل حساب آخر.';
        } else {
            // معالجة رفع الصورة الشخصية (إن وجدت)
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath   = $_FILES['avatar']['tmp_name'];
                $fileName      = $_FILES['avatar']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    // إنشاء المجلد إذا لم يكن موجوداً
                    $uploadDir = __DIR__ . '/../assets/images/avatars/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $newFileName = 'avatar_' . $userId . '_' . time() . '.' . $fileExtension;
                    $destPath    = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $avatarUrl = '../assets/images/avatars/' . $newFileName;
                    } else {
                        $error = 'حدث خطأ أثناء رفع الصورة.';
                    }
                } else {
                    $error = 'نوع الصورة غير مدعوم (الملفات المسموحة: JPG, PNG, WEBP).';
                }
            }

            // تحديث قاعدة البيانات عند عدم وجود أخطاء
            if (empty($error)) {
                $updateStmt = $pdo->prepare("
                    UPDATE users 
                    SET name = :name, 
                        email = :email, 
                        bio = :bio, 
                        interests = :interests, 
                        avatar_url = :avatar_url 
                    WHERE id = :id
                ");

                $result = $updateStmt->execute([
                    'name'       => $name,
                    'email'      => $email,
                    'bio'        => $bio,
                    'interests'  => $interests,
                    'avatar_url' => $avatarUrl,
                    'id'         => $userId
                ]);

                if ($result) {
                    $_SESSION['user_name']  = $name;
                    $_SESSION['user_email'] = $email;
                    $success = 'تم تحديث البيانات بنجاح.';
                    
                    // إعادة تنشيط المتغيرات بالبيانات الجديدة
                    $user['name']       = $name;
                    $user['email']      = $email;
                    $user['bio']        = $bio;
                    $user['interests']  = $interests;
                    $user['avatar_url'] = $avatarUrl;
                } else {
                    $error = 'حدث خطأ أثناء حفظ التغيرات.';
                }
            }
        }
    }
}

$pageTitle = "تعديل الملف الشخصي — مكتبة بغداد";
require_once __DIR__ . '/../includes/header.php';
?>

<style>
.edit-card {
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
.form-input, .form-textarea {
    width: 100%;
    padding: 12px 16px;
    background: var(--bg-primary);
    border: 1px solid var(--border-gold);
    border-radius: var(--radius-sm);
    color: var(--text-main);
    font-family: var(--font-body);
    font-size: 0.95rem;
    outline: none;
    transition: border-color var(--transition-fast);
}
.form-input:focus, .form-textarea:focus {
    border-color: var(--gold-soft);
}
.form-textarea {
    resize: vertical;
    min-height: 100px;
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
.alert-success {
    background: rgba(52, 168, 83, 0.15);
    border: 1px solid #34a853;
    color: #2ecc71;
    padding: 12px 16px;
    border-radius: var(--radius-sm);
    font-size: 0.88rem;
    margin-bottom: 20px;
    text-align: center;
}
.avatar-preview {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--border-gold);
    margin-bottom: 12px;
}
</style>

<main style="max-width: 1180px; margin: 0 auto; padding: 20px 24px 60px">
    <div class="edit-card illum-frame">
        <h1 class="font-title" style="font-size: 1.6rem; margin-bottom: 8px; border-bottom: 1px solid var(--border-gold); padding-bottom: 12px;">
            تعديل الملف الشخصي
        </h1>

        <?php if (!empty($error)): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <!-- الصورة الشخصية -->
            <div class="form-group" style="text-align: center;">
                <?php if (!empty($user['avatar_url'])): ?>
                    <img src="<?= htmlspecialchars($user['avatar_url']) ?>" alt="Avatar" class="avatar-preview" />
                <?php endif; ?>
                <label for="avatar" style="text-align: center;">تغيير الصورة الشخصية</label>
                <input type="file" id="avatar" name="avatar" class="form-input" accept="image/*" style="padding: 8px;" />
            </div>

            <!-- الاسم -->
            <div class="form-group">
                <label for="name">الاسم الكامل</label>
                <input type="text" id="name" name="name" class="form-input" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required />
            </div>

            <!-- البريد الإلكتروني -->
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" class="form-input" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required />
            </div>

            <!-- الاهتمامات -->
            <div class="form-group">
                <label for="interests">الاهتمامات والتخصص</label>
                <input type="text" id="interests" name="interests" class="form-input" placeholder="مثال: تاريخ العلوم، الفلسفة، الفلك" value="<?= htmlspecialchars($user['interests'] ?? '') ?>" />
            </div>

            <!-- النبذة -->
            <div class="form-group">
                <label for="bio">النبذة الشخصية (Bio)</label>
                <textarea id="bio" name="bio" class="form-textarea" placeholder="اكتب نبذة مختصرة عن اهتماماتك العلمية والتراثية..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
            </div>

            <!-- أزرار الإجراءات -->
            <div style="display: flex; gap: 12px; margin-top: 28px;">
                <button type="submit" class="btn btn-gold" style="flex: 1; padding: 12px;">حفظ التغييرات</button>
                <a href="profile.php" class="btn btn-outline" style="padding: 12px 24px; text-align: center;">إلغاء</a>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>