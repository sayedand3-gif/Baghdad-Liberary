<?php 
// Include Database connection
require_once __DIR__ . '/../config/db.php';

// Fetch scholars list from database
$stmt = $pdo->query("SELECT * FROM scholars ORDER BY id ASC");
$scholars = $stmt->fetchAll();

// Dynamic page title configuration
$pageTitle = "دليل العلماء والفلاسفة — مكتبة بغداد";

// Include shared layout header
require_once __DIR__ . '/../includes/header.php'; 
?>

<!-- Scholars Custom Grid Styles -->
<style>
.scholars-grid { 
    display: grid; 
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); 
    gap: 24px; 
    margin-top: 40px; 
}
.scholar-card { 
    background: var(--surface-1); 
    border: 1px solid var(--border-gold); 
    border-radius: var(--radius-md); 
    padding: 28px 24px; 
    display: flex; 
    flex-direction: column; 
    justify-content: space-between; 
    text-decoration: none;
    color: inherit;
    transition: transform var(--transition-fast), box-shadow var(--transition-fast); 
}
.scholar-card:hover { 
    transform: translateY(-4px); 
    box-shadow: var(--shadow-card); 
}
.scholar-avatar { 
    width: 64px; 
    height: 64px; 
    border-radius: 50%; 
    background: var(--surface-2); 
    border: 2px solid var(--border-gold); 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    font-family: var(--font-title); 
    font-size: 1.4rem; 
    color: var(--gold-soft); 
    margin-bottom: 16px; 
    overflow: hidden;
}
</style>

<!-- ===================== MAIN SCHOLARS CONTENT ===================== -->
<main style="max-width: 1180px; margin: 0 auto; padding: 50px 24px;">
    <!-- Section Header Title -->
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-size: 2.2rem; margin-bottom: 10px;">روّاد الفكر والعلوم</h1>
        <p class="font-ui" style="color: var(--text-muted); font-size: 0.95rem;">
            استكشف سير العلماء والفلاسفة الذين شكلوا المعرفة الإنسانية ووضعوا لبنات العلوم الحديثة.
        </p>
    </div>

    <!-- Dynamic Scholars Card Grid -->
    <div class="scholars-grid">
        <?php foreach ($scholars as $scholar): ?>
            <a href="scholar-details.php?id=<?= $scholar['id'] ?>" class="scholar-card illum-frame">
                <div>
                    <!-- Avatar or Initial Character -->
                    <div class="scholar-avatar">
                        <?php if (!empty($scholar['image_url'])): ?>
                            <img src="<?= htmlspecialchars($scholar['image_url']) ?>" alt="<?= htmlspecialchars($scholar['name']) ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                        <?php else: ?>
                            <?= mb_substr($scholar['name'], 0, 1, 'UTF-8') ?>
                        <?php endif; ?>
                    </div>
                    
                    <h2 style="font-size: 1.4rem; margin-bottom: 4px; margin-top: 12px;"><?= htmlspecialchars($scholar['name']) ?></h2>
                    <div class="font-ui" style="color: var(--gold-soft); font-size: 0.85rem; margin-bottom: 12px;"><?= htmlspecialchars($scholar['specialty'] ?? '') ?></div>
                    <p class="font-ui" style="color: var(--text-muted); font-size: 0.85rem; line-height: 1.6;">
                        <?= htmlspecialchars($scholar['bio'] ?? '') ?>
                    </p>
                </div>
                
                <div class="font-ui" style="margin-top: 20px; font-size: 0.8rem; color: var(--text-muted); border-top: 1px solid var(--border-subtle); padding-top: 12px; display: flex; justify-content: space-between;">
                    <span>العصر: <?= htmlspecialchars($scholar['era'] ?? 'الإسلامي') ?></span>
                    <span style="color: var(--gold-soft); font-weight: 700;">عرض التفاصيل ←</span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</main>

<?php 
// Include shared layout footer
require_once __DIR__ . '/../includes/footer.php'; 
?>