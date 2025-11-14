function showCurrentTime() {
    const now = new Date();
    const hour = String(now.getHours()).padStart(2, "0");
    const minute = String(now.getMinutes()).padStart(2, "0");
    const time = hour + ":" + minute;
    document.getElementById("time").textContent = time;
}
setInterval(showCurrentTime, 1000);
