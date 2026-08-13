<?php
// Start user session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include Database Connection
require_once __DIR__ . '/../config/db.php';

$error   = '';
$success = '';

// Handle password reset request form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $error = 'يرجى إدخال البريد الإلكتروني.';
    } else {
        // Check if email exists in database
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            $otpCode = rand(1000, 9999);

            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_otp']   = $otpCode;

            header('Location: verify-email.php');
            exit;
        } else {
            $error = 'البريد الإلكتروني غير مسجل لدينا.';
        }
    }
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>استعادة كلمة المرور — مكتبة بغداد</title>
    <link rel="stylesheet" href="../assets/css/main.css" />
    <style>
        .auth-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; position: relative; }
        .auth-card { background: var(--surface-1); border: 1px solid var(--border-gold); border-radius: var(--radius-md); padding: 40px 32px; width: 100%; max-width: 420px; box-shadow: var(--shadow-main); }
        .form-group { margin-bottom: 22px; text-align: right; }
        .form-group label { display: block; font-family: var(--font-ui); font-size: 0.85rem; color: var(--text-muted); margin-bottom: 8px; }
        .form-input { width: 100%; padding: 12px 16px; background: var(--bg-primary); border: 1px solid var(--border-gold); border-radius: var(--radius-sm); color: var(--text-main); font-family: var(--font-body); font-size: 0.95rem; outline: none; transition: border-color var(--transition-fast); }
        .form-input:focus { border-color: var(--gold-soft); }
        .alert-error { background: rgba(234, 67, 53, 0.15); border: 1px solid #ea4335; color: #ff6b6b; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 0.85rem; margin-bottom: 20px; font-family: var(--font-ui); text-align: center; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card illum-frame">
            <!-- Logo Header -->
            <div style="text-align: center; margin-bottom: 28px">
                <a href="../index.php" style="display: inline-flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                    <span class="star8" style="width: 24px; height: 24px"></span>
                    <span class="font-title" style="font-size: 1.6rem; font-weight: 700">مكتبة بغداد</span>
                </a>
                <h2 style="font-size: 1.4rem; margin-top: 4px">استعادة كلمة المرور</h2>
                <p class="font-ui" style="color: var(--text-muted); font-size: 0.85rem; margin-top: 6px; line-height: 1.6;">
                    أدخل بريدك الإلكتروني المسجل وسنرسل لك رمز التحقق لإعادة تعيين كلمة المرور.
                </p>
            </div>

            <!-- Error Message Alert -->
            <?php if (!empty($error)): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Reset Password Form -->
            <form action="" method="POST">
                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="example@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required />
                </div>
                <button type="submit" class="btn btn-gold" style="width: 100%; padding: 12px">
                    إرسال رمز التحقق
                </button>
            </form>

            <!-- Back to Login Link -->
            <div class="font-ui" style="text-align: center; margin-top: 24px; font-size: 0.85rem; color: var(--text-muted);">
                تذكرت كلمة المرور؟ <a href="login.php" style="color: var(--gold-soft); font-weight: 700">العودة لتسجيل الدخول</a>
            </div>
        </div>
    </div>
    <script src="../assets/js/main.js"></script>
</body>
</html>