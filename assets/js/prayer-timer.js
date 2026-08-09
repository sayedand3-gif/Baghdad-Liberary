function startPrayerTimer(rawTimings) {
  const prayerKeys = ["Fajr", "Sunrise", "Dhuhr", "Asr", "Maghrib", "Isha"];

  const prayerNamesAr = {
    Fajr: "الفجر",
    Sunrise: "الشروق",
    Dhuhr: "الظهر",
    Asr: "العصر",
    Maghrib: "المغرب",
    Isha: "العشاء",
  };

  function toArabicDigits(str) {
    const western = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
    const eastern = ["٠", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩"];
    return String(str).replace(/[0-9]/g, (w) => eastern[western.indexOf(w)]);
  }

  function update() {
    const now = new Date();
    let nextPrayerKey = null;
    let nextPrayerTime = null;

    for (let key of prayerKeys) {
      if (!rawTimings[key]) continue;
      const timeClean = rawTimings[key].split(" ")[0];
      const [h, m] = timeClean.split(":").map(Number);

      const pTime = new Date(now);
      pTime.setHours(h, m, 0, 0);

      if (pTime > now) {
        nextPrayerKey = key;
        nextPrayerTime = pTime;
        break;
      }
    }

    if (!nextPrayerTime) {
      nextPrayerKey = "Fajr";
      const timeClean = rawTimings["Fajr"].split(" ")[0];
      const [h, m] = timeClean.split(":").map(Number);
      nextPrayerTime = new Date(now);
      nextPrayerTime.setDate(now.getDate() + 1);
      nextPrayerTime.setHours(h, m, 0, 0);
    }

    const diffMs = nextPrayerTime - now;
    const totalSec = Math.floor(diffMs / 1000);

    const hours = Math.floor(totalSec / 3600);
    const minutes = Math.floor((totalSec % 3600) / 60);
    const seconds = totalSec % 60;

    const formattedTime =
      String(hours).padStart(2, "0") +
      ":" +
      String(minutes).padStart(2, "0") +
      ":" +
      String(seconds).padStart(2, "0");

    const timerEl = document.getElementById("countdownTimer");
    const labelEl = document.getElementById("nextPrayerLabel");

    if (timerEl) timerEl.innerText = toArabicDigits(formattedTime);
    if (labelEl)
      labelEl.innerText = `المتبقي حتى أذان ${prayerNamesAr[nextPrayerKey]}`;

    prayerKeys.forEach((key) => {
      const el = document.getElementById(`card-${key}`);
      if (el) {
        if (key === nextPrayerKey) {
          el.className = "prayer-card-active";
          if (el.children[0]) el.children[0].style.color = "var(--gold-soft)";
          if (el.children[1]) el.children[1].style.color = "var(--gold-soft)";
        } else {
          el.className = "";
          if (el.children[0]) el.children[0].style.color = "var(--text-muted)";
          if (el.children[1]) el.children[1].style.color = "";
        }
      }
    });
  }

  update();
  setInterval(update, 1000);
}
