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
    });
    $('body').on('focus', '.container-date', function(e){
        $(this).datepicker({ dateFormat: 'm/d' });
    });
    $('body').on('click', '.indppl-dot-container', function(e){
        $(this).parent().prev().addClass('indppl-remove-dot');
        $(this).replaceWith('<div class="indppl-no-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#1ab1ec" stroke-width="2" fill-opacity="0"/> Sorry, your browser does not support inline SVG.</svg></div>');

    });
    $('body').on('click', '.indppl-no-dot-container', function(e){
        $(this).parent().prev().removeClass('indppl-remove-dot');
        $(this).replaceWith('<div class="indppl-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#1ab1ec" stroke-width="2" fill-opacity="0"/><circle cx="12" cy="12" r="6" stroke="#1ab1ec" stroke-width="2" fill="#1ab1ec" fill-opacity="0.6"/>Sorry, your browser does not support inline SVG.</svg></div>');
    });
    $('body').on('click', '.container-available-in-store', function(){
        $(this).parent().parent().parent().parent().prepend("<div class='greyed-out-section'></div>");
        $(this).parent().parent().removeClass('indppl-checked');
        $(this).parent().parent().addClass('indppl-unchecked');
        $(this).replaceWith('<div class="container-not-available-in-store"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><path class="check-box" d="M30 7 L30 27 L10 27 L10 7 Z"></path></svg></div>');
        
    });
    $('body').on('click', '.container-not-available-in-store', function(){
        $(this).parent().parent().parent().prev().remove();
        $(this).parent().parent().addClass('indppl-checked');
        $(this).parent().parent().removeClass('indppl-unchecked');
        $(this).replaceWith('<div class="container-available-in-store"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><path class="check-box" d="M30 7 L30 27 L10 27 L10 7 Z"></path><path class="checkmark__check" fill="green" d="M15 12 L12 15 L20 22 L37 2 L20 17 L15 12"></path></svg></div>');
    });
    $('body').on('click', '#container-submit', function(e){
        e.preventDefault();
        indpplAddLoading();
        var date = $("#container-select-form").find('input').filter('.container-date').serializeArray();
        var available = [];
        var default_container = $("#container-select-form").find('input').filter('.indppl-default-container').serializeArray();
        var non_default = $("#container-select-form").find('input').filter('.indppl-non-default-container').serializeArray();
        var store_id = $('#store-id').val();
        var not_available = [];
        var remove_dot = [];
        var new_array = {};
        var array_num = 0;
        $(".indppl-checked").each(function(){
            available.push($(this).find('input').data('container'));
        });
        $(".indppl-unchecked").each(function(){
            not_available.push($(this).find('input').data('container'));
        });
        $('.indppl-remove-dot').each(function(){
            remove_dot.push($(this).attr('name'));
        });
        $('.indppl-container-edit-title').each(function(){
            if($(this).attr('name') == "new-container"){
                new_array[array_num] = {};
                new_array[array_num]['name'] = $(this).val();
                $(this).parent().parent().find('input').each(function(){
                    if($(this).is(':checked')){
                        season = $(this).attr('name');
                        season_array = season.split('-');
                        new_array[array_num][season] = season_array[1];
                    }
                });
                // $(this).attr('name', 'dead');
                array_num++;
            }
        });

        // console.log(non_default);
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
                available: available,
                not_available: not_available,
                new_array: new_array,
                remove_dot: remove_dot,
            },
            type: 'POST',
            success: function(e){
                console.log(e);
                var new_array = jQuery.parseJSON(e);
                console.log(new_array);
                var i = 0;
                $('.container-add-new').each(function(){
                    $(this).attr('name', "indppl-container-title");
                    $(this).addClass('container-title');
                    $(this).removeClass('container-add-new');
                    $(this).parent().removeClass()
                    $(this).parent().addClass("padding-left-40 position-absolute check-box-container");
                    $(this).parent().prepend('<div class="container-available indppl-checked"><input type="checkbox" id="' + new_array[i] + '-container-available" class="display-none" data-container="' + new_array[i] + '" name="' + new_array[i] + '-container-available" checked=""><label class="margin-0 container-available-check" for="' + new_array[i] + '-container-available"><div class="container-available-in-store"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><path class="check-box" d="M30 7 L30 27 L10 27 L10 7 Z"></path><path class="checkmark__check" fill="green" d="M15 12 L12 15 L20 22 L37 2 L20 17 L15 12"></path></svg></div></label></div>');
                    $(this).parent().parent().find('input').each(function(){
                        if($(this).is(':checkbox')){
                            var season = $(this).attr('name');
                            season_array = season.split('-');
                            $(this).attr('name', new_array[i] + "-" + season_array[1]);
                            $(this).attr('id', new_array[i] + '-' + season_array[1]);
                            $(this).next().attr('for', new_array[i] + '-' + season_array[1]);
                        }
                    });
                    i++;
                });
                greyOutAllUnchecked();
                indpplDelLoading();
            }
        });
            
    });
    $('body').on('click', '.indppl-add-product-btn', function(e){
        e.preventDefault();
        $('body').prepend("<div class='slide-in-products-container'></div>");
        setTimeout(function(){
            $('.slide-in-products-container').addClass('left-0');
            indpplAddLoading('.slide-in-products-container', 'grey', 'grey', 'white-bg-for-loading');
        }, 20)
        
        var type = $(this).data('type');
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_add_new_product_ajax',
                type: type,
            },
            type: 'POST',
            success: function(e){
                console.log(e);
                $('.slide-in-products-container').prepend(e);
                indpplDelLoading();
            }
        })
    })
    $('body').on('click', '.modal-close', function(e){
        $('.slide-in-products-container').removeClass('left-0');
        setTimeout(function(){
            $('.slide-in-products-container').remove();
        }, 1000);
    });
    // $('body').on('click', '.slide-in-products-container', function(e){
    //     $('.slide-in-products-container').remove();
    // })
    $('body').on('click', '.add-container-btn', function(e){
        e.preventDefault();
        $(this).prev().append('<tr class="indppl-table-color-offset"><td class="padding-bottom-5"><input type="text" name="new-container"         class="container-add-new indppl-container-edit-title" placeholder="Name"></td><td><input type="checkbox" name="new-spring" class="display-none indppl-non-default-container" id="new-spring"/><label class="margin-0" for="new-spring"><div class="indppl-no-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#1ab1ec" stroke-width="2" fill-opacity="0"/> Sorry, your browser does not support inline SVG.</svg></div></label></td>        <td><input type="checkbox" name="new-summer" class="display-none indppl-non-default-container" id="new-summer"/><label class="margin-0" for="new-summer"><div class="indppl-no-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#1ab1ec" stroke-width="2" fill-opacity="0"/> Sorry, your browser does not support inline SVG.</svg></div></label></td>        <td><input type="checkbox" name="new-fall" class="display-none indppl-non-default-container" id="new-fall"/><label class="margin-0" for="new-fall"><div class="indppl-no-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#1ab1ec" stroke-width="2" fill-opacity="0"/> Sorry, your browser does not support inline SVG.</svg></div></label></td>        <td><input type="checkbox" name="new-winter" class="display-none indppl-non-default-container" id="new-winter"/><label class="margin-0" for="new-winter"><div class="indppl-no-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#1ab1ec" stroke-width="2" fill-opacity="0"/> Sorry, your browser does not support inline SVG.</svg></div></label></td></tr>');
    });
    $('body').on('click', function(){
        check_on_load_and_click();
    });
    $('body').on('change', '#product-create-brand', function(){
        indpplAddLoading();
        var brand = $(this).val();
        var type = $('#indppl-modal-product-type').val();
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_get_products_by_brand_ajax',
                brand: brand,
                type: type,
            },
            type: 'POST',
            success: function(e){
                // console.log(e);
                $('.product-create-brand-cut-off').children().each(function(){
                    $(this).empty();
                })
                $('.product-create-product').empty();
                $('.product-create-product').append(e);
                indpplDelLoading();
            }
        })
    });
    $('body').on('change', '#product-create-product', function(e){
        indpplAddLoading();
        var product_id = $(this).val();
        var store_id = $('#store-id').val();
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_get_product_info_ajax',
                product_id: product_id,
                store_id: store_id,
            },
            type: 'POST',
            success: function(e){
                array = JSON.parse(e);
                console.log(array);
                $('.product-create-brand-cut-off').children().each(function(){
                    $(this).empty();
                })
                if(array["standard_unit"]){
                    $('.product-create-standard-unit-container').append(array["standard_unit"]);
                }
                if(array['size']){
                    $('.product-create-size-container').append(array['size']);
                }
                if(array['new_size']){
                    $('.product-create-new-size-container').append(array['new_size']);
                }
                if(array['dry_wet']){
                    $('.product-create-dry-wet-container').append(array['dry_wet'][0]);
                    units = indppl_get_units(array['dry_wet'][1]);
                    console.log(units);
                    $.each(units, function(index, value){
                        if(value != array['dry_wet'][2]){
                            $('.product-create-standard-unit').append('<option class="product-create-standard-unit-option" value="' + value + '">' + value + '</option>');
                            
                        }
                        $('.product-create-standard-unit-add').append('<option class="product-create-standard-unit-add-option" value="' + value + '">' + value + '</option>');
                    })
                }
               
                indpplDelLoading();
            }
        })
    });
    $('body').on('click', '.product-create-dry-wet', function(){
        var type = $(this).val();
        array = indppl_get_units(type);
        $('.product-create-standard-unit').empty();
        $.each(array, function(index, value){
            $('.product-create-standard-unit').append('<option class="product-create-standard-unit-option" value="' + value + '">' + value + '</option>');
        })
    })
    $('body').on('click', '#indppl-product-create-new-size-btn', function(e){
        e.preventDefault();
        var size = $('#indpll-product-create-size-num').val();
        var unit = $('#product-create-standard-unit-add').val();
        $('.product-create-size-container').append('<a href="#" class=" indppl-product-create-size-btn margin-right-4 indppl-new-package indppl-background-green" data-size=' + size + ' data-unit=' + unit + '>' + size + " " + unit + '</a>')
    })
    $('body').on('mouseenter', '.indppl-new-package', function(){
        $(this).append('<span class="indppl-x">X</span>');
    })
    $('body').on('mouseleave', '.indppl-new-package', function(){
        $(".indppl-x").remove();
    })
    $('body').on('click', '.indppl-x', function(){
        // this needs to remove a package in the back end.
        $(this).parent().remove();

    })
    greyOutAllUnchecked();
    // same as above but it checks on load.
    check_on_load_and_click();
    check_on_load();
});

function greyOutAllUnchecked(){
    $('.container-available').each(function(){
        if(!$(this).find('input').is(":checked")){
            $(this).parent().parent().prepend("<div class='greyed-out-section'></div>");
            console.log('this');
        }
    })
}

function indppl_get_units($type = 'dry'){
    if($type == 'dry'){
        return ['tsp', 'tbls', 'qt', 'cuft', 'lb', 'g', 'kg', 'oz', 'mL', 'L', 'cups', 'eaches'];
    }else{
        return ['tsp', 'tbls', 'oz', 'qt', 'gal', 'mL', 'L', 'cups'];
    }  
}


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

function check_on_load(){
    var user_status = $('#user-status').val();
    if(user_status != 'paidaccountpro'){
        $('.indppl-containers-table').prepend('<div class="greyed-out-form"><div class="up-sell-overlay"><h2 class="up-sell-title">Upgrade to Pro to gain these features and more!</h2><a href="#" class="indppl-button up-sell-link">Upgrade Now!</a></div></div>');
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

function indpplAddLoading(location = 'body', primary = 'white', secondary = 'white', background = 'indppl-loading-background'){
    jQuery(location).append("<div class='indppl-loading-background'><div class=" + background + "><div id='indppl-loading-icon'><svg class='image' width='100' height='100'><path d='M5,50 a1,1 0 0,0 90,0' fill='none' stroke-opacity='0.9' stroke='" + primary + "' stroke-width='9'/></svg><svg class='image-rev' width='100' height='100'><path d='M2,50 a1,1 0 0,1 96,0' fill='none' stroke-opacity='0.7' stroke='" + secondary + "' stroke-width='3.6'/></svg><svg class='image-rev-2' width='100' height='100'><path d='M10,50 a40,40 0 0,0  40,40' stroke-width='6' stroke-opacity='0.7' stroke='" + secondary + "' fill='none'</></svg></div></div></div>");
}

function indpplDelLoading(){
    jQuery('.indppl-loading-background').remove();
}