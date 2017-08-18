
var tags = {};init_by_value();
    create_tags();
    $('.huxing-tags > div')
    .draggable(
        {
            helper: "clone" ,
            containment : '#singlePicyw1',
            start : function(event,ui) {
                var type = $(this).data('type');
                $(this).hide();
            },
            stop : function(event, ui) {
                var $helper = $(ui.helper).clone(true);
                var p_elem  = $('#singlePicyw1').offset();
                $('#singlePicyw1').append($helper);
                var relative_offset = cal_relative_offset(p_elem,ui.offset);
                var data = {
                    'left' : relative_offset.x,
                    'top' : relative_offset.y
                };
                data = $.extend({},data,$helper.data());
                $helper.css({
                    'left' : data.left,
                    'top' : data.top
                }).addClass('ui-helper-stop').draggable({
                    containment : '#singlePicyw1',
                    stop : function(event,ui) {
                        update_offset($(this),ui);
                    }
                });
                tags['tag-' + data['type']] = data;
                fill_input_data();
            }
        }
    );

    $('#singlePicyw1').on('click','.guanbi',function() {
        var $self = $(this).closest('div');
        var id = $self.data('type');
        var type = $self.data('type');
        show_tag(type);
        $self.remove();
        delete tags['tag-'+id];
        fill_input_data();
        return false;
    })

    function update_offset($ele,ui){
        var id = 'tag-' + $ele.data('type');
        var _offset = ui.position;
        tags[id] = {
            'left' : _offset.left,
            'top' : _offset.top,
            'type' : $ele.data('type')
        };
        fill_input_data();
    }
    function show_tag(type){
        $('.huxing-tags').find('[data-type="' + type + '"]').show();
    }
    function init_by_value(){
        var val = $('#huxing-tags-value').val();
        if(val){
            tags = $.parseJSON(val);
        }
    }

    //创建标签
    function create_tags(){
        for(var id in tags){
            var d = tags[id];
            var type = d['type'];
            var html = $('[data-type="' + type + '"]').clone().css({
                position : 'absolute',
                left : d['left'],
                top : d['top']
            }).show().attr('id',d['_id']).draggable({
                containment : '#singlePicyw1',
                stop : function(event,ui) {
                    update_offset($(this),ui);
                }
            });
            $('#singlePicyw1').append(html);
        }
    }

    function fill_input_data(){
        var data = JSON.stringify(tags)
        $('#huxing-tags-value').val(data);
    }
    function cal_relative_offset(p_elem, tag_elem){
        var x = 0;
        var y = 0;
        x = tag_elem.left - p_elem.left;
        y = tag_elem.top - p_elem.top

        return {
            x : x,
            y : y
        };
    }
