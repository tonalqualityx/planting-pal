jQuery(document).ready(function( $ ) {
    checkSponsor();
    $('body').on('click', '#is_sponsor', function(){
        $('.sponsor-hidden').slideToggle();
    })
    function checkSponsor(){
        if($('#is_sponsor').is(':checked')){
            $('.sponsor-hidden').slideToggle();
            console.log('hello');
        }
    }
    
});
