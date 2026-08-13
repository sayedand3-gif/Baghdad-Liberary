/* =========================================================
   مكتبة بغداد — Main JavaScript System
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
  // 1. تهيئة التبديل بين الوضع الليلي والنهاري (Dark/Light Mode)
  initThemeToggle();
});

/**
 * وظيفة التبديل بين Dark Mode و Light Mode وحفظ خيار المستخدم في localStorage
 */
function initThemeToggle() {
  const toggleBtn = document.getElementById("modeToggle");
  const toggleThumb = document.getElementById("modeThumb");

  // جلب الخيار المحفوظ سابقاً إذا وجد
  const savedTheme = localStorage.getItem("baghdad_theme");
  if (savedTheme === "light") {
    document.documentElement.classList.add("light");
    if (toggleThumb) toggleThumb.textContent = "☀";
  } else {
    document.documentElement.classList.remove("light");
    if (toggleThumb) toggleThumb.textContent = "☾";
  }

  if (!toggleBtn) return;

  toggleBtn.addEventListener("click", () => {
    const isLight = document.documentElement.classList.toggle("light");

    // تحديث الأيقونة
    if (toggleThumb) {
      toggleThumb.textContent = isLight ? "☀" : "☾";
    }

    // حفظ الخيار في متصفح المستخدم
    localStorage.setItem("baghdad_theme", isLight ? "light" : "dark");
  });
}
