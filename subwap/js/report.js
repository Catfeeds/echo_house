var o = new Object();
o.hid = '';
o.name = '';
o.time = '';
o.sex = '';
o.phone = '';
o.note = '';
o.is_only_sub = '';
o.visit_way = '';
$(document).ready(function() {
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
	$('.sex-boy').css('background-color','#00b8ee');
	o.sex = '1';
}
function setSexG() {
	$('.sex-icon').css('background-color','');
	$('.sex-girl').css('background-color','#00b8ee');
	o.sex = '2';
}
function setSexC() {
	$('.visit-icon').css('background-color','');
	$('.visit-boy').css('background-color','#00b8ee');
	o.visit_way = '1';
}
function setSexD() {
	$('.visit-icon').css('background-color','');
	$('.visit-girl').css('background-color','#00b8ee');
	o.visit_way = '2';
}

function sub() {
	o.time = $('#appDateTime').val();
	o.phone = $('#phone').val();
	o.name = $('#name').val();
	if(o.time.trim() == '' || o.name.trim()==''||o.phone.trim()=='') {
		alert('请正确填写信息');
	}else
		$.post('/api/plot/addSub',o,function(data) {
			alert('保存成功');
			location.href = 'detail.html?id='+o.hid;
		});
}

$('.on-off').click(function(){
	if ($('.on-off').is('.off')) {
		$('.on-off').removeClass('off');
		$('.on-off').attr('src','./img/on.png');
		o.is_only_sub=1;
	} else {
		$('.on-off').addClass('off');
		$('.on-off').attr('src','./img/off.png');
		o.is_only_sub=0;
	}
});