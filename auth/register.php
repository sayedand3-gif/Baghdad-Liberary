<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$error = '';
$success = '';

// معالجة إرسال النموذج (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname         = trim($_POST['fullname'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm-password'] ?? '';
    $terms            = isset($_POST['terms']);

    // 1. التحقق من الحقول الإجبارية
    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'يرجى ملء جميع الحقول المطلوبة.';
    } 
    // 2. التحقق من تطابق كلمتي المرور
    elseif ($password !== $confirm_password) {
        $error = 'كلمتا المرور غير متطابقتين.';
    } 
    // 3. التحقق من طول كلمة المرور
    elseif (strlen($password) < 6) {
        $error = 'كلمة المرور يجب أن تكون 6 أرقام/حروف على الأقل.';
    } 
    // 4. التحقق من الموافقة على الشروط
    elseif (!$terms) {
        $error = 'يجب الموافقة على الشروط والأحكام ومتابعة التسجيل.';
    } 
    else {
        // 5. التحقق مما إذا كان البريد الإلكتروني مسجلاً مسبقاً
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        
        if ($stmt->fetch()) {
            $error = 'هذا البريد الإلكتروني مُسجّل بالفعل، يمكنك تسجيل الدخول.';
        } else {
            // 6. تشفير كلمة المرور وتخزين البيانات
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertStmt = $pdo->prepare("
                INSERT INTO users (name, email, password_hash, created_at) 
                VALUES (:name, :email, :password_hash, NOW())
            ");

            $result = $insertStmt->execute([
                'name'          => $fullname,
                'email'         => $email,
                'password_hash' => $hashedPassword
            ]);

            if ($result) {
                // حفظ بيانات المستخدم في الجلسة وتسجيل دخوله تلقائياً
                $_SESSION['user_id']   = $pdo->lastInsertId();
                $_SESSION['user_name'] = $fullname;
                $_SESSION['user_email'] = $email;

                // التوجيه إلى الصفحة الرئيسية
                header("Location: ../index.php");
                exit;
            } else {
                $error = 'حدث خطأ أثناء إنشاء الحساب، يرجى المحاولة لاحقاً.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>إنشاء حساب جديد — مكتبة بغداد</title>
    <!-- استدعاء ملف التنسيقات الرئيسي من الفولدر الأب -->
    <link rel="stylesheet" href="../assets/css/main.css" />
    <style>
        .auth-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 32px 24px; position: relative; }
        .auth-card { background: var(--surface-1); border: 1px solid var(--border-gold); border-radius: var(--radius-md); padding: 40px 32px; width: 100%; max-width: 440px; box-shadow: var(--shadow-main); }
        .form-group { margin-bottom: 18px; text-align: right; }
        .form-group label { display: block; font-family: var(--font-ui); font-size: 0.85rem; color: var(--text-muted); margin-bottom: 6px; }
        .form-input { width: 100%; padding: 11px 16px; background: var(--bg-primary); border: 1px solid var(--border-gold); border-radius: var(--radius-sm); color: var(--text-main); font-family: var(--font-body); font-size: 0.95rem; outline: none; transition: border-color var(--transition-fast); }
        .form-input:focus { border-color: var(--gold-soft); }
        .checkbox-group { display: flex; align-items: center; gap: 10px; margin: 16px 0 22px; font-family: var(--font-ui); font-size: 0.8rem; color: var(--text-muted); }
        .checkbox-group input[type="checkbox"] { accent-color: var(--gold-primary); width: 16px; height: 16px; cursor: pointer; }
        .divider { display: flex; align-items: center; text-align: center; margin: 22px 0; color: var(--text-muted); font-family: var(--font-ui); font-size: 0.8rem; }
        .divider::before, .divider::after { content: ""; flex: 1; border-bottom: 1px solid var(--border-subtle); }
        .divider span { padding: 0 12px; }
        .btn-google { width: 100%; background: var(--surface-2); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 11px; font-family: var(--font-ui); font-weight: 500; font-size: 0.9rem; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; gap: 10px; cursor: pointer; transition: background var(--transition-fast); }
        .btn-google:hover { background: var(--surface-3); }
        .alert-error { background: rgba(234, 67, 53, 0.15); border: 1px solid #ea4335; color: #ff6b6b; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 0.85rem; margin-bottom: 18px; font-family: var(--font-ui); text-align: center; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card illum-frame">
            <!-- Logo Header -->
            <div style="text-align: center; margin-bottom: 26px">
                <a href="../index.php" style="display: inline-flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                    <span class="star8" style="width: 24px; height: 24px"></span>
                    <span class="font-title" style="font-size: 1.6rem; font-weight: 700">مكتبة بغداد</span>
                </a>
                <h2 style="font-size: 1.4rem; margin-top: 2px">إنشاء حساب جديد</h2>
                <p class="font-ui" style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">
                    انضم إلى مكتبة بغداد الرقمية
                </p>
            </div>

            <!-- عرض رسالة الخطأ عند وجودها -->
            <?php if (!empty($error)): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Register Form -->
            <form action="" method="POST">
                <div class="form-group">
                    <label for="fullname">الاسم الكامل</label>
                    <input type="text" id="fullname" name="fullname" class="form-input" placeholder="مثال: أحمد محمد" value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>" required />
                </div>

                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="example@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required />
                </div>

                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required />
                </div>

                <div class="form-group">
                    <label for="confirm-password">تأكيد كلمة المرور</label>
                    <input type="password" id="confirm-password" name="confirm-password" class="form-input" placeholder="••••••••" required />
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="terms" name="terms" <?= isset($_POST['terms']) ? 'checked' : '' ?> required />
                    <label for="terms" style="margin: 0; cursor: pointer">
                        أوافق على <a href="../system/about-faq.php" style="color: var(--gold-soft); text-decoration: underline">الشروط والأحكام</a> وسياسة الخصوصية
                    </label>
                </div>

                <button type="submit" class="btn btn-gold" style="width: 100%; padding: 12px">
                    إنشاء حساب
                </button>
            </form>

            <div class="divider">
                <span>أو</span>
            </div>

            <!-- Google Registration -->
            <button class="btn-google" type="button">
                <svg width="18" height="18" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                </svg>
                التسجيل بواسطة Google
            </button>

            <!-- Login Link -->
            <div class="font-ui" style="text-align: center; margin-top: 22px; font-size: 0.85rem; color: var(--text-muted);">
                لديك حساب بالفعل؟ <a href="login.php" style="color: var(--gold-soft); font-weight: 700">تسجيل الدخول</a>
            </div>
        </div>
    </div>
    <script src="../assets/js/main.js"></script>
</body>
</html>