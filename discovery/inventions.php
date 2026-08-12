<?php 
// Include Database connection
require_once __DIR__ . '/../config/db.php';

// Fetch inventions list from database
$stmt = $pdo->query("SELECT * FROM inventions ORDER BY id ASC");
$inventions = $stmt->fetchAll();

// Dynamic page title configuration
$pageTitle = "معرض الاختراعات والأدوات — مكتبة بغداد";

// Include shared layout header
require_once __DIR__ . '/../includes/header.php'; 
?>

<!-- Inventions Custom Grid Styles -->
<style>
.inventions-grid { 
    display: grid; 
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); 
    gap: 28px; 
    margin-top: 40px; 
}
.invention-card { 
    background: var(--surface-1); 
    border: 1px solid var(--border-subtle); 
    border-radius: var(--radius-md); 
    overflow: hidden; 
    display: flex; 
    flex-direction: column; 
    padding: 24px;
    transition: transform var(--transition-fast), border-color var(--transition-fast); 
}
.invention-card:hover { 
    transform: translateY(-5px); 
    border-color: var(--border-gold); 
}
.invention-image { 
    height: 180px; 
    background: linear-gradient(135deg, var(--surface-2), #2c2214); 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    border-radius: var(--radius-sm);
    overflow: hidden;
    margin-bottom: 16px;
}
.invention-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.inventor-tag {
    font-size: 0.85rem;
    color: var(--gold-soft);
    margin-top: 12px;
    font-weight: 700;
}
</style>

<!-- ===================== MAIN INVENTIONS CONTENT ===================== -->
<main style="max-width: 1180px; margin: 0 auto; padding: 50px 24px;">
    <!-- Section Header Title -->
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-size: 2.2rem; margin-bottom: 10px;">روائع الاختراعات والابتكارات</h1>
        <p class="font-ui" style="color: var(--text-muted); font-size: 0.95rem;">
            تصفح الأجهزة والآلات التراثية التي ابتكرها علماء الحضارة الإسلامية وغيرت مجرى التاريخ.
        </p>
    </div>

    <!-- Dynamic Inventions Card Grid -->
    <div class="inventions-grid">
        <?php foreach ($inventions as $invention): ?>
            <div class="invention-card illum-frame">
                <?php if (!empty($invention['image_url'])): ?>
                    <div class="invention-image">
                        <img src="<?= htmlspecialchars($invention['image_url']) ?>" alt="<?= htmlspecialchars($invention['title']) ?>">
                    </div>
                <?php endif; ?>
                
                <h3 style="font-size: 1.3rem; margin-bottom: 8px;"><?= htmlspecialchars($invention['title']) ?></h3>
                <p class="font-ui" style="color: var(--text-muted); font-size: 0.88rem; line-height: 1.6; flex: 1;">
                    <?= htmlspecialchars($invention['description'] ?? '') ?>
                </p>
                <div class="inventor-tag font-ui">
                    المخترع: <?= htmlspecialchars($invention['inventor'] ?? 'غير محدد') ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php 
// Include shared layout footer
require_once __DIR__ . '/../includes/footer.php'; 
?>