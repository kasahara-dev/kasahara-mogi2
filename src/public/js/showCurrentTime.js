function showCurrentTime() {
    const now = new Date();
    const hour = String(now.getHours()).padStart(2, "0");
    const minute = String(now.getMinutes()).padStart(2, "0");
    const time = hour + ":" + minute;
    const year = String(now.getFullYear());
    const month = String(now.getMonth() + 1);
    const date = String(now.getDate());
    const weekDay = ["日", "月", "火", "水", "木", "金", "土"];
    const day = weekDay[now.getDay()];
    const formatDate = year + "年" + month + "月" + date + "日(" + day + ")";
    document.getElementById("date").textContent = formatDate;
    document.getElementById("time").textContent = time;
}
setInterval(showCurrentTime, 100);
