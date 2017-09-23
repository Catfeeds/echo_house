var tags='';
$(document).ready(function() {
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
        for(var i=0;i<tags[3].list[0].childAreas.length;i++){
          $('#area2').append('<option value="'+tags[3].list[0].childAreas[i].id+'">'+tags[3].list[0].childAreas[i].name+'</option>');
        }
        // console.log(tags[4].list);
        for(var i in tags[4].list){
          $('#mode').append('<li><input class="box" name="dllx" required type="radio" value="'+i+'"><div class="box-text">'+tags[4].list[i]+'</div></li>');
        }
    }); 
});
 

function submitBtn()  
{  
    $( '#form' ).validate({
      submitHandler:function() {
          
        if($('#img-url').val()=='') {
          alert('请上传封面图');
          return false;
        }
        var wylx = new Array;
        var zxzt = new Array;
        $(".wylx[type='checkbox']:checkbox:checked").each(function(){
          wylx.push($(this).val());
         //由于复选框一般选中的是多个,所以可以循环输出 
          // alert($(this).val()); 
        });
        $(".zxzt[type='checkbox']:checkbox:checked").each(function(){
          zxzt.push($(this).val());
         //由于复选框一般选中的是多个,所以可以循环输出 
          // alert($(this).val()); 
        });
        // console.log(wylx);debugger;
        $.post('/api/plot/addPlot',
          {
            'title':$('input[name="title"]').val(),
            'area':$('select[name="area"]').val(),
            'street':$('select[name="street"]').val(),
            'address':$('input[name="address"]').val(),
            'price':$('input[name="price"]').val(),
            'unit':$('select[name="unit"]').val(),
            'hxjs':$('textarea[name="hxjs"]').val(),
            'sfprice':$('select[name="sfprice"]').val(),
            'dllx':$('input[name="dllx"]').val(),
            // 'market_name':$('input[name="market_name"]').val(),
            // 'market_phone':$('input[name="market_phone"]').val(),
            'yjfa':$('textarea[name="yjfa"]').val(),
            'jy_rule':$('textarea[name="jy_rule"]').val(),
            'dk_rule':$('textarea[name="dk_rule"]').val(),
            'peripheral':$('textarea[name="peripheral"]').val(),
            'image':$('#img-url').val(),
            'wylx':wylx,
            'zxzt':zxzt,
          },function(data){
            if(data.status=='success'){
              alert(data.msg);
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
          $(obj).val('');
          $(obj).focus();
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

      for (var j = 0; j < arealist[i].childAreas.length; j++) {
      $('#area2').append('<option value="'+arealist[i].childAreas[j].id+'">'+arealist[i].childAreas[j].name+'</option>');     
      }
      break;
    }
  }
}