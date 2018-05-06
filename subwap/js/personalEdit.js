var tags = '';
var fmindex = 0;
var delimgindex = '';
var imageindex = 0;
var uid = '';
var id = GetQueryString('id');
var wylxList = [];//物业类型列表
var zxztList = [];//装修情况列表
var leastpayList = [];//首付金额列表
var modeList = [];//代理性质列表
var imgarr = [];//总的img
var imgarrBase64 = [];//base64img
var imgarrUrl = [];//编辑时的img
var wylxVal;//物业类型
var zxztVal;//装修情况
var leastpayVal;//首付金额
var modeVal;//代理性质

$(document).ready(function () {
    // 获取千帆uid
      $.get('/api/config/index',function(data) {
          if(data.data.is_user==false||data.data.is_user==0||data.data.is_user=="0") {
              alert('请认证后操作');
              location.href = 'register.html';
          }
          if(data.status=='success') {
            if(data.data.user.phone!=undefined) {
              $.get('/api/plot/checkCanSub?phone='+data.data.user.phone,function(data) {
                if(data.status=='error') {
                  alert(data.msg);
                  if(data.msg=='用户类型错误，只支持总代公司发布房源')
                    location.href = 'list.html';
                  else
                    location.href = 'duijierennew.html';
                }
              });
              $('#pname').val(data.data.user.name);
              $('#pphone').val(data.data.user.phone);
              $('#pcompany').val(data.data.companyname);
              $('#pname').attr('readonly','readonly');
              $('#pphone').attr('readonly','readonly');
              $('#pcompany').attr('readonly','readonly');
            }
          }
      });
    //
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
                wylxVal = d.values;
            }
        });
        for (var i = 0; i < tags[1].list.length; i++) {
            var zxztData = {};
            zxztData.value = tags[1].list[i].id;
            zxztData.title = tags[1].list[i].name;
            zxztList.push(zxztData);
        }
        $("#zxzt").select({
            title: "装修情况",
            multi: true,
            split: ',',
            closeText: '完成',
            items: zxztList,
            onChange: function (d) {
                zxztVal = d.values;
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
                leastpayVal = d.values;
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
                modeVal = d.values;
                // $.alert("你选择了"+d.values);
            }
        });

        //地区
        for(var i=0;i<tags[3].list.length;i++){
          $('#area1').append('<option value="'+tags[3].list[i].id+'">'+tags[3].list[i].name+'</option>');
        }
        $('#area2').append('<option value="0">请选择</option>');
        for(var i=0;i<tags[3].list[0].childAreas.length;i++){
          $('#area2').append('<option value="'+tags[3].list[0].childAreas[i].id+'">'+tags[3].list[0].childAreas[i].name+'</option>');
        }
        $('#area3').append('<option value="0">请选择</option>');
        // for(var i=0;i<tags[3].list[0].childAreas[0].childAreas.length;i++){
        //   $('#area3').append('<option value="'+tags[3].list[0].childAreas[0].childAreas[i].id+'">'+tags[3].list[0].childAreas[0].childAreas[i].name+'</option>');
        // }
    });

    //获取项目详情
    $.get('/api/plot/getPlotInfo?id=' + id, function(data){
        imgarrUrl = data.data.image;
        
        console.log(data.data)
    })
    QFH5.getUserInfo(function (state, data) {
        if (state == 1) {
            uid = data.uid;
        }
    })
});


//删除图片
function deleteimg(obj) {
    delimgindex = $(obj).attr('class');
    $('#' + delimgindex).remove();
    $(obj).closest('tr').remove();
}

function setFm(obj) {
    $('.is_cover').remove();
    $('.fm').attr('class', 'weui_uploader_file');
    // var dataid = obj.data('id');
    $(obj).append('<div class="is_cover"></div>');
    $(obj).attr('class', 'weui_uploader_file fm');
    // $('.fm').val($('#' + dataid).html());
}

//多图上传
function previewImage(file) {
    var MAXWIDTH = 100;
    var MAXHEIGHT = 200;

    for (var i = 0; i < file.files.length; i++) {
        if (file.files && file.files[i]) {
            var reader = new FileReader();
            reader.onload = function (evt) {
                console.log(imageindex)
                if(imageindex === 0){
                    $('#img').append('<li class="weui_uploader_file fm" onclick="setFm(this)" data-img="' + evt.target.result + '" style="background-image:url(' + evt.target.result + ')"><img class="imgarr imgindex' + imageindex + '" style="/* position: absolute; */height: 30px;width: 30px;margin-left: 50px;" onclick="deleteimg(this)" src="./img/deleteimg.png"><div class="is_cover"></div></li>');
                }else{
                    $('#img').append('<li class="weui_uploader_file" onclick="setFm(this)" data-img="' + evt.target.result + '" style="background-image:url(' + evt.target.result + ')"><img class="imgarr imgindex' + imageindex + '" style="/* position: absolute; */height: 30px;width: 30px;margin-left: 50px;" onclick="deleteimg(this)" src="./img/deleteimg.png"></li>');
                }
                imageindex++;
                if (imageindex > 8) {
                    $('.weui_uploader_input_wrp').css('display', 'none');
                }
            };
            reader.readAsDataURL(file.files[i]);
        }

    }

}

function deleteimg(obj) {
    delimgindex = $(obj).attr('class');
    $('#' + delimgindex).remove();
    $(obj).closest('li').remove();
    imageindex--;
    if (imageindex <= 8) {
        $('.weui_uploader_input_wrp').css('display', 'block');
    }
}

var $form = $("#form");
$form.form();
function clear_arr_trim(array) {  
    for(var i = 0 ;i<array.length;i++)  
    {  
        if(array[i] == "" || typeof(array[i]) == "undefined")  
        {  
            array.splice(i,1);  
            i= i-1;  
        }  
    }  
    return array;  
}  
$("#formSubmitBtn").on("click", function () {
    var imgarr = [];
    $form.validate(function (error) {
        if (error) {
            console.log(error)
        } else {
            // validate通过后处理这个 图片数组
            // imgarrBase64是新选择的图片数组 fmindex是封面下标
            // post的时候也用这两个参数 原先的fm和image都不要传
            
            if ($('.weui_uploader_file').length > 0) {
               
                for (var i = 0; i < $('.weui_uploader_file').length; i++) {
                    var tmpa = $('.weui_uploader_file')[i];
                    var tmpaFun = {};
                    tmpaFun.type = 'base64';
                    tmpaFun.url = $(tmpa).data('img');
                    imgarrBase64.push(tmpaFun);
                    if ($(tmpa).hasClass('fm')) {
                        fmindex = i;
                    }
                }
            
                
            }
            imgarr = imgarrUrl.concat(imgarrBase64);
            if (imgarr.length < 1) {
                alert('请上传图片');
                return false;
            }
            
            if ($('#area2').val() == 0 || $('#area3').val() == 0) {
                alert('请选择项目区域');
                return false;
            }
            // 删除空值
            imgarr = clear_arr_trim(imgarr);
            if(id){ //编辑
                var params = {
                    'price': $('#price').val(),
                    'unit': $('select[name="unit"]').val(),
                    'sfprice': leastpayVal,
                    'dllx': modeVal,
                    'yjfa': $('#yjfa').val(),
                    'jy_rule': $('#jy_rule').val(),
                    'dk_rule': $('#dk_rule').val(),
                    'peripheral': $('#peripheral').val(),
                    'imgarr': imgarr,
                    'fmindex':fmindex,
                    'qf_uid': uid,
                    'id':id
                };
            }else{ //新增
                var params = {
                    'pname': $('#pname').val(),
                    'pphone': $('#pphone').val(),
                    'pcompany': $('#pcompany').val(),
                    'title': $('#housename').val(),
                    'city': $('select[name="area"]').val(),
                    'area': $('select[name="street"]').val(),
                    'street': $('select[name="town"]').val(),
                    'address': $('#houseaddress').val(),
                    'wylx': wylxVal,
                    'zxzt': zxztVal,
                    'price': $('#price').val(),
                    'unit': $('select[name="unit"]').val(),
                    // 'hxjs':$('#hxjs').val(),
                    'sfprice': leastpayVal,
                    'dllx': modeVal,
                    // 'fm':$('#fm').val(),
                    // 'market_name':$('input[name="market_name"]').val(),
                    // 'market_phone':$('input[name="market_phone"]').val(),
                    'yjfa': $('#yjfa').val(),
                    'jy_rule': $('#jy_rule').val(),
                    'dk_rule': $('#dk_rule').val(),
                    'peripheral': $('#peripheral').val(),
                    'imgarr': imgarr,
                    'fmindex':fmindex,
                    'qf_uid': uid,
                };
            }
            
            console.log(params)
            // $.showLoading('正在发布中');
            // $.post('/api/plot/addPlotNew',params
            //   ,function(data){
            //     $.hideLoading();
            //     if(data.status=='success'){
            //         alert('您好，您的房源信息已提交。');
            //         location.href = 'personalSuccess.html';
            //     } else {
            //       alert(data.msg);
            //     }
            //   });
            // $.toptips('验证通过提交', 'ok');
        }
    });

});
//二级下拉框
function setStreets(){
  $('#area2').empty();
  $('#area3').empty();
  $('#area3').append('<option value="0">请选择</option>');
  var arealist = tags[3].list;
  for(var i = 0; i < arealist.length; i++){
    // console.log(tags[3][i]);
    if($('#area1').val()==arealist[i].id){
      $('#area2').append('<option value="0">请选择</option>');
      for (var j = 0; j < arealist[i].childAreas.length; j++) {
      $('#area2').append('<option value="'+arealist[i].childAreas[j].id+'">'+arealist[i].childAreas[j].name+'</option>');     
      }
      break;
    }
  }
}
function setTowns(){
  $('#area3').empty();
  var arealist = tags[3].list;
  for(var i = 0; i < arealist.length; i++){
    // console.log(tags[3][i]);
    if($('#area1').val()==arealist[i].id){

      for (var j = 0; j < arealist[i].childAreas.length; j++) {

        if($('#area2').val()==arealist[i].childAreas[j].id) {
          $('#area3').append('<option value="0">请选择</option>');
          for (var k = 0; k < arealist[i].childAreas[j].childAreas.length; k++) {
            $('#area3').append('<option value="'+arealist[i].childAreas[j].childAreas[k].id+'">'+arealist[i].childAreas[j].childAreas[k].name+'</option>');
          }
          
        }
           
      }
      break;
    }
  }
}
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}