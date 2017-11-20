$(function () {
    // 百度地图API功能
    var map = new BMap.Map("allmap");            // 创建Map实例
    map.centerAndZoom(new BMap.Point(116.404, 39.915), 11);
    var local = new BMap.LocalSearch(map, {
        renderOptions:{map: map, autoViewport:true}
    });
    local.searchNearby("小吃", "前门");



})

// function mapRoundSearch(type) {
//     map.clearOverlays();
//     // var local = new BMap.LocalSearch(map, {
//     //     renderOptions:{map: map}
//     // });
//     var options = {
//         onSearchComplete: function (results) {
//             console.log(results._pois)
//             if (local.getStatus() == BMAP_STATUS_SUCCESS) {
//                 // // 判断状态是否正确
//                 var s = [];
//                 for (var i = 0; i < results.getCurrentNumPois(); i++) {
//                     s.push(results.getPoi(i).title);
//                     // s.push(results.getPoi(i).title + ", " + results.getPoi(i).address);
//                 }
//                 document.getElementById("list").innerHTML = s.join("<br>");
//             }
//         },
//         renderOptions: {map: map}
//     };
//     var local = new BMap.LocalSearch(map, options);
//     local.search(type);
//     local.searchInBounds(type, map.getBounds());
//
//
// }