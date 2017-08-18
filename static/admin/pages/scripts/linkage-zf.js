/**
 * Created by fanqi on 2016/8/18.
 *  实现二手房/租房 房源类型联动
 *  传入父级类型的值，所有的子集
 *  @param children JSON 子集集合
 *  子集:{
 *      id:(子集ID) int,
 *      parent_value:(对应父级值) int
 *  }
 *  列：
 *  $('#ResoldZfExt_category').linkage_zf({
 *      {
 *      _id:id1,
 *       _parent_value:1
 *      },
 *     {
 *     _id:id2,
 *     _parent_value:2
 *      },
 *  });
 */
(function ($) {
    $.fn.linkage_zf = function (options) {
        //设置默认值
        var defaults = {};
        var opts = $.extend(defaults, options);
        var $_this = $(this);
        function change($_this, opts) {
            var val = $_this.find('.checked').find('.radio-inline').val()||$_this.val();
            if(val==1 || val=='1')
                $('.wuye_fee').css('display','none');   
            else
                $('.wuye_fee').css('display','block');   
            for (var i in opts) {
                var _parent_value = opts[i]._parent_value;
                var _id = opts[i]._id;
                _id = _id.indexOf("#") == 0 ? _id : "#" + _id;
                if ($.isArray(_parent_value)) {
                    for (var value_id in _parent_value) {
                        if (_parent_value[value_id] == val) {
                            $(_id).show();
                            $(_id).find('.checked').find('input').removeAttr("disabled");
                            $(_id).find('select').removeAttr("disabled");
                            break;
                        } else {
                            $(_id).hide();
                            $(_id).find('.checked').find('input').attr("disabled","disabled");
                            $(_id).find('select').attr("disabled","disabled");
                        }
                    }
                } else {
                    if (_parent_value == val) {
                        $(_id).show();
                        $(_id).find('.checked').find('input').removeAttr("disabled");
                        $(_id).find('Select').removeAttr("disabled");
                    } else {
                        $(_id).hide();
                        $(_id).find('.checked').find('input').attr("disabled","disabled");
                        $(_id).find('select').attr("disabled","disabled");
                    }
                }
            }
        }

        change($_this, opts);
        //绑定值变化事件
        $_this.bind("change", function () {
            change($_this, opts);
        });

    };
})(jQuery);
