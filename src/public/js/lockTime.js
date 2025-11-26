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
for (var i = 1; i <= restsCount; i++) {
    // var i = 1;
    document
        .getElementById("rest_end_hour_" + i)
        .addEventListener("change", (e) => {
            if (e.target.value == "24") {
                document.getElementById(
                    "rest_end_minute_" + i
                ).options[1].selected = true;
                document.getElementById("rest_end_minute_" + i).disabled = true;
            } else {
                document.getElementById(
                    "rest_end_minute_" + i
                ).disabled = false;
            }
        });
    console.log("this line is" + i);
}
