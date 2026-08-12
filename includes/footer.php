<!-- Footer Component -->
<footer style="background: var(--surface-1); border-top: 1px solid var(--border-gold); padding: 40px 24px 20px; margin-top: 40px;">
    <div style="max-width: 1180px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <!-- Footer Logo -->
        <div style="display: flex; align-items: center; gap: 10px">
            <span class="star8" style="width: 20px; height: 20px"></span>
            <span class="font-title" style="font-size: 1.2rem; font-weight: 700">مكتبة بغداد</span>
        </div>

        <!-- Footer Navigation Links -->
        <div class="font-ui" style="display: flex; gap: 20px; font-size: 0.85rem; color: var(--text-muted);">
            <a href="system/about-faq.php">عن المكتبة</a>
            <a href="system/about-faq.php">الأسئلة الشائعة</a>
            <a href="system/about-faq.php">اتصل بنا</a>
        </div>

        <!-- Copyright -->
        <div class="font-ui" style="color: var(--text-muted); font-size: 0.85rem">
            © 2026 مكتبة بغداد — بيت الحكمة الرقمي. جميع الحقوق محفوظة.
        </div>
    </div>
</footer>

<!-- External JavaScript Scripts -->
<script src="assets/js/prayer-timer.js"></script>
<?php if (isset($timings)): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        startPrayerTimer(<?php echo json_encode($timings); ?>);
    });
</script>
<?php endif; ?>
<script src="assets/js/main.js"></script>
</body>
</html>