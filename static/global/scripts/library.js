/*
*	Copyright VeryIDE,2007-2010
*	http://www.veryide.com/
*	
*	$Id: veryide.library.js,v2 22:12 2009-4-13 leilei $
*/

if(typeof VeryIDE!='object'){
	var VeryIDE={script:[]}
}

//object event
function addObjectEvent(ele,evt,func){
	var oldonevent = ele['on'+evt];
	if (typeof ele['on'+evt] != 'function') {
		ele['on'+evt] = func;
	} else {
		ele['on'+evt] = function(event) {
			oldonevent(event);
			func(event);
		}
	}
}

//key event
function addKeyEvent(key,func){
	if(!VeryIDE.tmpKey){
		VeryIDE.tmpKey=[];
	}
	
	VeryIDE.tmpKey["k"+key]=func;
}

//注册按键事件
addKeyEvent.Listener=function(e,test){
	var event=e||window.event;
	if(VeryIDE.tmpKey["k"+event.keyCode]){
		VeryIDE.tmpKey["k"+event.keyCode](event);
	}
	if(test){
		alert(event.keyCode);
	}
}

/*
	获取对象
	o	对象ID
	s	[可选]子对象标签名
*/
function getObject(o,s){

	if(typeof(o)!="object"){
		var o=document.getElementById(o);
	}
	
	if(s){
		var a=new Array();
		var c=o.childNodes;
		for(var i=0;i<c.length;i++){
			var n=c[i];
			if(!!n.tagName && n.tagName.toLowerCase()==s){
				a.push(n);
			}
		}
		return a
	}
	
	return o;
}

//删除对象
function removes(obj){
	var obj=getObject(obj);
	if(obj){
		obj.parentNode.removeChild(obj);
		return true;
	}else{
		return false;
	}
}

/*state*/
VeryIDE.script["library"]=true;