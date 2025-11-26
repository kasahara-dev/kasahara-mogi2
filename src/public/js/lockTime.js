// 24時台は24:00のみ
var selectHour = document.getElementById("attendance_end_hour");
var targetMinute = document.getElementById("attendance_end_minute");
if (selectHour.value == "24") {
    targetMinute.disabled = true;
} else {
    targetMinute.disabled = false;
}
selectHour.addEventListener("change", (e) => {
    if (e.target.value == "24") {
        targetMinute.options[0].selected = true;
        targetMinute.disabled = true;
    } else {
        targetMinute.disabled = false;
    }
});
for (let i = 1; i <= restsCount; i++) {
    if (document.getElementById("rest_end_hour_" + i).value == "24") {
        document.getElementById("rest_end_minute_" + i).disabled = true;
    } else {
        document.getElementById("rest_end_minute_" + i).disabled = false;
    }
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
    // --選択で他も--へ変化させる
    document
        .getElementById("rest_start_hour_" + i)
        .addEventListener("click", (e) => {
            if (e.target.value == "") {
                document.getElementById(
                    "rest_start_minute_" + i
                ).options[0].selected = true;
                document.getElementById(
                    "rest_end_hour_" + i
                ).options[0].selected = true;
                document.getElementById(
                    "rest_end_minute_" + i
                ).options[0].selected = true;
            }
        });
    document
        .getElementById("rest_start_minute_" + i)
        .addEventListener("click", (e) => {
            if (e.target.value == "") {
                document.getElementById(
                    "rest_start_hour_" + i
                ).options[0].selected = true;
                document.getElementById(
                    "rest_end_hour_" + i
                ).options[0].selected = true;
                document.getElementById(
                    "rest_end_minute_" + i
                ).options[0].selected = true;
            }
        });
    document
        .getElementById("rest_end_hour_" + i)
        .addEventListener("click", (e) => {
            if (e.target.value == "") {
                document.getElementById(
                    "rest_end_minute_" + i
                ).options[0].selected = true;
                document.getElementById(
                    "rest_start_hour_" + i
                ).options[0].selected = true;
                document.getElementById(
                    "rest_start_minute_" + i
                ).options[0].selected = true;
            }
        });
    document
        .getElementById("rest_end_minute_" + i)
        .addEventListener("click", (e) => {
            if (e.target.value == "") {
                document.getElementById(
                    "rest_end_hour_" + i
                ).options[0].selected = true;
                document.getElementById(
                    "rest_start_hour_" + i
                ).options[0].selected = true;
                document.getElementById(
                    "rest_start_minute_" + i
                ).options[0].selected = true;
            }
        });
}
