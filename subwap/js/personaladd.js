var tags='';
var delimgindex='';
var uid = '';
$(document).ready(function() {
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
      
     //validata
    // $('#form').validate();
    $.get('/api/tag/publishtags',function(data) {
        tags=data.data;
        for(var i=0;i<tags[0].list.length;i++){
          $('#wylx').append('<li><input class="box wylx" required name="wylx[]" type="checkbox" value="'+tags[0].list[i].id+'"><div class="box-text">'+tags[0].list[i].name+'</div></li>');
        }
        for(var i=0;i<tags[1].list.length;i++){
          $('#zxzt').append('<li><input class="box zxzt" required name="zxzt[]" type="checkbox" value="'+tags[1].list[i].id+'"><div class="box-text">'+tags[1].list[i].name+'</div></li>');
        }
        for(var i=0;i<tags[2].list.length;i++){
          $('#leastpay').append('<option value="'+tags[2].list[i].id+'">'+tags[2].list[i].name+'</option>');
        }
        for(var i=0;i<tags[3].list.length;i++){
          $('#area1').append('<option value="'+tags[3].list[i].id+'">'+tags[3].list[i].name+'</option>');
        }
        $('#area2').append('<option value="0">请选择</option>');
        for(var i=0;i<tags[3].list[0].childAreas.length;i++){
          $('#area2').append('<option value="'+tags[3].list[0].childAreas[i].id+'">'+tags[3].list[0].childAreas[i].name+'</option>');
        }
        // console.log(tags[4].list);
        for(var i in tags[4].list){
          $('#mode').append('<li><input class="box" name="dllx" required type="radio" value="'+i+'"><div class="box-text">'+tags[4].list[i]+'</div></li>');
        }
    }); 
    QFH5.getUserInfo(function(state,data){
      if(state==1){
        uid = data.uid;
      }
    })
});
 

function submitBtn()  
{  
    $( '#form' ).validate({
      submitHandler:function() {

         $.showLoading();
        var wylx = new Array;
        var zxzt = new Array;
        var imgs = new Array;
        $(".wylx[type='checkbox']:checkbox:checked").each(function(){
          wylx.push($(this).val());
         //由于复选框一般选中的是多个,所以可以循环输出 
          // alert($(this).val()); 
        });
        $(".img-key").each(function(){
          imgs.push($(this).html());
         //由于复选框一般选中的是多个,所以可以循环输出 
          // alert($(this).val()); 
        });
        if(imgs.length<1) {
          alert('请上传封面图');
          return false;
        }
        $(".zxzt[type='checkbox']:checkbox:checked").each(function(){
          zxzt.push($(this).val());
         //由于复选框一般选中的是多个,所以可以循环输出 
          // alert($(this).val()); 
        });
        // console.log(wylx);debugger;
        $.post('/api/plot/addPlot',
          {
            'pname':$('input[name="pname"]').val(),
            'pphone':$('input[name="pphone"]').val(),
            'pcompany':$('input[name="pcompany"]').val(),
            'title':$('input[name="title"]').val(),
            'city':$('select[name="area"]').val(),
            'area':$('select[name="street"]').val(),
            'street':$('select[name="town"]').val(),
            'address':$('input[name="address"]').val(),
            'price':$('input[name="price"]').val(),
            'unit':$('select[name="unit"]').val(),
            'hxjs':$('textarea[name="hxjs"]').val(),
            'sfprice':$('select[name="sfprice"]').val(),
            'dllx':$('input[name="dllx"]').val(),
            'fm':$('input[name="fm"]').val(),
            // 'market_name':$('input[name="market_name"]').val(),
            // 'market_phone':$('input[name="market_phone"]').val(),
            'yjfa':$('textarea[name="yjfa"]').val(),
            'jy_rule':$('textarea[name="jy_rule"]').val(),
            'dk_rule':$('textarea[name="dk_rule"]').val(),
            'peripheral':$('textarea[name="peripheral"]').val(),
            'image[]':imgs,
            'qf_uid':uid,
            'wylx':wylx,
            'zxzt':zxzt,
          },function(data){
            if(data.status=='success'){
              alert('您好，您的房源信息已提交。');
              // location.href = 'duijieren.html?hid='+data.data;
              location.href = 'personallist.html';
            } else {
              alert(data.msg);
            }
          });
  // {$('#aaa').data('name'):$('#aaa').val('name')}
      },
      errorPlacement: function(error, element) {  
          error.appendTo(element.parent());  
      }
    });   
}  
function sub(){
}

function checkName(obj) {
  var name = $(obj).val();
  if(name!='') {
      $.get('/api/plot/checkName?name='+name,function(data){
        if(data.status=='error') {
          alert(data.msg);
          location.href = 'detail.html?id='+data.data;
        }
      });
  } 
}
function checkPhone(obj) {
  var name = $(obj).val();
  if(name!='') {
      $.get('/api/plot/checkCanSub?phone='+name,function(data){
        if(data.status=='error') {
          alert(data.msg);
          location.href = 'duijierennew.html';
          // $(obj).val('');
          // $(obj).focus();
        }
      });
  } 
}
//二级下拉框
function setStreets(){
  $('#area2').empty();
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
//删除图片
function deleteimg(obj) {
  delimgindex=$(obj).attr('class');
  $('#'+delimgindex).remove();
  $(obj).closest('tr').remove();
}
function setFm(obj) {
    $('.is_cover').remove();
    var dataid = obj.data('id');
    obj.append('<div class="is_cover"></div>');
    $('.fm').val($('#'+dataid).html());
}