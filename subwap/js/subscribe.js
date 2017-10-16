$(function() {
    $( "#slider-range1" ).slider({
      range: true,
      min: 0,
      max: 500,
      values: [ 0, 500 ],
      slide: function( event, ui ) {
        $( "#amount1" ).val(ui.values[ 0 ]);
        $( "#amount11" ).val(ui.values[ 1 ] );
      }
    });
    $( "#amount1" ).val($( "#slider-range1" ).slider( "values", 0 ));
    $( "#amount11" ).val($( "#slider-range1" ).slider( "values", 1 ));
  });
$(function() {
    $( "#slider-range2" ).slider({
      range: true,
      min: 0,
      max: 500,
      values: [ 0, 500 ],
      slide: function( event, ui ) {
        $( "#amount2" ).val(ui.values[ 0 ]);
        $( "#amount22" ).val(ui.values[ 1 ] );
      }
    });
    $( "#amount2" ).val($( "#slider-range2" ).slider( "values", 0 ));
    $( "#amount22" ).val($( "#slider-range2" ).slider( "values", 1 ));
  });