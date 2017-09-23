$(document).ready(function() {
     //validata
    $('#form').validate();
    $.get('/api/tag/publishtags',function(data) {
        $('.register-attention-text').html(data.data.regis_words);
        our_uids = data.data.our_uids;
        kf_id = data.data.kf_id;
    }); 
});