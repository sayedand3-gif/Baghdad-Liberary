<!doctype html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>الكتاب الصوتي — مقدمة ابن خلدون</title>

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

      /* Player Layout Grid */
      .audio-player-layout {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 36px;
        margin-top: 30px;
      }

      @media (max-width: 850px) {
        .audio-player-layout {
          grid-template-columns: 1fr;
        }
      }

      /* Main Audio Card */
      .player-card {
        background: var(--surface-1);
        border: 1px solid var(--border-gold);
        border-radius: var(--radius-md);
        padding: 32px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
      }

      .audio-cover {
        width: 220px;
        height: 220px;
        border-radius: var(--radius-md);
        background: linear-gradient(135deg, #1e2740, #2c625d);
        border: 2px solid var(--border-gold);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      }

      /* Custom Audio Progress Bar */
      .progress-container {
        width: 100%;
        margin: 20px 0 10px;
      }
      .progress-bar {
        width: 100%;
        height: 6px;
        background: var(--surface-2);
        border-radius: 3px;
        overflow: hidden;
        cursor: pointer;
        position: relative;
      }
      .progress-fill {
        width: 35%;
        height: 100%;
        background: var(--gold-primary);
      }

      .time-stamps {
        display: flex;
        justify-content: space-between;
        font-family: var(--font-ui);
        font-size: 0.8rem;
        color: var(--text-muted);
      }

      /* Player Controls */
      .player-controls {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        margin-top: 16px;
      }

      .play-btn-lg {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--gold-primary);
        color: var(--text-dark);
        border: none;
        font-size: 1.4rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform var(--transition-fast);
      }
      .play-btn-lg:hover {
        transform: scale(1.08);
      }

      .control-btn {
        background: var(--surface-2);
        border: 1px solid var(--border-subtle);
        color: var(--text-main);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.9rem;
      }
      .control-btn:hover {
        border-color: var(--border-gold);
        color: var(--gold-soft);
      }

      /* Playlist Items */
      .playlist-card {
        background: var(--surface-1);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
        padding: 24px;
      }

      .playlist-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        border-radius: var(--radius-sm);
        margin-bottom: 8px;
        background: var(--bg-primary);
        border: 1px solid transparent;
        cursor: pointer;
        transition: all var(--transition-fast);
      }
      .playlist-item:hover,
      .playlist-item.active {
        border-color: var(--border-gold);
        background: var(--surface-2);
      }
      .playlist-item.active .track-title {
        color: var(--gold-soft);
        font-weight: 700;
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
          <a href="../discovery/library.php" style="color: var(--gold-soft)"
            >المكتبة</a
          >
          <a href="../discovery/scholars.php" style="color: var(--text-muted)"
            >العلماء</a
          >
          <a href="../discovery/inventions.php" style="color: var(--text-muted)"
            >الاختراعات</a
          >
          <a href="../user/community.php" style="color: var(--text-muted)"
            >المجتمع</a
          >
        </nav>

        <div class="header-actions">
          <div class="toggle-track" id="modeToggle" role="button">
            <div class="toggle-thumb" id="modeThumb">☾</div>
          </div>
        </div>
      </div>
    </header>

    <!-- ===================== MAIN CONTENT ===================== -->
    <main style="max-width: 1180px; margin: 0 auto; padding: 40px 24px">
      <div
        style="
          display: flex;
          align-items: center;
          gap: 12px;
          margin-bottom: 10px;
        "
      >
        <a
          href="book-details.php"
          class="font-ui"
          style="color: var(--text-muted); font-size: 0.9rem"
          >← العودة لصفحة الكتاب</a
        >
      </div>

      <div class="audio-player-layout">
        <!-- Left Column: Main Player UI -->
        <div class="player-card illum-frame">
          <div class="audio-cover">
            <span
              class="star8"
              style="width: 40px; height: 40px; margin-bottom: 10px"
            ></span>
            <span class="font-title" style="color: #fff; font-size: 1.3rem"
              >مقدمة ابن خلدون</span
            >
          </div>

          <h2 style="font-size: 1.4rem; margin-bottom: 4px">
            الباب الأول: في العمران البشري
          </h2>
          <div
            class="font-ui"
            style="
              color: var(--gold-soft);
              font-size: 0.85rem;
              margin-bottom: 16px;
            "
          >
            أداء بصوتي: الفضل بن يحيى
          </div>

          <!-- Progress Bar -->
          <div class="progress-container">
            <div class="progress-bar">
              <div class="progress-fill"></div>
            </div>
            <div class="time-stamps">
              <span>12:45</span>
              <span>34:10</span>
            </div>
          </div>

          <!-- Player Controls -->
          <div class="player-controls">
            <button class="control-btn" title="تأخير 15 ثانية">↺15</button>
            <button
              class="play-btn-lg"
              id="playPauseBtn"
              title="تشغيل/إيقاف مؤقت"
            >
              ▶
            </button>
            <button class="control-btn" title="تقديم 15 ثانية">15↻</button>
          </div>

          <!-- Additional Controls -->
          <div
            style="display: flex; gap: 12px; margin-top: 24px"
            class="font-ui"
          >
            <button
              class="btn btn-outline"
              style="font-size: 0.8rem; padding: 6px 14px"
              id="speedBtn"
            >
              السرعة: 1.0x
            </button>
            <button
              class="btn btn-outline"
              style="font-size: 0.8rem; padding: 6px 14px"
            >
              🔊 الصوت
            </button>
          </div>
        </div>

        <!-- Right Column: Audio Chapters Playlist -->
        <div class="playlist-card illum-frame">
          <h3
            class="font-title"
            style="
              font-size: 1.3rem;
              margin-bottom: 20px;
              border-bottom: 1px solid var(--border-gold);
              padding-bottom: 10px;
            "
          >
            فصول الكتاب الصوتية (١٢ مقطع)
          </h3>

          <div class="font-ui">
            <!-- Track 1 -->
            <div class="playlist-item">
              <div>
                <div
                  class="track-title"
                  style="font-size: 0.95rem; margin-bottom: 2px"
                >
                  01. المقدمة في فضل علم التاريخ
                </div>
                <div style="font-size: 0.8rem; color: var(--text-muted)">
                  المدة: ١٨ دقيقة
                </div>
              </div>
              <span style="color: var(--gold-soft); font-size: 0.85rem"
                >مكتمل ✓</span
              >
            </div>

            <!-- Track 2 (Active) -->
            <div class="playlist-item active">
              <div>
                <div
                  class="track-title"
                  style="font-size: 0.95rem; margin-bottom: 2px"
                >
                  02. الباب الأول: في العمران البشري على الجملة
                </div>
                <div style="font-size: 0.8rem; color: var(--gold-soft)">
                  جاري التشغيل الان...
                </div>
              </div>
              <span style="color: var(--gold-primary); font-weight: 700"
                >▶ 34:10</span
              >
            </div>

            <!-- Track 3 -->
            <div class="playlist-item">
              <div>
                <div
                  class="track-title"
                  style="font-size: 0.95rem; margin-bottom: 2px"
                >
                  03. الفصل الأول: في البداوة والأمم الوحشية
                </div>
                <div style="font-size: 0.8rem; color: var(--text-muted)">
                  المدة: ٢٥ دقيقة
                </div>
              </div>
              <span style="color: var(--text-muted); font-size: 0.85rem"
                >🔒</span
              >
            </div>

            <!-- Track 4 -->
            <div class="playlist-item">
              <div>
                <div
                  class="track-title"
                  style="font-size: 0.95rem; margin-bottom: 2px"
                >
                  04. الفصل الثاني: في الملك والدولة والراتب
                </div>
                <div style="font-size: 0.8rem; color: var(--text-muted)">
                  المدة: ٤٠ دقيقة
                </div>
              </div>
              <span style="color: var(--text-muted); font-size: 0.85rem"
                >🔒</span
              >
            </div>
          </div>
        </div>
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
    <script>
      // Play/Pause toggle logic
      const playPauseBtn = document.getElementById("playPauseBtn");
      let isPlaying = false;

      playPauseBtn.addEventListener("click", () => {
        isPlaying = !isPlaying;
        playPauseBtn.textContent = isPlaying ? "❚❚" : "▶";
      });

      // Speed toggle logic
      const speedBtn = document.getElementById("speedBtn");
      const speeds = ["1.0x", "1.25x", "1.5x", "2.0x"];
      let currentSpeedIdx = 0;

      speedBtn.addEventListener("click", () => {
        currentSpeedIdx = (currentSpeedIdx + 1) % speeds.length;
        speedBtn.textContent = "السرعة: " + speeds[currentSpeedIdx];
      });
    </script>
  </body>
</html>
