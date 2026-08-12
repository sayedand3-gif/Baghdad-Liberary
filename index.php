<?php 
require_once 'controllers/HomeController.php'; 

// Page Title Variable
$pageTitle = "مكتبة بغداد — بيت الحكمة الرقمي";

// Include Header Component
require_once __DIR__ . '/includes/header.php'; 
?>

<style>
/* Dynamic Grid Layouts */
.hero-grid { 
    display: grid; 
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); 
    gap: 40px; 
    align-items: center; 
    max-width: 1180px; 
    margin: 0 auto; 
}
.stats-grid { 
    display: grid; 
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
    gap: 24px; 
    text-align: center; 
    max-width: 1180px; 
    margin: 0 auto; 
}
.prayer-card-active { 
    background: var(--surface-2); 
    border-radius: 4px; 
    padding: 4px 8px; 
    border: 1px solid var(--gold-soft); 
}
</style>

<!-- Hero Section -->
<section style="padding: 70px 24px">
    <div class="hero-grid">
        <div>
            <div class="font-ui" style="color: var(--gold-soft); font-size: 0.85rem; font-weight: 700; margin-bottom: 8px;">
                بيت الحكمة الرقمي — من بغداد إلى العالم
            </div>
            <h1 style="font-size: 3rem; line-height: 1.2; margin-bottom: 20px">
                إحياء العلم <br />
                <span style="color: var(--gold-soft)">واستعادة المجد</span>
            </h1>
            <p style="color: var(--text-muted); font-size: 1.05rem; line-height: 1.9; max-width: 500px; margin-bottom: 30px;">
                منصة تجمّع أهم كتب الفكر العالمي مترجمة، إلى جانب تراث علماء الحضارة الإسلامية، ومواقيت الصلاة والأذكار اليومية في مكان واحد.
            </p>
            <div style="display: flex; gap: 16px">
                <a href="discovery/library.php" class="btn btn-gold">استكشف المكتبة</a>
                <a href="auth/register.php" class="btn btn-outline">ابدأ رحلتك الآن</a>
            </div>
        </div>

        <!-- Prayer Timings Card -->
        <div class="illum-frame" style="background: var(--surface-1); border: 1px solid var(--border-gold); border-radius: var(--radius-md); padding: 28px; box-shadow: var(--shadow-main);">
            <div class="font-ui" style="font-size: 0.85rem; color: var(--text-muted); display: flex; justify-content: space-between;">
                <span>مواقيت الصلاة</span>
                <span><?php echo htmlspecialchars(($city ?? 'القاهرة') . '، ' . ($country ?? 'مصر')); ?></span>
            </div>
            <div class="font-title" id="countdownTimer" style="text-align: center; font-size: 2.5rem; color: var(--gold-soft); margin: 16px 0 6px;">
                ٠٠:٠٠:٠٠
            </div>
            <div style="text-align: center; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 24px;" id="nextPrayerLabel">
                المتبقي حتى الأذان
            </div>
            <div class="font-ui" style="display: flex; justify-content: space-between; text-align: center; font-size: 0.8rem;">
                <div id="card-Fajr">
                    <div style="color: var(--text-muted)">الفجر</div>
                    <div style="font-weight: 700; margin-top: 4px"><?php echo function_exists('toEasternDigits') && function_exists('format12Hour') ? toEasternDigits(format12Hour($timings['Fajr'] ?? '05:00')) : ($timings['Fajr'] ?? '05:00'); ?></div>
                </div>
                <div id="card-Sunrise">
                    <div style="color: var(--text-muted)">الشروق</div>
                    <div style="font-weight: 700; margin-top: 4px"><?php echo function_exists('toEasternDigits') && function_exists('format12Hour') ? toEasternDigits(format12Hour($timings['Sunrise'] ?? '06:30')) : ($timings['Sunrise'] ?? '06:30'); ?></div>
                </div>
                <div id="card-Dhuhr">
                    <div style="color: var(--text-muted)">الظهر</div>
                    <div style="font-weight: 700; margin-top: 4px"><?php echo function_exists('toEasternDigits') && function_exists('format12Hour') ? toEasternDigits(format12Hour($timings['Dhuhr'] ?? '12:00')) : ($timings['Dhuhr'] ?? '12:00'); ?></div>
                </div>
                <div id="card-Asr">
                    <div style="color: var(--text-muted)">العصر</div>
                    <div style="font-weight: 700; margin-top: 4px"><?php echo function_exists('toEasternDigits') && function_exists('format12Hour') ? toEasternDigits(format12Hour($timings['Asr'] ?? '15:30')) : ($timings['Asr'] ?? '15:30'); ?></div>
                </div>
                <div id="card-Maghrib">
                    <div style="color: var(--text-muted)">المغرب</div>
                    <div style="font-weight: 700; margin-top: 4px"><?php echo function_exists('toEasternDigits') && function_exists('format12Hour') ? toEasternDigits(format12Hour($timings['Maghrib'] ?? '18:15')) : ($timings['Maghrib'] ?? '18:15'); ?></div>
                </div>
                <div id="card-Isha">
                    <div style="color: var(--text-muted)">العشاء</div>
                    <div style="font-weight: 700; margin-top: 4px"><?php echo function_exists('toEasternDigits') && function_exists('format12Hour') ? toEasternDigits(format12Hour($timings['Isha'] ?? '19:45')) : ($timings['Isha'] ?? '19:45'); ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section style="padding: 20px 24px 60px; border-bottom: 1px solid var(--border-subtle);">
    <div class="stats-grid">
        <div>
            <div class="font-title" style="font-size: 2.4rem; color: var(--gold-soft)">+<?php echo $booksCount ?? 0; ?></div>
            <div class="font-ui" style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">كتاب مترجم</div>
        </div>
        <div>
            <div class="font-title" style="font-size: 2.4rem; color: var(--gold-soft)">+<?php echo $inventionsCount ?? 0; ?></div>
            <div class="font-ui" style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">اختراع وإنجاز</div>
        </div>
        <div>
            <div class="font-title" style="font-size: 2.4rem; color: var(--gold-soft)">+١,٢٠٠</div>
            <div class="font-ui" style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">ملخص كتاب</div>
        </div>
        <div>
            <div class="font-title" style="font-size: 2.4rem; color: var(--gold-soft)">+٣٥٠,٠٠٠</div>
            <div class="font-ui" style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">مستخدم نشط</div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section style="padding: 70px 24px; background: var(--surface-1); border-bottom: 1px solid var(--border-gold);">
    <div style="max-width: 1180px; margin: 0 auto">
        <div style="text-align: center; margin-bottom: 44px">
            <div class="font-ui" style="color: var(--gold-soft); font-size: 0.85rem; font-weight: 700; margin-bottom: 6px;">كل ما تحتاجه</div>
            <h2 style="font-size: 2rem">في مكان واحد</h2>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
            <div class="illum-frame" style="background: var(--bg-primary); border: 1px solid var(--border-gold); padding: 24px; border-radius: var(--radius-sm);">
                <span class="star8" style="width: 20px; height: 20px; margin-bottom: 12px"></span>
                <h3 style="font-size: 1.2rem; margin-bottom: 8px">ترجمة وتلخيص الكتب</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.8;">أهم كتب الفكر العالمي مترجمة ومختصرة بلغة عربية سليمة.</p>
            </div>
            <div class="illum-frame" style="background: var(--bg-primary); border: 1px solid var(--border-gold); padding: 24px; border-radius: var(--radius-sm);">
                <span class="star8" style="width: 20px; height: 20px; margin-bottom: 12px"></span>
                <h3 style="font-size: 1.2rem; margin-bottom: 8px">التراث والاختراعات</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.8;">تعرف على إنجازات علماء المسلمين وأثرهم في الحضارة الإنسانية.</p>
            </div>
            <div class="illum-frame" style="background: var(--bg-primary); border: 1px solid var(--border-gold); padding: 24px; border-radius: var(--radius-sm);">
                <span class="star8" style="width: 20px; height: 20px; margin-bottom: 12px"></span>
                <h3 style="font-size: 1.2rem; margin-bottom: 8px">التذكير والعبادات</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.8;">مواقيت الصلاة، الأذكار، والتحديات القرآنية في مكان واحد.</p>
            </div>
            <div class="illum-frame" style="background: var(--bg-primary); border: 1px solid var(--border-gold); padding: 24px; border-radius: var(--radius-sm);">
                <span class="star8" style="width: 20px; height: 20px; margin-bottom: 12px"></span>
                <h3 style="font-size: 1.2rem; margin-bottom: 8px">المجتمع والمناقشات</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.8;">شارك اقتباساتك وتفاعل مع القراء حول العالم.</p>
            </div>
        </div>
    </div>
</section>

<!-- Books Showcase Section -->
<section style="padding: 70px 24px">
    <div style="max-width: 1180px; margin: 0 auto">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 32px;">
            <div>
                <div class="font-ui" style="color: var(--gold-soft); font-size: 0.85rem; font-weight: 700;">المكتبة الرقمية</div>
                <h2 style="font-size: 1.8rem; margin-top: 4px">أحدث الكتب المترجمة</h2>
            </div>
            <a href="discovery/library.php" class="font-ui" style="color: var(--gold-soft); font-size: 0.9rem">عرض الكل ←</a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <?php if (!empty($books)): ?>
                <?php foreach ($books as $book): $coverImage = !empty($book['cover_image']) ? ltrim($book['cover_image'], './') : ''; ?>
                    <a href="reader-module/book-details.php?id=<?= $book['id'] ?>" style="background: var(--surface-1); border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); overflow: hidden; transition: transform var(--transition-fast); display: block; text-decoration: none; color: inherit;">
                        <div style="height: 240px; background: linear-gradient(135deg, #1e2740, #2c625d); display: flex; align-items: flex-end; padding: 16px; background-size: cover; background-position: center; <?= !empty($coverImage) ? "background-image: linear-gradient(to top, rgba(0,0,0,0.8), transparent), url('" . htmlspecialchars($coverImage) . "');" : "" ?>">
                            <span class="font-title" style="font-size: 1.2rem; color: #fff; text-shadow: 0 2px 4px rgba(0,0,0,0.8);"><?= htmlspecialchars($book['title']) ?></span>
                        </div>
                        <div style="padding: 14px">
                            <div style="font-weight: 700; font-size: 0.95rem"><?= htmlspecialchars($book['author_name'] ?? '') ?></div>
                            <div class="font-ui" style="color: var(--gold-soft); font-size: 0.85rem; margin-top: 6px;">
                                ★ <?= function_exists('toEasternDigits') ? toEasternDigits($book['rating'] ?? '4.9') : ($book['rating'] ?? '4.9') ?> (<?= htmlspecialchars($book['category'] ?? '') ?>)
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php 
// Include Footer Component
require_once __DIR__ . '/includes/footer.php'; 
?>