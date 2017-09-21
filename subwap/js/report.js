var o = new Object();
o.hid = '';
o.name = '';
o.time = '';
o.sex = '';
o.phone = '';
o.note = '';
o.notice='';
o.is_only_sub = '';
o.visit_way = '';
var phone='';
var phone2='';
var hidden='';
var a='';
$(document).ready(function() {
	$.get('/api/config/index',function(data) {
        $('.report-attention-text').html(data.data.report_words);
    });
	$('#appDateTime').val(getNowFormatDate());
	if(GetQueryString('hid')!=null) {
		o.hid = GetQueryString('hid');
	}
	$('#plottitle').html(GetQueryString('title'));
	$.get('/api/plot/getPhones?hid='+o.hid,function(data) {
        a=data.data;
        for (var i = 0; i < a.length; i++) {
          $('.report-contacter').append('<option value="'+a[i].key+'">'+a[i].value+'</option>');
        }
    });
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
	o.notice=$('.report-contacter').val();
	if(o.time.trim() == '' || o.name.trim()==''||o.phone.trim()=='') {
		alert('请正确填写信息');
	}else
		$.post('/api/plot/addSub',o,function(data) {
			if(data.status == 'success') {
				alert('报备成功，项目负责人将会收到您的报备信息，客户到访时请主动出示客户码。');
				location.href = 'detail.html?id='+o.hid;
			}
			else
				alert(data.msg);
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

//替换版
// var phone='';
// var phone2='';
// var phone3='';
// var hidden='';
// $('.phone-on-off').click(function(){
// 	if ($('.phone-on-off').is('.off')) {
// 		$('.phone-on-off').removeClass('off');
// 		$('.phone-on-off').attr('src','./img/on.png');
// 		phone=$('#phone0').val();
// 		for (var i = 3; i < phone.length; i++) {
// 			phone1=phone.replace(phone[i],"*");
// 		}
// 		$('#phone0').val(phone1);
// 	} else {
// 		$('.phone-on-off').addClass('off');
// 		$('.phone-on-off').attr('src','./img/off.png');
// 		$('#phone0').val(phone);
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

// $('#phone1').keydown(function(){
// 	if($('#phone1').val().length==3) {
// 		$('#phone3').focus();
// 	}
// });
// $('#phone3').keydown(function(){
// 	if($('#phone3').val().length==0) {
// 		$('#phone1').focus();
// 	}
// });


