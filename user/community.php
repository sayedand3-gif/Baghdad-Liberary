<<<<<<< HEAD
<?php
// Include database connection
require_once __DIR__ . '/../config/db.php';

// Set page title for header component
$pageTitle = "مجالس الفكر والمجتمع — مكتبة بغداد";

// Fetch all topics with author information and comments count
$topicsStmt = $pdo->query("
    SELECT 
        t.*, 
        u.name AS author_name, 
        u.avatar_url AS author_avatar,
        (SELECT COUNT(*) FROM comments c WHERE c.topic_id = t.id) AS comments_count
    FROM topics t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
");
$topics = $topicsStmt->fetchAll();

// Include shared navigation header
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Community Layout Custom Styles -->
<style>
.community-grid { 
    display: grid; 
    grid-template-columns: 1fr 320px; 
    gap: 32px; 
    margin-top: 30px; 
}
@media (max-width: 850px) { 
    .community-grid { grid-template-columns: 1fr; } 
}
.discussion-card { 
    background: var(--surface-1); 
    border: 1px solid var(--border-subtle); 
    border-radius: var(--radius-md); 
    padding: 24px; 
    margin-bottom: 20px; 
    transition: transform var(--transition-fast), border-color var(--transition-fast); 
}
.discussion-card:hover { 
    border-color: var(--border-gold); 
    transform: translateY(-2px); 
}
.author-info { 
    display: flex; 
    align-items: center; 
    gap: 12px; 
    margin-bottom: 14px; 
}
.author-avatar { 
    width: 42px; 
    height: 42px; 
    border-radius: 50%; 
    background: var(--surface-2); 
    border: 1px solid var(--border-gold); 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    font-family: var(--font-title); 
    color: var(--gold-soft); 
    font-weight: 700; 
    overflow: hidden;
}
.author-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.tag-badge { 
    background: var(--surface-2); 
    border: 1px solid var(--border-subtle); 
    color: var(--gold-soft); 
    padding: 4px 10px; 
    border-radius: 12px; 
    font-family: var(--font-ui); 
    font-size: 0.75rem; 
}
</style>

<!-- ===================== MAIN COMMUNITY CONTENT ===================== -->
<main style="max-width: 1180px; margin: 0 auto; padding: 40px 24px">
    <!-- Community Top Banner -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; border-bottom: 1px solid var(--border-gold); padding-bottom: 24px;">
        <div>
            <h1 style="font-size: 2rem; margin-bottom: 6px">مجالس الفكر والنقاش</h1>
            <p class="font-ui" style="color: var(--text-muted); font-size: 0.95rem">
                شارك أفكارك ومقتبساتك وناقش باقي القراء حول أهم مؤلفات التراث العربي والإسلامي.
            </p>
        </div>
        <a href="create-topic.php" class="btn btn-gold" style="padding: 10px 24px; text-decoration: none;">
            + إضافة موضوع جديد
        </a>
    </div>

    <div class="community-grid">
        <!-- Main Feed: Dynamic Topics List -->
        <div>
            <?php if (!empty($topics)): ?>
                <?php foreach ($topics as $topic): 
                    $firstChar = mb_substr($topic['author_name'] ?? 'م', 0, 1, 'UTF-8');
                ?>
                    <article class="discussion-card illum-frame">
                        <div class="author-info">
                            <div class="author-avatar">
                                <?php if (!empty($topic['author_avatar'])): ?>
                                    <img src="<?= htmlspecialchars($topic['author_avatar']) ?>" alt="<?= htmlspecialchars($topic['author_name']) ?>" />
                                <?php else: ?>
                                    <?= htmlspecialchars($firstChar) ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div style="font-weight: 700; font-size: 0.95rem">
                                    <?= htmlspecialchars($topic['author_name']) ?>
                                </div>
                                <div class="font-ui" style="font-size: 0.8rem; color: var(--text-muted)">
                                    <?= date('Y-m-d H:i', strtotime($topic['created_at'])) ?>
                                </div>
                            </div>
                        </div>

                        <h2 style="font-size: 1.25rem; margin-bottom: 8px">
                            <?= htmlspecialchars($topic['title']) ?>
                        </h2>
                        <p class="font-ui" style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.7; margin-bottom: 16px;">
                            <?= nl2br(htmlspecialchars($topic['content'])) ?>
                        </p>

                        <div style="display: flex; justify-content: space-between; align-items: center;" class="font-ui">
                            <span class="tag-badge"><?= htmlspecialchars($topic['category'] ?? 'عام') ?></span>
                            <div style="display: flex; gap: 16px; font-size: 0.85rem; color: var(--text-muted);">
                                <span>💬 <?= $topic['comments_count'] ?> تعليقات</span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: var(--text-muted);" class="font-ui">
                    لا توجد مواضيع منشورة حتى الآن. كن أول من يضيف موضوعاً!
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Section -->
        <aside>
            <!-- Community Rules Card -->
            <div style="background: var(--surface-1); border: 1px solid var(--border-gold); border-radius: var(--radius-md); padding: 24px; margin-bottom: 24px;" class="illum-frame">
                <h3 class="font-title" style="font-size: 1.1rem; margin-bottom: 12px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                    آداب المجلس
                </h3>
                <ul class="font-ui" style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.8; padding-right: 18px;">
                    <li>احترام الآراء والتفاعل البنّاء.</li>
                    <li>الاستشهاد بالمصادر عند نقل النصوص.</li>
                    <li>الابتعاد عن النقاشات الجانبية غير العلمية.</li>
                </ul>
            </div>

            <!-- Trending Tags Card -->
            <div style="background: var(--surface-1); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 24px;">
                <h3 class="font-title" style="font-size: 1.1rem; margin-bottom: 16px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                    وسوم متداولة
                </h3>
                <div style="display: flex; flex-wrap: wrap; gap: 8px">
                    <span class="tag-badge">#ابن_خلدون</span>
                    <span class="tag-badge">#علم_الفلك</span>
                    <span class="tag-badge">#فلسفة_إسلامية</span>
                    <span class="tag-badge">#بيت_الحكمة</span>
                </div>
            </div>
        </aside>
    </div>
</main>

<?php
// Include shared footer component
require_once __DIR__ . '/../includes/footer.php';
?>
=======
<!doctype html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>مجالس الفكر والمجتمع — مكتبة بغداد</title>

    <!-- استدعاء ملف التنسيقات الرئيسي -->
    <link rel="stylesheet" href="../assets/css/main.css" />

    <style>
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

      /* Community Page Layout */
      .community-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 32px;
        margin-top: 30px;
      }

      @media (max-width: 850px) {
        .community-grid {
          grid-template-columns: 1fr;
        }
      }

      /* Discussion Cards */
      .discussion-card {
        background: var(--surface-1);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
        padding: 24px;
        margin-bottom: 20px;
        transition:
          transform var(--transition-fast),
          border-color var(--transition-fast);
      }
      .discussion-card:hover {
        border-color: var(--border-gold);
        transform: translateY(-2px);
      }

      .author-info {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
      }
      .author-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: var(--surface-2);
        border: 1px solid var(--border-gold);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-title);
        color: var(--gold-soft);
        font-weight: 700;
      }

      .tag-badge {
        background: var(--surface-2);
        border: 1px solid var(--border-subtle);
        color: var(--gold-soft);
        padding: 4px 10px;
        border-radius: 12px;
        font-family: var(--font-ui);
        font-size: 0.75rem;
      }
    </style>
  </head>
  <body>
    <!-- ===================== HEADER ===================== -->
    <header
      style="
        border-bottom: 1px solid var(--border-gold);
        background: var(--surface-1);
      "
    >
      <div class="header-container">
        <a
          href="../index.php"
          style="display: flex; align-items: center; gap: 10px"
        >
          <span class="star8" style="width: 26px; height: 26px"></span>
          <span class="font-title" style="font-size: 1.5rem; font-weight: 700"
            >مكتبة بغداد</span
          >
        </a>

        <nav class="header-nav font-ui">
          <a href="../index.php" style="color: var(--text-muted)">الرئيسية</a>
          <a href="../discovery/library.php" style="color: var(--text-muted)"
            >المكتبة</a
          >
          <a href="../discovery/scholars.php" style="color: var(--text-muted)"
            >العلماء</a
          >
          <a href="../discovery/inventions.php" style="color: var(--text-muted)"
            >الاختراعات</a
          >
          <a href="community.php" style="color: var(--gold-soft)">المجتمع</a>
        </nav>

        <div class="header-actions">
          <div class="toggle-track" id="modeToggle" role="button">
            <div class="toggle-thumb" id="modeThumb">☾</div>
          </div>
          <a href="profile.php" class="btn btn-outline">حسابي</a>
        </div>
      </div>
    </header>

    <!-- ===================== MAIN CONTENT ===================== -->
    <main style="max-width: 1180px; margin: 0 auto; padding: 40px 24px">
      <!-- Top Banner -->
      <div
        style="
          display: flex;
          justify-content: space-between;
          align-items: center;
          flex-wrap: wrap;
          gap: 20px;
          border-bottom: 1px solid var(--border-gold);
          padding-bottom: 24px;
        "
      >
        <div>
          <h1 style="font-size: 2rem; margin-bottom: 6px">
            مجالس الفكر والنقاش
          </h1>
          <p
            class="font-ui"
            style="color: var(--text-muted); font-size: 0.95rem"
          >
            شارك أفكارك ومقتبساتك وناقش باقي القراء حول أهم مؤلفات التراث العربي
            والإسلامي.
          </p>
        </div>
        <button class="btn btn-gold" style="padding: 10px 24px">
          + إضافة موضوع جديد
        </button>
      </div>

      <div class="community-grid">
        <!-- Main Content Column: Discussions List -->
        <div>
          <!-- Post 1 -->
          <article class="discussion-card illum-frame">
            <div class="author-info">
              <div class="author-avatar">أ</div>
              <div>
                <div style="font-weight: 700; font-size: 0.95rem">
                  أحمد المنصوري
                </div>
                <div
                  class="font-ui"
                  style="font-size: 0.8rem; color: var(--text-muted)"
                >
                  منذ ساعتين • في كتاب: مقدمة ابن خلدون
                </div>
              </div>
            </div>

            <h2 style="font-size: 1.25rem; margin-bottom: 8px">
              هل العصبية في الفكر الخلدوني ما زالت معاصرة؟
            </h2>
            <p
              class="font-ui"
              style="
                color: var(--text-muted);
                font-size: 0.9rem;
                line-height: 1.7;
                margin-bottom: 16px;
              "
            >
              أثناء قراءتي للباب الأول من المقدمة، تساءلت عن إسقاط مفهوم
              "العصبية الخلدونية" على التكتلات الاقتصادية والتحالفات الحديثة
              اليوم. هل تتفقون أن التكتلات الحديثة تشبه العصبية القبائلية
              القديمة؟
            </p>

            <div
              style="
                display: flex;
                justify-content: space-between;
                align-items: center;
              "
              class="font-ui"
            >
              <span class="tag-badge">فلسفة وتاريخ</span>
              <div
                style="
                  display: flex;
                  gap: 16px;
                  font-size: 0.85rem;
                  color: var(--text-muted);
                "
              >
                <span>💬 ١٤ تعليقًا</span>
                <span>❤️ ٢٨ إعجابًا</span>
              </div>
            </div>
          </article>

          <!-- Post 2 -->
          <article class="discussion-card illum-frame">
            <div class="author-info">
              <div class="author-avatar">م</div>
              <div>
                <div style="font-weight: 700; font-size: 0.95rem">
                  مريم العطار
                </div>
                <div
                  class="font-ui"
                  style="font-size: 0.8rem; color: var(--text-muted)"
                >
                  منذ ٥ ساعات • في كتاب: القانون في الطب
                </div>
              </div>
            </div>

            <h2 style="font-size: 1.25rem; margin-bottom: 8px">
              مقتبس ملهم من ابن سينا حول الوقاية والتوازن
            </h2>
            <blockquote
              class="font-body"
              style="
                background: var(--surface-2);
                border-right: 3px solid var(--gold-primary);
                padding: 12px 16px;
                margin-bottom: 16px;
                border-radius: 4px;
                font-size: 0.95rem;
                color: var(--gold-soft);
              "
            >
              "الوهم نصف الداء، والاطمئنان نصف الدواء، والصبر أول خطوات الشفاء."
            </blockquote>

            <div
              style="
                display: flex;
                justify-content: space-between;
                align-items: center;
              "
              class="font-ui"
            >
              <span class="tag-badge">مقتبسات</span>
              <div
                style="
                  display: flex;
                  gap: 16px;
                  font-size: 0.85rem;
                  color: var(--text-muted);
                "
              >
                <span>💬 ٨ تعليقات</span>
                <span>❤️ ٥٤ إعجابًا</span>
              </div>
            </div>
          </article>
        </div>

        <!-- Sidebar Column -->
        <aside>
          <!-- Community Rules Card -->
          <div
            style="
              background: var(--surface-1);
              border: 1px solid var(--border-gold);
              border-radius: var(--radius-md);
              padding: 24px;
              margin-bottom: 24px;
            "
            class="illum-frame"
          >
            <h3
              class="font-title"
              style="
                font-size: 1.1rem;
                margin-bottom: 12px;
                border-bottom: 1px solid var(--border-subtle);
                padding-bottom: 8px;
              "
            >
              آداب المجلس
            </h3>
            <ul
              class="font-ui"
              style="
                font-size: 0.85rem;
                color: var(--text-muted);
                line-height: 1.8;
                padding-right: 18px;
              "
            >
              <li>احترام الآراء والتفاعل البنّاء.</li>
              <li>الاستشهاد بالمصادر عند نقل النصوص.</li>
              <li>الابتعاد عن النقاشات الجانبية غير العلمية.</li>
            </ul>
          </div>

          <!-- Trending Topics Card -->
          <div
            style="
              background: var(--surface-1);
              border: 1px solid var(--border-subtle);
              border-radius: var(--radius-md);
              padding: 24px;
            "
          >
            <h3
              class="font-title"
              style="
                font-size: 1.1rem;
                margin-bottom: 16px;
                border-bottom: 1px solid var(--border-subtle);
                padding-bottom: 8px;
              "
            >
              وسوم متداولة
            </h3>
            <div style="display: flex; flex-wrap: wrap; gap: 8px">
              <a href="#" class="tag-badge" style="text-decoration: none"
                >#ابن_خلدون</a
              >
              <a href="#" class="tag-badge" style="text-decoration: none"
                >#علم_الفلك</a
              >
              <a href="#" class="tag-badge" style="text-decoration: none"
                >#فلسفة_إسلامية</a
              >
              <a href="#" class="tag-badge" style="text-decoration: none"
                >#البيت_الحكمة</a
              >
            </div>
          </div>
        </aside>
      </div>
    </main>

    <!-- ===================== FOOTER ===================== -->
    <footer
      style="
        background: var(--surface-1);
        border-top: 1px solid var(--border-gold);
        padding: 40px 24px 20px;
        margin-top: 60px;
      "
    >
      <div
        style="
          max-width: 1180px;
          margin: 0 auto;
          display: flex;
          justify-content: space-between;
          align-items: center;
          flex-wrap: wrap;
          gap: 20px;
        "
      >
        <div style="display: flex; align-items: center; gap: 10px">
          <span class="star8" style="width: 20px; height: 20px"></span>
          <span class="font-title" style="font-size: 1.2rem; font-weight: 700"
            >مكتبة بغداد</span
          >
        </div>
        <div
          class="font-ui"
          style="
            display: flex;
            gap: 20px;
            font-size: 0.85rem;
            color: var(--text-muted);
          "
        >
          <a href="../system/about-faq.php">عن المكتبة</a>
          <a href="../system/about-faq.php">الأسئلة الشائعة</a>
          <a href="../system/about-faq.php">اتصل بنا</a>
        </div>
        <div
          class="font-ui"
          style="color: var(--text-muted); font-size: 0.85rem"
        >
          © 2026 مكتبة بغداد — بيت الحكمة الرقمي. جميع الحقوق محفوظة.
        </div>
      </div>
    </footer>

    <script src="../assets/js/main.js"></script>
  </body>
</html>
>>>>>>> 26402661383b426caf30dea836d8d431ec57debc
