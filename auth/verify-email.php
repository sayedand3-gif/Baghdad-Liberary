<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

$error = '';

// Check if user reached this page through reset password flow
$resetEmail = $_SESSION['reset_email'] ?? '';
$sessionOtp = $_SESSION['reset_otp'] ?? '';

// Handle OTP submission form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect OTP inputs
    $digit1 = trim($_POST['otp1'] ?? '');
    $digit2 = trim($_POST['otp2'] ?? '');
    $digit3 = trim($_POST['otp3'] ?? '');
    $digit4 = trim($_POST['otp4'] ?? '');

    $enteredOtp = $digit1 . $digit2 . $digit3 . $digit4;

    if (strlen($enteredOtp) < 4) {
        $error = 'يرجى إدخال كافة أرقام رمز التحقق.';
    } elseif (!empty($sessionOtp) && $enteredOtp == $sessionOtp) {
        // Fetch user info to authenticate session
        $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $resetEmail]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            // Clear temp OTP session keys
            unset($_SESSION['reset_email'], $_SESSION['reset_otp']);

            // Redirect to home page
            header('Location: ../index.php');
            exit;
        } else {
            $error = 'حدث خطأ، الحساب غير موجود.';
        }
    } else {
        $error = 'رمز التحقق غير صحيح، يرجى المحاولة مرة أخرى.';
    }
}

// Handle OTP Resend request
if (isset($_GET['action']) && $_GET['action'] === 'resend') {
    $_SESSION['reset_otp'] = rand(1000, 9999);
    $resendMessage = 'تم إعادة إرسال رمز التحقق جديد.';
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>تأكيد البريد الإلكتروني — مكتبة بغداد</title>
    <link rel="stylesheet" href="../assets/css/main.css" />
    <style>
        .auth-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; position: relative; }
        .auth-card { background: var(--surface-1); border: 1px solid var(--border-gold); border-radius: var(--radius-md); padding: 40px 32px; width: 100%; max-width: 420px; box-shadow: var(--shadow-main); text-align: center; }
        .otp-inputs { display: flex; gap: 12px; justify-content: center; margin: 28px 0; direction: ltr; }
        .otp-field { width: 52px; height: 56px; background: var(--bg-primary); border: 1px solid var(--border-gold); border-radius: var(--radius-sm); color: var(--gold-soft); font-family: var(--font-title); font-size: 1.6rem; font-weight: 700; text-align: center; outline: none; transition: border-color var(--transition-fast), box-shadow var(--transition-fast); }
        .otp-field:focus { border-color: var(--gold-soft); box-shadow: 0 0 10px rgba(201, 162, 39, 0.25); }
        .alert-error { background: rgba(234, 67, 53, 0.15); border: 1px solid #ea4335; color: #ff6b6b; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 0.85rem; margin-bottom: 16px; font-family: var(--font-ui); text-align: center; }
        .alert-info { background: rgba(52, 168, 83, 0.15); border: 1px solid #34a853; color: #2ecc71; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 0.85rem; margin-bottom: 16px; font-family: var(--font-ui); text-align: center; }
        .demo-otp-hint { background: var(--surface-2); border: 1px dashed var(--border-gold); color: var(--gold-soft); padding: 8px 12px; border-radius: var(--radius-sm); font-size: 0.85rem; margin-bottom: 20px; font-family: var(--font-ui); }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card illum-frame">
            <!-- Logo Header -->
            <div style="margin-bottom: 24px">
                <a href="../index.php" style="display: inline-flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                    <span class="star8" style="width: 24px; height: 24px"></span>
                    <span class="font-title" style="font-size: 1.6rem; font-weight: 700">مكتبة بغداد</span>
                </a>
                <h2 style="font-size: 1.4rem; margin-top: 4px">تأكيد البريد الإلكتروني</h2>
                <p class="font-ui" style="color: var(--text-muted); font-size: 0.85rem; margin-top: 6px; line-height: 1.6;">
                    قمنا بطلب إرسال رمز التحقق المؤلف من 4 أرقام إلى بريدك الإلكتروني.
                </p>
            </div>

            <!-- Display OTP hint for testing/demo mode -->
            <?php if (!empty($sessionOtp)): ?>
                <div class="demo-otp-hint">
                    رمز التحقق التجريبي هو: <strong><?= htmlspecialchars($sessionOtp) ?></strong>
                </div>
            <?php endif; ?>

            <!-- Error and Resend Alerts -->
            <?php if (!empty($error)): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!empty($resendMessage)): ?>
                <div class="alert-info"><?= htmlspecialchars($resendMessage) ?></div>
            <?php endif; ?>

            <!-- Verification Form -->
            <form action="" method="POST">
                <div class="otp-inputs">
                    <input type="text" name="otp1" maxlength="1" class="otp-field" autofocus required />
                    <input type="text" name="otp2" maxlength="1" class="otp-field" required />
                    <input type="text" name="otp3" maxlength="1" class="otp-field" required />
                    <input type="text" name="otp4" maxlength="1" class="otp-field" required />
                </div>

                <button type="submit" class="btn btn-gold" style="width: 100%; padding: 12px">
                    تأكيد الحساب
                </button>
            </form>

            <!-- Resend Code Link -->
            <div class="font-ui" style="margin-top: 24px; font-size: 0.85rem; color: var(--text-muted)">
                لم يصلك الرمز؟
                <a href="?action=resend" style="color: var(--gold-soft); font-weight: 700">إعادة الإرسال</a>
            </div>
        </div>
    </div>

    <!-- Script to automatically switch input focus to next digit -->
    <script src="../assets/js/main.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const otpFields = document.querySelectorAll('.otp-field');
        otpFields.forEach((field, index) => {
            field.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < otpFields.length - 1) {
                    otpFields[index + 1].focus();
                }
            });
            field.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    otpFields[index - 1].focus();
                }
            });
        });
    });
    </script>
</body>
</html>