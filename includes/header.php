<?php 
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
} 

// 1. Determine base URL dynamically 
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']); 
$pathParts  = explode('/', trim($scriptPath, '/')); 
$baseFolder = $pathParts[0] ?? ''; 
$baseUrl    = '/' . $baseFolder . '/'; 

// 2. Identify active page path to highlight correct navigation link 
$currentScript = $_SERVER['SCRIPT_NAME']; 
?> 
<!doctype html> 
<html lang="ar" dir="rtl"> 
<head> 
    <meta charset="UTF-8" /> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0" /> 
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'مكتبة بغداد — بيت الحكمة الرقمي' ?></title> 
    
    <!-- Direct CSS Path --> 
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/main.css" /> 
    
    <style> 
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            user-select: none; 
        } 
        .header-container { 
            max-width: 1180px; 
            margin: 0 auto; 
            padding: 18px 24px; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            gap: 24px; 
        } 
        .header-nav { 
            display: flex; 
            gap: 28px; 
            font-size: 0.95rem; 
        } 
        .header-nav a { 
            text-decoration: none; 
            transition: color var(--transition-fast, 0.2s); 
        } 
        .header-nav a.active { 
            color: var(--gold-soft) !important; 
            font-weight: 700; 
        } 
        .header-nav a:not(.active) { 
            color: var(--text-muted); 
        } 
        .header-nav a:hover { 
            color: var(--gold-soft); 
        } 
        .header-actions { 
            display: flex; 
            align-items: center; 
            gap: 16px; 
        } 
        .toggle-track { 
            width: 50px; 
            height: 26px; 
            border-radius: 20px; 
            background: var(--surface-2); 
            border: 1px solid var(--border-gold); 
            position: relative; 
            cursor: pointer; 
        } 
        .toggle-thumb { 
            position: absolute; 
            top: 2px; 
            right: 2px; 
            width: 20px; 
            height: 20px; 
            border-radius: 50%; 
            background: var(--gold-primary); 
            transition: transform 0.3s ease; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 11px; 
            color: var(--text-dark); 
        } 
        html.light .toggle-thumb { 
            transform: translateX(-24px); 
        } 
    </style> 
</head> 
<body> 
    <!-- Header Navigation Component --> 
    <header style="border-bottom: 1px solid var(--border-gold); background: var(--surface-1);"> 
        <div class="header-container"> 
            <!-- Brand Logo --> 
            <a href="<?= $baseUrl ?>index.php" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;"> 
                <span class="star8" style="width: 26px; height: 26px"></span> 
                <span class="font-title" style="font-size: 1.5rem; font-weight: 700">مكتبة بغداد</span> 
            </a> 

            <!-- Main Navigation Links --> 
            <nav class="header-nav font-ui"> 
                <a href="<?= $baseUrl ?>index.php" class="<?= (strpos($currentScript, 'index.php') !== false) ? 'active' : '' ?>">الرئيسية</a> 
                <a href="<?= $baseUrl ?>discovery/library.php" class="<?= (strpos($currentScript, 'library.php') !== false) ? 'active' : '' ?>">المكتبة</a> 
                <a href="<?= $baseUrl ?>discovery/scholars.php" class="<?= (strpos($currentScript, 'scholars.php') !== false) ? 'active' : '' ?>">العلماء</a> 
                <a href="<?= $baseUrl ?>discovery/inventions.php" class="<?= (strpos($currentScript, 'inventions.php') !== false) ? 'active' : '' ?>">الاختراعات</a> 
                <a href="<?= $baseUrl ?>user/community.php" class="<?= (strpos($currentScript, 'community.php') !== false) ? 'active' : '' ?>">المجتمع</a> 
            </nav> 

            <!-- Dynamic User Actions Section --> 
            <div class="header-actions"> 
                <!-- Theme Toggle --> 
                <div class="toggle-track" id="modeToggle" role="button" aria-label="تبديل الوضع الليلي والنهاري" onClick="toggleTheme()"> 
                    <div class="toggle-thumb" id="modeThumb">☾</div> 
                </div> 

                <?php if (isset($_SESSION['user_id'])): ?> 
                    <!-- Render User Menu if Authenticated --> 
                    <div style="display: flex; align-items: center; gap: 12px;"> 
                        <span class="font-ui" style="font-size: 0.9rem; color: var(--text-main);"> 
                            مرحباً، <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'مستخدم') ?></strong> 
                        </span> 
                        <a href="<?= $baseUrl ?>user/profile.php" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.85rem; text-decoration: none;">الملف الشخصي</a> 
                        <a href="<?= $baseUrl ?>auth/logout.php" class="btn btn-gold" style="padding: 6px 12px; font-size: 0.85rem; text-decoration: none;">خروج</a> 
                    </div> 
                <?php else: ?> 
                    <!-- Render Guest Buttons if Not Authenticated --> 
                    <a href="<?= $baseUrl ?>auth/login.php" class="btn btn-outline" style="text-decoration: none;">تسجيل الدخول</a> 
                    <a href="<?= $baseUrl ?>auth/register.php" class="btn btn-gold" style="text-decoration: none;">ابدأ الآن</a> 
                <?php endif; ?> 
            </div> 
        </div> 
    </header>

    <!-- Theme Toggle JavaScript Script -->
    <script src="<?= $baseUrl ?>assets/js/theme-toggle.js"></script>
</body>
</html>