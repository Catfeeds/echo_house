var hid='';
var url='';
var detail='';
$(document).ready(function() {
	var url = GetQueryString('url');
	//二维码
	$.get('/api/config/qr?url='+url,function(data) {
		$('.qrcode-img').attr('src',data.data);
	});
	//顶部图片
	if(GetQueryString('id')!=''&&GetQueryString('id')!=undefined) {
		hid = GetQueryString('id');
	}
	$.get('/api/plot/info?id='+hid, function(data) {
		detail=data.data;
		if (data.data.images.length>0) {
			url=detail.images[0].url;
			$('.qrcode-head-img').attr('src',url);
		}
		$('title').html(detail.title+'-诚邀分销');
		$('.qrcode-head-title').append(detail.title+'-'+detail.area+'-'+detail.street);
		//价格
		$('.qrcode-head-price').append(detail.price,detail.unit);
		//公司名
		$('.company-name').append(detail.zd_company.name);
		//标签
		if (detail.tags.length<1) {
            $('.head-price-tags').css('display','none');
        }
        for (var i = 0; i < detail.tags.length; i++) {
            if (i%3==1) {
                $('#showadd').css('display','none');
                $('.head-price-tags ul').append('<li class="color1">'+detail.tags[i]+'</li>'); 
            }else if(i%3==2){
                $('#showadd').css('display','none');
                $('.head-price-tags ul').append('<li class="color2">'+detail.tags[i]+'</li>'); 
            }else{
                $('.head-price-tags ul').append('<li class="color3">'+detail.tags[i]+'</li>');  
            }
        }
	});
});
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}