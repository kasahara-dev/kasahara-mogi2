$(function () {
    $("#monthPicker")
        .datepicker({
            altField: "#monthPicker",
            altFormat: "yy/mm",
            dateFormat: "yy/mm",
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            showMonthAfterYear: true,
            defaultDate: new Date(setYear, parseInt(setMonth) - 1),
            hideIfNoPrevNext: true,
            setDate: setYear + "-" + setMonth,
            currentText: "今月",
            closeText: "閉じる",
            hideIfNoPrevNext: true,
            monthNames: [
                "1月",
                "2月",
                "3月",
                "4月",
                "5月",
                "6月",
                "7月",
                "8月",
                "9月",
                "10月",
                "11月",
                "12月",
            ],
            monthNamesShort: [
                "1月",
                "2月",
                "3月",
                "4月",
                "5月",
                "6月",
                "7月",
                "8月",
                "9月",
                "10月",
                "11月",
                "12月",
            ],
            onClose: function (dateText, inst) {
                setMonth = $(
                    "#ui-datepicker-div .ui-datepicker-month :selected"
                ).val();
                setYear = $(
                    "#ui-datepicker-div .ui-datepicker-year :selected"
                ).val();
                if (setYear && setMonth !== null) {
                    window.location.href =
                        "/attendance/list/?year=" +
                        setYear +
                        "&month=" +
                        (parseInt(setMonth) + 1);
                }
            },
        })
        .focus(function () {
            $(".ui-datepicker-calendar").hide();
            $(".ui-datepicker-next").hide();
            $(".ui-datepicker-prev").hide();
        });
});
