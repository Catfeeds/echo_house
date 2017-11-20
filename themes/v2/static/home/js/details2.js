$(function () {



})

function mapRoundSearch(type) {
    map.clearOverlays();
    // var local = new BMap.LocalSearch(map, {
    //     renderOptions:{map: map}
    // });
    var options = {
        onSearchComplete: function (results) {
            // console.log(results._pois)
            if (local.getStatus() == BMAP_STATUS_SUCCESS) {
                $('#list').empty();
                // // 判断状态是否正确
                var list = '';
                // for (var i = 0; i < results.getCurrentNumPois(); i++) {
                    // if()
                    // console.log(results.getPoi(i));
                    // var tpl = '';
                    // tpl += '<div class="nearby_r"><img src="img/tubiao.png" alt=""><p>' + results.getPoi(i).title + '<span style="color: #a0a0a0"></span></p><p style="color: #a0a0a0">' + results.getPoi(i).address + '</p><div class="nearby_line"></div></div>';
                    // tpl += '打开';
                    // tpl += '</a>';
                    // $('#list').append(tpl);
                // }


                // new Vue({
                //     el:"#list",
                //     data:{
                //         items: s
                //     }
                // })
            }
        },
        renderOptions: {map: map}
    };
    var local = new BMap.LocalSearch(map, options);
    local.search(type);
    local.searchInBounds(type, map.getBounds());
}



