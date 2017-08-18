/*
*	Copyright VeryIDE,2007-2010
*	http://www.veryide.com/
*	
*	$Id: veryide.core.js v1.5 20:41 2009-10-2 leilei $
*/

var VeryIDE={

	/*config*/
	site	:	"VeryIDE",
	domain	:	location.host,

	/*version*/
	version			: 2.2,
	versionBuild	: 20090630,

	/*event*/
	mouseX:0,
	mouseY:0,
	
	/*inti*/
	root:"",
	product:"",
	inti:null,
	layer:100,
	temp:"",
	
	/*script*/
	script:[],
	
	/*debug*/
	debug:false,
	
	/*random*/
	rnd:Math.random(),
	getRnd:function(){
		return Math.random();
	},
	
	/*analytics*/
	isIE			:false,
	isOpera		:false,
	isSafari		:false,
	isFirefox		:false,
	isMaxthon	:false,
	appName:"",
	appVersion:"",
	appLang:"",

	/*load*/
	start:function(){

		/*browse*/
		switch (navigator.appName){
			case "Microsoft Internet Explorer":{
				VeryIDE.appName = "ie";
				var reg = /^.+MSIE (\d+\.\d+);.+$/;
				VeryIDE.isIE=true;
				
				if(navigator.userAgent.indexOf ("MAXTHON") != -1){
					VeryIDE.isMaxthon=true;
					var regMax = /^.+MAXTHON ([\d\.]+).{0,}$/;
					VeryIDE.maxVersion=navigator.userAgent.replace (regMax, "$1");
				}
				
				break;
			}default:{
				if (navigator.userAgent.indexOf ("Safari") != -1){
					VeryIDE.appName = "safari";
					var reg = /^.+Version\/([\d\.]+?) Safari.+$/;
					VeryIDE.isSafari=true;
				}else if (navigator.userAgent.indexOf ("Opera") != -1){
					VeryIDE.appName = "opera";
					var reg = /^.{0,}Opera\/(.+?) \(.+$/;
					VeryIDE.isOpera=true;
				}else{
					VeryIDE.appName = "firefox";
					var reg = /^.+Firefox\/([\d\.]+).{0,}$/;
					VeryIDE.isFirefox=true;
				}
			}
			break;
		}
    	VeryIDE.appVersion = navigator.userAgent.replace (reg, "$1");
    	
		if(!VeryIDE.isIE){
			var lang=navigator.language;
		}else{
			var lang=navigator.browserLanguage;
		}
		VeryIDE.appLang=lang.toLowerCase();
    	
		/*bg cache*/
		if(VeryIDE.isIE && !VeryIDE.appVersion<7){
			try{
				VeryIDE.getDocument().addBehavior("#default#userdata");
				document.execCommand("BackgroundImageCache", false, true);
			}catch(e){}
		}
		
		VeryIDE.root = 'http://' + window.location.host + '/veryide/';

		/*root*/ 
		/***
		var children = document.getElementsByTagName('script');
		var len = children.length;

		for (var i = 0; i < len; i++) {
			var src  = children[i].src;
			var pos = src.indexOf("js/core.js");
			if(src && pos>-1){
				VeryIDE.root = src.substr(0,pos);
				break;
			}
		}
		***/
	
		/*folder*/
		VeryIDE.folder={
			js 			:	VeryIDE.root+"js/",
			xml 		: 	VeryIDE.root+"xml/",
			skin		:	VeryIDE.root+"skins/",
			icons		:	VeryIDE.root+"images/icons/",
			//icon		:	"images/icon/",
			cache 	: 	VeryIDE.root+"cache/",
			images	:	VeryIDE.root+"images/",
			plugins	:	VeryIDE.root+"plugins/"
		}
		
    },
    
	/*date*/
	getDate:function(format,date){
		var str = format; var now = date ? date : new Date();
		var y = now.getFullYear(); var m = now.getMonth()+1;
		var d = now.getDate(); var h = now.getHours();
		var i = now.getMinutes(); var s = now.getSeconds();
		
		str = str.replace('yy',y.toString().substr(y.toString().length-2));
		str = str.replace('y',y);
		str = str.replace('mm',('0'+m).substr(m.toString().length-1));
		str = str.replace('m',m);
		str = str.replace('dd',('0'+d).substr(d.toString().length-1));
		str = str.replace('d',d);
		str = str.replace('hh',('0'+h).substr(h.toString().length-1));
		str = str.replace('h',h);
		str = str.replace('ii',('0'+i).substr(i.toString().length-1));
		str = str.replace('i',i);
		str = str.replace('ss',('0'+s).substr(s.toString().length-1));
		str = str.replace('s',s);
		
		return str;
	},
    
	/*document*/
	getDocument:function(){
		//return (document.documentElement && document.documentElement.clientWidth)?document.documentElement:document.body;
		//return (document.documentElement && document.documentElement.scrollTop) ? document.documentElement : document.body;
		return document.documentElement || document.body;
	},
	
	/*script*/
	loadScript:function(name){
		if(VeryIDE.script[name]) return true;
	
		var root=document.getElementsByTagName("HEAD")[0];
		
		var script=document.createElement("script");
		script.type="text/javascript";
		script.src=VeryIDE.folder.js +"veryide."+ name+".js";
		root.appendChild(script);
	},
	
	/*鏄剧ず褰撳墠宸插姞杞界殑缁勪欢*/
	showScript:function(){
		var str="";
		for(var key in VeryIDE.script){
			str+="veryide."+key+".js"+"\n";
		};
		alert(str);
	},
	
	/*鍙戠敓浜嬩欢鐨勮妭鐐�/
	getElement : function(e) {
		if (VeryIDE.isIE) {
			return window.event.srcElement;
		} else {
			return e.currentTarget;
		}
	},
	
	/*鍙戠敓褰撳墠鍦ㄥ鐞嗙殑浜嬩欢鐨勮妭鐐�/
	getTargetElement : function(ev) {
		if (VeryIDE.isIE) {
			return window.event.srcElement;
		} else {
			return e.target;
		}
	},
	
	/*鍋滄浜嬩欢鍐掓场*/
	stopEvent : function(e){
		if (VeryIDE.isIE) {
			window.event.cancelBubble = true;
			window.event.returnValue = false;
		} else {
			e.preventDefault();
			e.stopPropagation();
		}
	},
	
	/*Powered 淇℃伅*/
	showPowered:function(){
		return "Powered by <a href='http://www.veryide.com/?version="+VeryIDE.version+"&versionBuild="+VeryIDE.versionBuild+"&from="+location.host+"' title='VeryIDE' target='_blank'>VeryIDE "+VeryIDE.product+"</a>";
	},
	
	showNavigator:function(){
		var str="";
		for(var key in navigator){
			str+= key+": "+navigator[key]+"\n";
		}
		alert(str);
	},	
	
	/*event*/
	getMouse:function(e){
		if(!document.all){
			VeryIDE.mouseX = e.pageX;
			VeryIDE.mouseY = e.pageY;
		}else{
			VeryIDE.mouseX = e.x + VeryIDE.getDocument().scrollLeft;
			VeryIDE.mouseY = e.y + VeryIDE.getDocument().scrollTop;
		}
	},
	
	/*status*/
	setStatus:function(str){
		window.status=str;
	},
		
	/*message*/
	showMessage:function(str,fun){
		if (typeof fun != 'function') {
			alert(str.replace("<br />","\n\n"));
		}else{
			fun(str);
		}
	}
	
}

VeryIDE.start();

/*state*/
VeryIDE.script["core"]=true;