var tags = '';
var fmindex = 0;
var delimgindex = '';
var imageindex = 0;
var uid = '';
var wylxList = [];//物业类型
var zxztList = [];//装修情况
var leastpayList = [];//首付金额
var modeList = [];//代理性质
var imgarr = [];
$(document).ready(function () {
    // 获取千帆uid
    //   $.get('/api/config/index',function(data) {
    //       if(data.data.is_user==false||data.data.is_user==0||data.data.is_user=="0") {
    //           alert('请认证后操作');
    //           location.href = 'register.html';
    //       }
    //       if(data.status=='success') {
    //         if(data.data.user.phone!=undefined) {
    //           $.get('/api/plot/checkCanSub?phone='+data.data.user.phone,function(data) {
    //             if(data.status=='error') {
    //               alert(data.msg);
    //               if(data.msg=='用户类型错误，只支持总代公司发布房源')
    //                 location.href = 'list.html';
    //               else
    //                 location.href = 'duijierennew.html';
    //             }
    //           });
    //           $('#pname').val(data.data.user.name);
    //           $('#pphone').val(data.data.user.phone);
    //           $('#pcompany').val(data.data.companyname);
    //           $('#pname').attr('readonly','readonly');
    //           $('#pphone').attr('readonly','readonly');
    //           $('#pcompany').attr('readonly','readonly');
    //         }
    //       }
    //   });
    //
    //validata
    // $('#form').validate();
    $.get('/api/tag/publishtags', function (data) {
        tags = data.data;
        for (var i = 0; i < tags[0].list.length; i++) {
            var wylxData = {};
            wylxData.value = tags[0].list[i].id;
            wylxData.title = tags[0].list[i].name;
            wylxList.push(wylxData);
        }
        $("#wylx").select({
            title: "物业类型",
            multi: true,
            split: ',',
            closeText: '完成',
            items: wylxList,
            onChange: function (d) {
                // $.alert("你选择了"+d.values+d.titles);
            }
        });
        for (var i = 0; i < tags[1].list.length; i++) {
            var zxztData = {};
            zxztData.value = tags[1].list[i].id;
            zxztData.title = tags[1].list[i].name;
            zxztList.push(zxztData);
        }
        $("#zxzt").select({
            title: "物业类型",
            multi: true,
            split: ',',
            closeText: '完成',
            items: zxztList,
            onChange: function (d) {
                // $.alert("你选择了"+d.values+d.titles);
            }
        });
        for (var i = 0; i < tags[2].list.length; i++) {
            var leastpayData = {};
            leastpayData.value = tags[2].list[i].id;
            leastpayData.title = tags[2].list[i].name;
            leastpayList.push(leastpayData);
        }
        $("#leastpay").select({
            title: "首付金额",
            items: leastpayList,
            onChange: function (d) {
                // $.alert("你选择了"+d.values);
            }
        });
        for (var i in tags[4].list) {
            var modeData = {};
            modeData.value = i;
            modeData.title = tags[4].list[i];
            modeList.push(modeData);
        }
        $("#mode").select({
            title: "代理性质",
            items: modeList,
            onChange: function (d) {
                // $.alert("你选择了"+d.values);
            }
        });
    });
    QFH5.getUserInfo(function (state, data) {
        if (state == 1) {
            uid = data.uid;
        }
    })
});


function submitBtn() {
    $('#form').validate({
        submitHandler: function () {


            var wylx = new Array;
            var zxzt = new Array;
            var imgs = new Array;
            $(".wylx[type='checkbox']:checkbox:checked").each(function () {
                wylx.push($(this).val());
                //由于复选框一般选中的是多个,所以可以循环输出
                // alert($(this).val());
            });
            $(".img-key").each(function () {
                imgs.push($(this).html());
                //由于复选框一般选中的是多个,所以可以循环输出
                // alert($(this).val());
            });
            if (imgs.length < 1) {
                alert('请上传封面图');
                return false;
            }
            $(".zxzt[type='checkbox']:checkbox:checked").each(function () {
                zxzt.push($(this).val());
                //由于复选框一般选中的是多个,所以可以循环输出
                // alert($(this).val());
            });
            // console.log(wylx);debugger;
            $.post('/api/plot/addPlot',
                {
                    'pname': $('input[name="pname"]').val(),
                    'pphone': $('input[name="pphone"]').val(),
                    'pcompany': $('input[name="pcompany"]').val(),
                    'title': $('input[name="title"]').val(),
                    'city': $('select[name="area"]').val(),
                    'area': $('select[name="street"]').val(),
                    'street': $('select[name="town"]').val(),
                    'address': $('input[name="address"]').val(),
                    'price': $('input[name="price"]').val(),
                    'unit': $('select[name="unit"]').val(),
                    'hxjs': $('textarea[name="hxjs"]').val(),
                    'sfprice': $('select[name="sfprice"]').val(),
                    'dllx': $('input[name="dllx"]').val(),
                    'fm': $('input[name="fm"]').val(),
                    // 'market_name':$('input[name="market_name"]').val(),
                    // 'market_phone':$('input[name="market_phone"]').val(),
                    'yjfa': $('textarea[name="yjfa"]').val(),
                    'jy_rule': $('textarea[name="jy_rule"]').val(),
                    'dk_rule': $('textarea[name="dk_rule"]').val(),
                    'peripheral': $('textarea[name="peripheral"]').val(),
                    'image[]': imgs,
                    'qf_uid': uid,
                    'wylx': wylx,
                    'zxzt': zxzt,
                }, function (data) {
                    if (data.status == 'success') {
                        alert('您好，您的房源信息已提交。');
                        // location.href = 'duijieren.html?hid='+data.data;
                        location.href = 'personallist.html';
                    } else {
                        alert(data.msg);
                    }
                });
            // {$('#aaa').data('name'):$('#aaa').val('name')}
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.parent());
        }
    });
}

function sub() {
}

function checkName(obj) {
    var name = $(obj).val();
    if (name != '') {
        $.get('/api/plot/checkName?name=' + name, function (data) {
            if (data.status == 'error') {
                alert(data.msg);
                location.href = 'detail.html?id=' + data.data;
            }
        });
    }
}

function checkPhone(obj) {
    var name = $(obj).val();
    if (name != '') {
        $.get('/api/plot/checkCanSub?phone=' + name, function (data) {
            if (data.status == 'error') {
                alert(data.msg);
                location.href = 'duijierennew.html';
                // $(obj).val('');
                // $(obj).focus();
            }
        });
    }
}

//二级下拉框
function setStreets() {
    $('#area2').empty();
    var arealist = tags[3].list;
    for (var i = 0; i < arealist.length; i++) {
        // console.log(tags[3][i]);
        if ($('#area1').val() == arealist[i].id) {
            $('#area2').append('<option value="0">请选择</option>');
            for (var j = 0; j < arealist[i].childAreas.length; j++) {
                $('#area2').append('<option value="' + arealist[i].childAreas[j].id + '">' + arealist[i].childAreas[j].name + '</option>');
            }
            break;
        }
    }
}

function setTowns() {
    $('#area3').empty();
    var arealist = tags[3].list;
    for (var i = 0; i < arealist.length; i++) {
        // console.log(tags[3][i]);
        if ($('#area1').val() == arealist[i].id) {

            for (var j = 0; j < arealist[i].childAreas.length; j++) {

                if ($('#area2').val() == arealist[i].childAreas[j].id) {
                    $('#area3').append('<option value="0">请选择</option>');
                    for (var k = 0; k < arealist[i].childAreas[j].childAreas.length; k++) {
                        $('#area3').append('<option value="' + arealist[i].childAreas[j].childAreas[k].id + '">' + arealist[i].childAreas[j].childAreas[k].name + '</option>');
                    }

                }

            }
            break;
        }
    }
}

//删除图片
function deleteimg(obj) {
    delimgindex = $(obj).attr('class');
    $('#' + delimgindex).remove();
    $(obj).closest('tr').remove();
}

function setFm(obj) {
    $('.is_cover').remove();
    $('.fm').attr('class','weui_uploader_file');
    // var dataid = obj.data('id');
    $(obj).append('<div class="is_cover"></div>');
    $(obj).attr('class','weui_uploader_file fm');
    // $('.fm').val($('#' + dataid).html());
}


$("#ssx").cityPicker({
    title: "选择省市县"
});

//多图上传
function previewImage(file) {
    var MAXWIDTH = 100;
    var MAXHEIGHT = 200;
    for(var i=0;i<file.files.length;i++){

        if (file.files && file.files[i]) {
            var reader = new FileReader();
            reader.onload = function (evt) {

                $('#img').append('<li class="weui_uploader_file" onclick="setFm(this)" data-img="'+evt.target.result+'" style="background-image:url('+evt.target.result+')"><img class="imgarr imgindex'+imageindex+'" style="/* position: absolute; */height: 30px;width: 30px;/* right: 0px; *//* top: 0px; */margin-left: 50px;" onclick="deleteimg(this)" src="./img/deleteimg.png"></li>');
                imageindex++;

            };
            reader.readAsDataURL(file.files[i]);
        }

    }

}

function deleteimg(obj) {
  delimgindex=$(obj).attr('class');
  $('#'+delimgindex).remove();
  $(obj).closest('li').remove();
}
var $form = $("#form");
$form.form();
$("#formSubmitBtn").on("click", function(){
    $form.validate(function(error){
        if(error){

        }else{

            // validate通过后处理这个 图片数组
            // imgarr是图片数组 fmindex是封面下标
            // post的时候也用这两个参数 原先的fm和image都不要传
            if($('.weui_uploader_file').length>0) {
                for (var i = 0; i < $('.weui_uploader_file').length; i++) {
                    var tmpa = $('.weui_uploader_file')[i];
                    imgarr.push($(tmpa).data('img'));
                    if($(tmpa).hasClass('fm')) {
                        fmindex = i;
                    }
                }
            }
            // $.post('/api/plot/addPlot',
            //   {
            //     'pname':$('#pname').val(),
            //     'pphone':$('#pphone').val(),
            //     'pcompany':$('#pcompany').val(),
            //     'title':$('#housename').val(),
            //     'city':$('select[name="area"]').val(),
            //     'area':$('select[name="street"]').val(),
            //     'street':$('select[name="town"]').val(),
            //     'address':$('#houseaddress').val(),
            //     'price':$('#price').val(),
            //     'unit':$('select[name="unit"]').val(),
            //     'hxjs':$('#hxjs').val(),
            //     'sfprice':$('select[name="sfprice"]').val(),
            //     'dllx':$('#dllx').val(),
            //     'fm':$('#fm').val(),
            //     // 'market_name':$('input[name="market_name"]').val(),
            //     // 'market_phone':$('input[name="market_phone"]').val(),
            //     'yjfa':$('#yjfa').val(),
            //     'jy_rule':$('#jy_rule').val(),
            //     'dk_rule':$('#dk_rule').val(),
            //     'peripheral':$('#peripheral').val(),
            //     'image[]':imgs,
            //     'qf_uid':uid,
            //     'wylx':wylx,
            //     'zxzt':zxzt,
            //   },function(data){
            //     if(data.status=='success'){
            //       alert('您好，您的房源信息已提交。');
            //       // location.href = 'duijieren.html?hid='+data.data;
            //       location.href = 'personallist.html';
            //     } else {
            //       alert(data.msg);
            //     }
            //   });
            $.toptips('验证通过提交','ok');
        }
    });

});
