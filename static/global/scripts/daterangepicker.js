//====================日期控件=========================
$(document).ready(function(){
    $("#defaultrange").daterangepicker({
        opens : "right", //日期选择框的弹出位置
        format: "YYYY-MM-DD",
        separator: " - ",
        startDate: moment().startOf("month"),
        endDate: moment(),
        minDate: "2012-01-01",
        maxDate: "2020-12-31",
        ranges : {
            //"最近1小时": [moment().subtract("hours",1), moment()],
            "今日": [moment().startOf("day"), moment().startOf("day")],
            "昨日": [moment().subtract("days", 1).startOf("day"), moment().subtract("days", 1).endOf("day")],
            "最近7日": [moment().subtract("days", 6), moment()],
            "最近30日": [moment().subtract("days", 29), moment()]
        },


        locale: {
            applyLabel: "确定",
            cancelLabel: "清除",
            fromLabel: "开始日期",
            toLabel: "截止日期",
            customRangeLabel: "自定义",
            daysOfWeek: ["日", "一", "二", "三", "四", "五", "六"],
            monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
            firstDay: 1
        }
    });
    $("#defaultrange").on("apply.daterangepicker",function(ev,picker){
        $("#defaultrange input").val(picker.startDate.format("YYYY/MM/DD") + " - " + picker.endDate.format("YYYY/MM/DD"));
        $("#start").val(picker.startDate.format("YYYY-MM-DD"));
        $("#end").val(picker.endDate.format("YYYY-MM-DD"));
    });
    $("#defaultrange").on("cancel.daterangepicker",function(ev,picker){
        $(this).find('input').val('');
    });
});
