var selectHour = document.getElementById("attendance_end_hour");
selectHour.addEventListener("change", (e) => {
    var targetMinute = document.getElementById("attendance_end_minute");
    if (e.target.value == "24") {
        targetMinute.options[0].selected = true;
        targetMinute.disabled = true;
    } else {
        targetMinute.disabled = false;
    }
});
