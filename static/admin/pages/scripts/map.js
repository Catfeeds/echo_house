$(function(){

    //由于地图在隐藏状态时无法设置地图的位置
    $('.show-map').on('click',function(){
        setTimeout(function(){
            bmap.setCenterZoom();
            if($('[target=pointname]').length>0){
                try{
                    getLatLng($('[target=pointname]').val());
                }catch(e){}
            }
        },1000);
    });

    var zoom=parseInt($("#zoom").val());
    //var zoom=12;
    var lng=$("#lng").val();
    var lat=$("#lat").val();
    var map = new BMap.Map("allmap");
    bmap.init( {"target":"map","zoom":zoom } );
    bmap.addToolFish();
    bmap.enableWheelZoom(true);
    bmap.setPoint(lng,lat);
    bmap.setMarker();
    bmap.getLatLngByMarker(getLatLngByMarker);
    bmap.getZoom(getZoom);
    bmap.enableMarkerDrag();
    bmap.setCenterZoom();

    $("#local").onkeydown = keyDown;

});

function myFun(result){
    var dlng = result.center.lng;
    var dlat = result.center.lat;
    bmap.setPoint(dlng, dlat);
    bmap.setMarker();

    $("#lng").val(dlng);
    $("#lat").val(dlat);
}

if( $('#lng').val()<=0 && $('#lat').val()<=0 ){
    var myCity = new BMap.LocalCity();
    myCity.get(myFun);
}

function changeCoordText(){
    var lng=$("#lng").val();
    var lat=$("#lat").val();
    document.getElementById('coordText').innerText ="经度: " + lng + " 纬度: "+lat;
}

function keyDown( e ) {
    var e = e || event;
    var keyCode = e.which || e.keyCode;

    if( keyCode == 13 ) {
        getLatLng($("#local").val());
    }
}

//根据名字模糊搜索 addr:string  文化宫
function getLatLng( addr ) {

    //先清空百度地图上的OVERLAY
    bmap.map.clearOverlays();

    //搜索对象实例
    var local = new BMap.LocalSearch(bmap.map, {
        renderOptions:{map: bmap.map}
    });

    //设置返回搜索结果的个数
    local.setPageCapacity(1);

    //地图添加标注的回调函数
    local.setMarkersSetCallback(function(){

        var point = null;
        //获取地图上的所有标注（返回数组）
        var ov = bmap.map.getOverlays();
        //根据第一个标注，获得POINT对象
        var point = ov[0].getPosition();
        //再次清空百度地图上的OVERLAY
        bmap.map.clearOverlays();

        //这里调用bmap类的marker成员变量，改变原先的坐标值
        bmap.marker.setPosition(point);
        //向百度地图添加此marker
        bmap.map.addOverlay(bmap.marker);

        //文本框的值
        document.getElementById('lng').value=point.lng;
        document.getElementById('lat').value=point.lat;
    });

    //执行搜索
    local.search(addr);
}

function getLatLngByMarker(lng,lat,map) {

    document.getElementById('lng').value=lng;
    document.getElementById('lat').value=lat;
}

function getZoom( zoom ) {
    document.getElementById('zoom').value=zoom;
}
