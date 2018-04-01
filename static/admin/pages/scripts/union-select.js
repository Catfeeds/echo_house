         var request_aid = getUrlParameter('parent');
         var request_rid = getUrlParameter('region');
         var request_hid = getUrlParameter('hid');

        $(function(){
               var selectData;
               var $select = $("#parid");
               var $selectSub = $("#subid");
               var $selectDefaultOption = $select.find("option[value=0]").detach();

               $select.change(function(){

                  $selectSub.html("");
                  $.each(selectData.msg[$select.val()], function(index,array){
                     var option = "<option value=\'"+this.id+"\' data-parent=\'"+this.parent+"\'>"+this.name+"</option>";
                     $selectSub.append(option);
                  });
                  $selectSub.prepend($selectDefaultOption);

               });

                $.getJSON(get_select_url,{}, function(data){
                     var $selectDefaultOption;
                         selectData = data;

                     $selectDefaultOption = '<option value="">请选择</option>';

                     $.each(data.msg[0], function(index,array){
                        var option = "<option value=\'"+this.id+"\' >"+this.name+"</option>";
                        $select.append(option);
                     });

                     //如果选择了区域
                     if(request_aid){
                        $select.find("[value="+request_aid+"]").prop('selected','selected');
                     }else{
                        $select.prepend($selectDefaultOption);
                     }

                     //如果选择了片区
                     if(request_rid){
                        $selectSub.html("");
                        $.each(selectData.msg[request_aid], function(index,array){
                           var option = "<option value=\'"+this.id+"\' data-parent=\'"+this.parent+"\'>"+this.name+"</option>";
                           $selectSub.append(option);
                        });
                        $selectSub.prepend($selectDefaultOption);
                        $selectSub.find("[value="+request_rid+"]").prop('selected','selected');
                     }

                     var defaultSubID = $selectSub.data("parent");

                     if(defaultSubID){
                        var opt = $select.find("[value="+defaultSubID+"]").prop("selected","selected");
                     }
                });

        });


        function getUrlParameter(sParam)
        {
            var sPageURL = window.location.search.substring(1);
            var sURLVariables = sPageURL.split('&');
            for (var i = 0; i < sURLVariables.length; i++)
            {
                var sParameterName = sURLVariables[i].split('=');
                if (sParameterName[0] == sParam)
                {
                    return sParameterName[1];
                }
            }
        }
