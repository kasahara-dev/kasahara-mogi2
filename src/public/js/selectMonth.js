// window.addEventListener("DOMContentLoaded", function () {
// input要素を取得
// let input_name = document.getElementById("monthPicker");
//     // イベントリスナーでイベント「change」を登録
// input_name.addEventListener("change", function () {
//     var year = this.value.substr(0, str.indexOf("/"));
//     var month = this.value.substr(0, str.indexOf("/") + 1);
//     window.location.href =
//         "/attendance/list/?year=" + year + "&month=" + month; // 通常の遷移
// });

//     // イベントリスナーでイベント「input」を登録
//     input_name.addEventListener("input", function () {
//         console.log("Input action");
//         console.log(this.value);
//     });
// });
$(function () {
    $("#monthPicker")
        .datepicker({
            altField: "#monthPicker",
            altFormat: "yy/mm",
            dateFormat: "yy/mm",
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            showMonthAfterYear: true, // 年月を並び替え
            defaultDate: new Date(setYear, parseInt(setMonth) - 1),
            // minDate: new Date(),
            // maxDate: "+100y",
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
            onSelect: function (dateText, inst) {
                var dates = dateText.split("/");
                setMonth = dates[0];
                setYear = dates[1];
                $(this).val("2021/11");
                // if (setYear && setMonth !== null) {
                //     $(this).val(
                //         setYear +
                //             "/" +
                //             ("0" + (parseInt(setMonth) + 1)).slice(-2)
                //     ); // 選択された年月をテキストボックスに反映
                // }
            },
            onClose: function (dateText, inst) {
                setMonth = $(
                    "#ui-datepicker-div .ui-datepicker-month :selected"
                ).val();
                setYear = $(
                    "#ui-datepicker-div .ui-datepicker-year :selected"
                ).val();
                if (setYear && setMonth !== null) {
                    // $(this).val(
                    //     setYear +
                    //         "/" +
                    //         ("0" + (parseInt(setMonth) + 1)).slice(-2)
                    // ); // 選択された年月をテキストボックスに反映
                    window.location.href =
                        "/attendance/list/?year=" +
                        setYear +
                        "&month=" +
                        (parseInt(setMonth) + 1); // 通常の遷移
                }
            },
        })
        .focus(function () {
            $(".ui-datepicker-calendar").hide();
            $(".ui-datepicker-next").hide();
            $(".ui-datepicker-prev").hide();
        });
    // $("#monthPicker").datepicker("setDate", "2021/12");
});
// $("#datepicker").datepicker();
