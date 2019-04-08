jQuery(document).ready(function( $ ) {
    $('body').on('click', '.geo-submit', function(e){
        indpplAddLoading();
    })

    $('body').on('click', '#location-icon', function(e){
        e.preventDefault();
        // var gps = Promise.resolve(getLocation());
        // gps.then(function(value){
        var lat = 0;
        var lon = 0;
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
            lat = position.coords.latitude;
            lon = position.coords.longitude;
            });
        }
        indpplAddLoading();
        setTimeout(function(){
            $.ajax({
                url:indppl_ajax.ajaxurl,
                dataType: 'text',
                method: 'POST',
                data: {
                    action: 'indppl_planting_pal_home_ajax',
                    lat: lat,
                    lon: lon,
                },
                type: 'POST',
                success: function(e){
                    indpplDelLoading();
                    $('.store-list-container').replaceWith(e);
                }
            })
        }, 200);
    })
    $('body').on('click', '.edit-logo-btn', function(e){
        e.preventDefault();
        $('.edit-store-logo').slideToggle();
        $('.current-store-logo').slideToggle();
        
    })

    // tabs
    $('body').on('click', '.indppl-nav li', function(e){
        e.preventDefault();
        $('.indppl-tab-pane, .indppl-nav li').removeClass('indppl-active');
        var active = $(this).children().attr('href');
        $(active).addClass('indppl-active');
        $(this).addClass('indppl-active');
    })

    $('body').on('click', '.store-go-live-btn', function(e){
        e.preventDefault();
        var store_id = $(this).data('id');
        var elem = $(this);
        console.log(store_id);
        indpplAddLoading();
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_switch_live_ajax',
                id: store_id,
            },
            type: 'POST',
            success: function(e){
                indpplDelLoading();
                console.log(e);
                if(e == 0){
                    $(elem).prev('p').text("Your store is not live. If you have filled out all the information below you can make your store live with this button.");
                    $(elem).html("Make Public");
                }else if(e == 1){
                    $(elem).prev('p').text("Your store is Live. To make your site private hit the button below.");
                    $(elem).html("Make Private");
                }
            }
        })
    })
    $('body').on('focus', '.container-date', function(e){
        $(this).datepicker({ dateFormat: 'm/d' });
    })
    $('body').on('click', '.indppl-dot-container', function(e){
        // var id = $(this).parent().prev().attr('id');
        // if(!$(this).parent().prev().checked){
        //     console.log('unchecked');
        // }
        $(this).replaceWith('<div class="indppl-no-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#1ab1ec" stroke-width="2" fill-opacity="0"/> Sorry, your browser does not support inline SVG.</svg></div>');
    })
    $('body').on('click', '.indppl-no-dot-container', function(e){
        var id = $(this).parent().prev().attr('id');
        // console.log(id);
        // if($(this).parent().prev().checked){
        //     console.log('checked');
        // }
        $(this).replaceWith('<div class="indppl-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#1ab1ec" stroke-width="2" fill-opacity="0"/><circle cx="12" cy="12" r="6" stroke="#1ab1ec" stroke-width="2" fill="#1ab1ec" fill-opacity="0.6"/>Sorry, your browser does not support inline SVG.</svg></div>');
    })
    $('body').on('click', '.container-available-in-store', function(){
        $(this).replaceWith('<div class="container-not-available-in-store"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><path class="check-box" d="M30 7 L30 27 L10 27 L10 7 Z"></path></svg></div>');
    })
    $('body').on('click', '.container-not-available-in-store', function(){
        $(this).replaceWith('<div class="container-available-in-store"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><path class="check-box" d="M30 7 L30 27 L10 27 L10 7 Z"></path><path class="checkmark__check" fill="green" d="M15 12 L12 15 L20 22 L37 2 L20 17 L15 12"></path></svg></div>');
    })
    $('body').on('click', '#container-submit', function(e){
        e.preventDefault();
        indpplAddLoading();
        var date = $("#container-select-form").find('input').filter('.container-date').serializeArray();
        var default_container = $("#container-select-form").find('input').filter('.indppl-default-container').serializeArray();
        var non_default = $("#container-select-form").find('input').filter('.indppl-non-default-container').serializeArray();
        var store_id = $('#store-id').val();
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_save_container_data_ajax',
                date: date,
                default_container: default_container,
                non_default: non_default,
                store_id: store_id,
            },
            typed: 'POST',
            success: function(e){
                indpplDelLoading();
                console.log(e);
            }
        })

    })
    $('body').on('click', '.add-container-btn', function(e){
        e.preventDefault();
        $(this).prev().append('<tr class="indppl-table-color-offset"><td class="padding-bottom-5"><input type="text" name="new-container"         class="indppl-container-edit-title" placeholder="Name"></td><td><input type="checkbox" name="new-spring" class="display-none indppl-non-default-container" id="new-spring"/><label class="margin-0" for="new-spring"><div class="indppl-no-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#1ab1ec" stroke-width="2" fill-opacity="0"/> Sorry, your browser does not support inline SVG.</svg></div></label></td>        <td><input type="checkbox" name="new-summer" class="display-none indppl-non-default-container" id="new-summer"/><label class="margin-0" for="new-summer"><div class="indppl-no-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#1ab1ec" stroke-width="2" fill-opacity="0"/> Sorry, your browser does not support inline SVG.</svg></div></label></td>        <td><input type="checkbox" name="new-fall" class="display-none indppl-non-default-container" id="new-fall"/><label class="margin-0" for="new-fall"><div class="indppl-no-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#1ab1ec" stroke-width="2" fill-opacity="0"/> Sorry, your browser does not support inline SVG.</svg></div></label></td>        <td><input type="checkbox" name="new-winter" class="display-none indppl-non-default-container" id="new-winter"/><label class="margin-0" for="new-winter"><div class="indppl-no-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#1ab1ec" stroke-width="2" fill-opacity="0"/> Sorry, your browser does not support inline SVG.</svg></div></label></td></tr>');
    })
    $('body').on('click', function(){
        check_on_load_and_click();
    })
    // same as above but it checks on load.
    check_on_load_and_click();
});

function check_on_load_and_click(){
    var add = 0;
    var user_status = $('#user-status').val();
    console.log(user_status);
    add = $('.indppl-container-edit-title').length;
    // console.log(add);
    if(user_status == 'paidaccountpro' && add > 24){
        $('.add-container-btn').remove();
    }else if(user_status != 'paidaccountpro' && add > 4){
        $('.add-container-btn').remove();
    }
}

function getLocation() {
    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
        var lat = position.coords.latitude;
        var lon = position.coords.longitude;
        gps = [lat, lon];
        return gps;
        });
    }
}

function indpplAddLoading(){
    var primary = 'white';
    var secondary = 'white';
    jQuery('body').append("<div class='indppl-loading-background'><div id='indppl-loading-icon'><svg class='image' width='100' height='100'><path d='M5,50 a1,1 0 0,0 90,0' fill='none' stroke-opacity='0.9' stroke='" + primary + "' stroke-width='9'/></svg><svg class='image-rev' width='100' height='100'><path d='M2,50 a1,1 0 0,1 96,0' fill='none' stroke-opacity='0.7' stroke='" + secondary + "' stroke-width='3.6'/></svg><svg class='image-rev-2' width='100' height='100'><path d='M10,50 a40,40 0 0,0  40,40' stroke-width='6' stroke-opacity='0.7' stroke='" + secondary + "' fill='none'</></svg></div></div>");
}

function indpplDelLoading(){
    jQuery('.indppl-loading-background').remove();
}