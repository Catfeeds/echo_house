var o = new Object();
o.hid = '';
o.name = '';
o.time = '';
o.sex = '';
o.phone = '';
o.note = '';
o.is_only_sub = '';
o.visit_way = '';
var phone='';
var phone2='';
var hidden='';
$(document).ready(function() {
	$.get('/api/config/index',function(data) {
        $('.report-attention-text').html(data.data.report_words);
    });
	$('#appDateTime').val(getNowFormatDate());
	if(GetQueryString('hid')!=null) {
		o.hid = GetQueryString('hid');
	}
	$('#plottitle').html(GetQueryString('title'));
});
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}

function getNowFormatDate() {
    var date = new Date();
    var seperator1 = "-";
    var seperator2 = ":";
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }
    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
            + " " + date.getHours() + seperator2 + date.getMinutes()
            + seperator2 + date.getSeconds();
    return currentdate;
}

function setSexB() {
	$('.sex-icon').css('background-color','');
	$('.sex-boy').css('background-color','#00b7ee');
	o.sex = '1';
}
function setSexG() {
	$('.sex-icon').css('background-color','');
	$('.sex-girl').css('background-color','#00b7ee');
	o.sex = '2';
}
function setSexC() {
	$('.visit-icon').css('background-color','');
	$('.visit-boy').css('background-color','#00b7ee');
	o.visit_way = '1';
}
function setSexD() {
	$('.visit-icon').css('background-color','');
	$('.visit-girl').css('background-color','#00b7ee');
	o.visit_way = '2';
}

function sub() {
	if ($('.phone-on-off').is('.off')) {
		phonenumber=$('#phone0').val();
	} else {
		phonenumber=$('#phone1').val()+'****'+$('#phone3').val();
	}
	o.time = $('#appDateTime').val();
	o.phone = phonenumber;
	o.name = $('#name').val();
	o.note=$('.report-write').val();
	if(o.time.trim() == '' || o.name.trim()==''||o.phone.trim()=='') {
		alert('请正确填写信息');
	}else
		$.post('/api/plot/addSub',o,function(data) {
			alert('保存成功');
			location.href = 'detail.html?id='+o.hid;
		});
}

$('.baobei-on-off').click(function(){
	if ($('.baobei-on-off').is('.off')) {
		$('.baobei-on-off').removeClass('off');
		$('.baobei-on-off').attr('src','./img/on.png');
		o.is_only_sub=1;
	} else {
		$('.baobei-on-off').addClass('off');
		$('.baobei-on-off').attr('src','./img/off.png');
		o.is_only_sub=0;
	}
});

// $('.phone-on-off').click(function(){
// 	if ($('.phone-on-off').is('.off')) {
// 		$('.phone-on-off').removeClass('off');
// 		$('.phone-on-off').attr('src','./img/on.png');
// 		phone=$('#phone').val();
// 		if(phone.length>=11) {
// 			phone2=phone.substr(3,4);
// 			hidden=phone.replace(phone2,"****");
// 			$('#phone').val(hidden);
// 		}	
// 	} else {
// 		$('.phone-on-off').addClass('off');
// 		$('.phone-on-off').attr('src','./img/off.png');
// 		$('#phone').val(phone);
// 	}
// });
// $('#phone').blur(function(){
// 	phone=$('#phone').val();
// 	if(phone.length>3 && !$('.phone-on-off').is('.off')) {
// 		phone2=phone.substr(3,4);
// 		hidden=phone.replace(phone2,"****");
// 		$('#phone').val(hidden);
// 	}
// });



//2框版
$('.phone-on-off').click(function(){
	if ($('.phone-on-off').is('.off')) {
		$('#phone0').css('display','none');
		$('.phone-on').css('display','block');	
		$('.phone-on-off').removeClass('off');
		$('.phone-on-off').attr('src','./img/on.png');
	} else {
		$('.phone-on').css('display','none');
		$('#phone0').css('display','block');
		$('.phone-on-off').addClass('off');
		$('.phone-on-off').attr('src','./img/off.png');
	}
});

$('#phone1').keyup(function(){
	if($('#phone1').val().length==3) {
		$('#phone3').focus();
	}
});
$('#phone3').keyup(function(){
	if($('#phone3').val().length==0) {
		$('#phone1').focus();
	}
});


