// window.addEventListener("DOMContentLoaded", function () {
//     // input要素を取得
//     let input_name = document.getElementById("list_select_month");

//     // イベントリスナーでイベント「change」を登録
//     input_name.addEventListener("change", function () {
//         console.log("Change action");
//         console.log(this.value);
//         window.location.href = "パス名"; // 通常の遷移
//     });

//     // イベントリスナーでイベント「input」を登録
//     input_name.addEventListener("input", function () {
//         console.log("Input action");
//         console.log(this.value);
//     });
// });
$(function () {
    $("#datepicker")
        .datepicker({
            dateFormat: "yy-mm",
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            showMonthAfterYear: true, // 年月を並び替え
            // minDate: new Date(),
            // maxDate: "+1y",
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
                var month = $(
                    "#ui-datepicker-div .ui-datepicker-month :selected"
                ).val();
                var year = $(
                    "#ui-datepicker-div .ui-datepicker-year :selected"
                ).val();
                if (year && month !== null) {
                    $(this).val(
                        year + "/" + ("0" + (parseInt(month) + 1)).slice(-2)
                    ); // 選択された年月をテキストボックスに反映
                }
            },
        })
        .focus(function () {
            $(".ui-datepicker-calendar").hide();
        });
});
// $("#datepicker").datepicker();
