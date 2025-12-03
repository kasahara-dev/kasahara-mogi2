$(function () {
    $("#datePicker").datepicker({
        altField: "#datePicker",
        altFormat: "yy/mm/dd",
        dateFormat: "yy/mm/dd",
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        showMonthAfterYear: true,
        defaultDate: new Date(setYear, parseInt(setMonth), parseInt(setDay)),
        hideIfNoPrevNext: true,
        setDate: setYear + "-" + setMonth + "-" + setDay,
        onClose: function (dateText, inst) {
            var dates = dateText.split("/");
            setYear = dates[0];
            setMonth = dates[1];
            setDay = dates[2];
            if (setYear && setMonth && setDay !== null) {
                window.location.href =
                    "/admin/attendance/list/?year=" +
                    setYear +
                    "&month=" +
                    parseInt(setMonth) +
                    "&day=" +
                    parseInt(setDay);
            }
        },
    });
});
