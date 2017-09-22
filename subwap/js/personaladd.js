var tags='';
$(document).ready(function() {
     //validata
    $('#form').validate();
    $.get('/api/tag/publishtags',function(data) {
        tags=data.data;
        for(var i=0;i<tags[0].length;i++){
          $('#wylx').append('<li><input class="box" type="checkbox" name="住宅"><div class="box-text">住宅</div></li>');
        }
        for(var i=0;i<tags[1].length;i++){
          $('#zxzt').append('<li><input class="box" type="checkbox" name="住宅"><div class="box-text">住宅</div></li>');
        }
        for(var i=0;i<tags[2].length;i++){
          $('#area1').append('<option value="volvo">Volvo</option>');
        }
        for(var i=0;i<tags[3].length;i++){
          $('#mode').append('<li><input class="box" type="radio" name="住宅"><div class="box-text">住宅</div></li>');
        }
    }); 
});
function sub(){
  
}