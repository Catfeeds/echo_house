/*
*	Copyright VeryIDE,2007-2010
*	http://www.veryide.com/
*	
*	$Id: veryide.effect.js v2.3 17:16 2009-9-30 leilei $
*/

if(typeof VeryIDE!='object'){
	var VeryIDE={script:[]}
}

/*
	显示窗口/iframe
		
	参数：
		id					容器ID
		title				标题名称
		url					页面URL地址
		width				页面宽,可用%
		height				页面高,可用%
		options				选项,JSON数据
*/

VeryIDE.Window=function(id,title,url,width,height,options){
	
	if(typeof options!="object") var options={};
	
	var doc=VeryIDE.getDocument();
	var tid=id+"-title";
	var obj=$(id);
	
        obj=document.createElement("DIV");
        obj.id=id;
        document.body.appendChild(obj);
		
	if(width.toString().indexOf("%")>-1)	width=doc.scrollWidth*(parseInt(width)/100);
	if(height.toString().indexOf("%")>-1) 	height=doc.scrollHeight*(parseInt(height)/100);
	
	if(width>doc.scrollWidth){
		width=doc.scrollWidth-100;
		height+=30;
	}
	
	if(height>doc.scrollHeight){
		height=doc.scrollHeight-100;
		width+=30;
	}
	
	var str='<div id="'+tid+'" class="title" ondblclick="removes(\''+id+'\');" title="双击关闭"><span class="close" onclick="removes(\''+id+'\');" alt="关闭"><em>关闭</em></span><strong class="text">'+title+'</strong></div>'
	
	str+='<div class="box"><iframe id="'+id+'-iframe" name="'+id+'-iframe" width="100%" height="'+(height-30)+'" frameborder="0" src="' + url + '" /></div>';
		
	obj.innerHTML = str;
	
	
	var top=100+(doc.clientHeight/2)-(height/2);
	if(top<0){
		top=20;
		
	}
	
	var left=(doc.clientWidth-width)/2;
	if(left<0){
		left=20;
	}
	
	obj.style.left=left+"px";
	obj.style.top=top+"px";
	obj.style.width=width+"px";
	obj.style.height=height+"px";
	obj.style.zIndex=999999;
        
        //绑定ESC到关闭
	addKeyEvent(27,function(){
		removes(id);
	});
			
	addObjectEvent(document,"keyup",function(event){
		addKeyEvent.Listener(event);
	});
}

/*state*/
VeryIDE.script["effect"]=true;