
if(typeof ind_base_url !== 'undefined'){
    if(window.location.href == ind_base_url){
        if(ind_is_mobile == true && ind_desktop != true){
            window.location.href = ind_base_url + 'app';
        }
    }
}

jQuery(document).ready(function( $ ) {

    // Let's set some stuff up for use whenever we need it!
    var checkBox = '<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><path class="check-box" d="M30 7 L30 27 L10 27 L10 7 Z"></path></svg>';
    var checkMark = '<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><path class="check-box" d="M30 7 L30 27 L10 27 L10 7 Z"></path><path class="checkmark__check" fill="green" d="M15 12 L12 15 L20 22 L37 2 L20 17 L15 12"></path></svg>';

    $('body').on('click', '.geo-submit', function(e){
        e.preventDefault();
        var zip = $('#zip-for-location').val();
        // var radius = $('#geo-radius-custom').val();
        // if(parseInt(radius) > 30){
        //     $('#geo-radius-custom').val(30);
        // }
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                lat = position.coords.latitude;
                lon = position.coords.longitude;
            });
        }
        var radius = 30;
        indpplAddLoading();
        setTimeout(function(){
            $.ajax({
                url:indppl_ajax.ajaxurl,
                dataType: 'text',
                method: 'POST',
                data: {
                    action: 'indppl_planting_pal_home_ajax',
                    zip: zip,
                    lat: lat,
                    lon: lon,
                    radius: radius,
                },
                type: 'POST',
                success: function(e){
                    indpplDelLoading();
                    $('.store-list-container').replaceWith(e);
                }
            });
        }, 200);
    })

    $('body').on('click', '#indppl-app-pagination', function(e){
        e.preventDefault();
        indpplAddLoading();

        var pagination = $(this).data('page');
        var zip = $('#zip-for-location').val();
        var lat = 0;
        var lon = 0;
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                lat = position.coords.latitude;
                lon = position.coords.longitude;
            });
        }
        setTimeout(function(){
            var radius = 30;
            $.ajax({
                url:indppl_ajax.ajaxurl,
                dataType: 'text',
                method: 'POST',
                data: {
                    action: 'indppl_planting_pal_home_ajax',
                    zip: zip,
                    lat: lat,
                    lon: lon,
                    radius: radius,
                    pagination: pagination,
                },
                type: 'POST',
                success: function(e){
                    indpplDelLoading();
                    $('.store-list-container').replaceWith(e);
                }
            });
        }, 200);

    })

    $('body').on('click', '#location-icon', function(e){
        e.preventDefault();
        ind_geo_locate_stores();
    })

    if($('#location-icon').length > 0){
        ind_geo_locate_stores();
    }

    function ind_geo_locate_stores(){
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
        // var radius = $('#geo-radius-custom').val();
        // if(parseInt(radius) > 30){
        //     radius = 30;
        //     $('#geo-radius-custom').val(30);
        // }
        radius = 30;
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
                    radius: radius,
                },
                type: 'POST',
                success: function(e){
                    indpplDelLoading();
                    $('.store-list-container').replaceWith(e);
                }
            });
        }, 200);
    }

    $('body').on('click', '.edit-logo-btn', function(e){
        e.preventDefault();
        $('.edit-store-logo').slideToggle();
        $('.current-store-logo').slideToggle();
        
    })

    // tabs
    $('body').on('click', '.indppl-nav li', function(e){
        e.preventDefault();
        var active = '';
        if($(this).children().attr('href') != '#indppl-tab-2'){
            $('.indppl-tab-pane').each(function(){
                if($(this).hasClass('indppl-active')){
                    
                    active = $(this).attr('id');

                }
            });
            if(active == 'indppl-tab-2'){
                containerSubmit();
            }
        }
        var split = location.search.replace('?', '').split('=')
        $store_id = $('#store-id').val();
        var url = window.location.href;
        url = url + "?store-id=" + $store_id;
        // console.log(split);
        if(split[0] != 'store-id'){
            window.location.href = url;
        }else{

            $('.indppl-tab-pane, .indppl-nav li').removeClass('indppl-active');
            var active = $(this).children().attr('href');
            $(active).addClass('indppl-active');
            $(this).addClass('indppl-active');
        }


    })

    $('body').on('click', '.store-go-live-btn', function(e){
        e.preventDefault();
        var store_id = $(this).data('id');
        var elem = $(this);
        var version_check = 1.0;
        // console.log(store_id);
        indpplAddLoading();
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_switch_live_ajax',
                id: store_id,
                version_check: version_check,
            },
            type: 'POST',
            success: function(e){
                indpplDelLoading();
                // console.log(e);
                if(e == 0){
                    $(elem).prev('p').text("Your store is not live. If you have filled out all the information below you can make your store live with this button.");
                    $(elem).html("Make Public");
                }else if(e == 1){
                    $(elem).prev('p').text("Your store is Live. To make your site private hit the button below.");
                    $(elem).html("Make Private");
                }
            }
        });
    });
    $('body').on('focus', '.container-date', function(e){
        $(this).datepicker({ dateFormat: 'm/d' });
    });
    $('body').on('click', '.indppl-dot-container', function(e){
        $(this).parent().prev().addClass('indppl-remove-dot');
        $(this).replaceWith('<div class="indppl-no-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#6a6e76" stroke-width="2" fill-opacity="0"/> Sorry, your browser does not support inline SVG.</svg></div>');

    });
    $('body').on('click', '.indppl-no-dot-container', function(e){
        $(this).parent().prev().removeClass('indppl-remove-dot');
        $(this).replaceWith('<div class="indppl-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#6a6e76" stroke-width="2" fill-opacity="0"/><circle cx="12" cy="12" r="6" stroke="#a9d56a" stroke-width="2" fill="#a9d56a" fill-opacity="1"/>Sorry, your browser does not support inline SVG.</svg></div>');
    });
    $('body').on('click', '.container-available-in-store', function(){
        $(this).parents('.indppl-table-color-offset').find('.indppl-dot-container').each(function(){
            // if($(this).find('.indppl-no-dot-container')){
                $(this).html('<svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#6a6e76" stroke-width="2" fill-opacity="0"/> Sorry, your browser does not support inline SVG.</svg>')
            // }
            if(!$(this).parent().prev().hasClass('indppl-remove-dot')){
                $(this).parent().prev().addClass('indppl-remove-dot');
            }
            if($(this).parent().prev().is(':checked')){
                $(this).parent().prev().prop('checked', false);
            }
            $(this).addClass('indppl-no-dot-container');
            $(this).removeClass('indppl-dot-container');

        })
        $(this).parent().parent().parent().parent().prepend("<div class='greyed-out-section'></div>");
        $(this).parent().parent().removeClass('indppl-checked');
        $(this).parent().parent().addClass('indppl-unchecked');
        $(this).replaceWith('<div class="container-not-available-in-store"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><path class="check-box" d="M30 7 L30 27 L10 27 L10 7 Z"></path></svg></div>');
    });
    $('body').on('click', '.container-not-available-in-store', function(){
        $(this).parents('.indppl-table-color-offset').find('.indppl-no-dot-container').each(function(){
            if($(this).find('.indppl-dot-container')){
                $(this).html('<svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#6a6e76" stroke-width="2" fill-opacity="0"/><circle cx="12" cy="12" r="6" stroke="#a9d56a" stroke-width="2" fill="#a9d56a" fill-opacity="1"/>Sorry, your browser does not support inline SVG.</svg>')
            }
            if($(this).parent().prev().hasClass('indppl-remove-dot')){
                $(this).parent().prev().removeClass('indppl-remove-dot');
            }
            if(!$(this).parent().prev().is(':checked')){
                $(this).parent().prev().prop('checked', true);
            }
            $(this).addClass('indppl-dot-container');
            $(this).removeClass('indppl-no-dot-container');
            

        })
        $(this).parent().parent().parent().prev().remove();
        $(this).parent().parent().addClass('indppl-checked');
        $(this).parent().parent().addClass('indppl-update-apps');
        $(this).parent().parent().removeClass('indppl-unchecked');
        $(this).replaceWith('<div class="container-available-in-store"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><path class="check-box" d="M30 7 L30 27 L10 27 L10 7 Z"></path><path class="checkmark__check" fill="green" d="M15 12 L12 15 L20 22 L37 2 L20 17 L15 12"></path></svg></div>');

    });
    $('body').on('click', '#container-submit', function(e){
        e.preventDefault();
        containerSubmit();  
    });

    $("body").keyup(function(e){
        if(e.keyCode == 27){
            closeModal();
        }
    });

    $('body').on('click', '.indppl-add-product-btn', function(e){
        e.preventDefault();
        var type = $(this).data('type');
        indpplAddProduct(type);
    })
    $('body').on('click', '.modal-close', function(e){
        $(this).hide();
        closeModal();
    });

    function closeModal(){
        $('.slide-in-products-container').removeClass('left-0');
        setTimeout(function(){
            $('.slide-in-products-container').remove();
        }, 1000);
    }
    $('body').on('click', '.sponsor-modal-close', function(e){
        $(this).hide();
        $('.slide-in-sponsor-container').removeClass('left-0');
        setTimeout(function(){
            $('.slide-in-sponsor-container').remove();
        }, 1000);
    });
    // $('body').on('click', '.slide-in-products-container', function(e){
    //     $('.slide-in-products-container').remove();
    // })
    var container_id = 1;
    $('body').on('click', '.add-container-btn', function(e){
        e.preventDefault();
        $('.indppl-containers-table').append('<tr id="contianer_tr_id_' + container_id + '" class="indppl-containers-row indppl-table-color-offset"><td class="padding-bottom-5"><input type="text" style="width: 75%!important;" name="new-container-'+ container_id +'"         class="container-add-new indppl-container-edit-title" placeholder="Name"></td><td class="text-align-center indppl-season-boarders"><input type="checkbox" checked name="new-spring-'+ container_id +'" class="display-none indppl-non-default-container" id="new-spring-'+ container_id +'"/><label class="margin-0" for="new-spring-'+ container_id +'"><div class="indppl-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#6a6e76" stroke-width="2" fill-opacity="0"/><circle cx="12" cy="12" r="6" stroke="#a9d56a" stroke-width="2" fill="#a9d56a" fill-opacity="1"></circle> Sorry, your browser does not support inline SVG.</svg></div></label></td>        <td class="text-align-center indppl-season-boarders"><input type="checkbox" checked name="new-summer-'+ container_id +'" class="display-none indppl-non-default-container" id="new-summer-'+ container_id +'"/><label class="margin-0" for="new-summer-'+ container_id +'"><div class="indppl-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#6a6e76" stroke-width="2" fill-opacity="0"/> <circle cx="12" cy="12" r="6" stroke="#a9d56a" stroke-width="2" fill="#a9d56a" fill-opacity="1"></circle>Sorry, your browser does not support inline SVG.</svg></div></label></td>        <td class="text-align-center indppl-season-boarders"><input type="checkbox" checked name="new-fall-'+ container_id +'" class="display-none indppl-non-default-container" id="new-fall-'+ container_id +'"/><label class="margin-0" for="new-fall-'+ container_id +'"><div class="indppl-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#6a6e76" stroke-width="2" fill-opacity="0"/> <circle cx="12" cy="12" r="6" stroke="#a9d56a" stroke-width="2" fill="#a9d56a" fill-opacity="1"></circle>Sorry, your browser does not support inline SVG.</svg></div></label></td>        <td class="text-align-center indppl-season-boarders"><input type="checkbox" checked name="new-winter-'+ container_id +'" class="display-none indppl-non-default-container" id="new-winter-'+ container_id +'"/><label class="margin-0" for="new-winter-'+ container_id +'"><div class="indppl-dot-container"><svg height="24" width="24"><circle cx="12" cy="12" r="10" stroke="#6a6e76" stroke-width="2" fill-opacity="0"/> <circle cx="12" cy="12" r="6" stroke="#a9d56a" stroke-width="2" fill="#a9d56a" fill-opacity="1"></circle>Sorry, your browser does not support inline SVG.</svg></div></label></td></tr>');
        container_id++;
    });
    $('body').on('click', function(){
        check_on_load_and_click();
    });

    $('body').on('click', '#product-add-new-brand-btn', function(e){
        e.preventDefault();
        indpplAddLoading();
        var brand = $('#product-add-new-brand').val();
        var version_check = 1.0;
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_add_new_brand_ajax',
                brand: brand,
                version_check: version_check,
            },
            type: 'POST',
            success: function(e){
                // console.log(e);
                $(".create-brand-button-container").hide();
                $("#product-add-new-brand").hide();
                $('#product-create-product').empty();
                $(".product-create-product").show();

                $('#product-create-brand').append('<option selected value="' + e + '">' + brand + '</option>');
                var status = $('#user-status').val();
                if(status == 'paidaccountpro'){
                    $('.product-create-product').append('<option selected dissabled>Select Brand</option><option value="new">Add Product</option>');
                }

                indpplDelLoading();
            }
        });
    })

    $('body').on('change', '#product-create-brand', function(){
        
        var brand = $(this).val();
        if(brand == 'new'){
            $(".product-create-product").hide();
            $("<input type='text' id='product-add-new-brand'><div class='create-brand-button-container'><a href='#' class='indppl-button' id='product-add-new-brand-btn'>Create Brand</a></div>").insertAfter(this);
        }else{
            indpplAddLoading();
            $(".product-create-product").show();
            $("#product-add-new-brand").hide();
            $(".create-brand-button-container").hide();
            var type = $('#indppl-modal-product-type').val();
            var version_check = 1.0;
            $.ajax({
                url:indppl_ajax.ajaxurl,
                dataType: 'text',
                method: 'POST',
                data: {
                    action: 'indppl_get_products_by_brand_ajax',
                    brand: brand,
                    type: type,
                    version_check: version_check,
                },
                type: 'POST',
                success: function(e){
                    // console.log(e);
                    $('.product-create-brand-cut-off').children().each(function(){
                        $(this).empty();
                    })
                    $('.product-create-product').empty();
                    $('.product-create-product').append(e);
                    var status = $('#user-status').val();
                    // console.log(status);
                    if(status == 'paidaccountpro'){
                        $('.product-create-product option:first').after('<option id="add_new_brand_select" value="new">Create New Product</option>');
                    }
                    indpplDelLoading();
                }
            });
        }
    });
    $('body').on('change', '#product-create-product', function(e){
        indpplAddLoading();
        var product_id = $(this).val();
        var store_id = $('#store-id').val();
        var type = $('#indppl-modal-product-type').val();
        var version_check = 1.0;
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_get_product_info_ajax',
                product_id: product_id,
                store_id: store_id,
                type: type,
                version_check: version_check,
            },
            type: 'POST',
            success: function(e){
                // console.log(e);
                array = JSON.parse(e);
                $('.product-create-brand-cut-off').children().each(function(){
                    $(this).empty();
                })
                if(array['instructions']){
                    $('.create-product-header-instructions').empty();
                    $('.create-product-header-instructions').append(array['instructions']);
                }
                if(array["standard_unit"]){
                    $('.product-create-standard-unit-container').append(array["standard_unit"]);
                }
                if(array['size']){
                    $('.product-create-size-container').append(array['size']);
                }
                if(array['new_size']){
                    $('.product-create-new-size-container').append(array['new_size']);
                }
                $('.product-more-things-header').append("<h3 class='product-create-dry-wet-title green-text'>A Couple More Things</h3>");
                if(array['dry_wet']){
                    $('.product-create-dry-wet-container').append(array['dry_wet'][0]);
                    units = indppl_get_units(array['dry_wet'][1]);
                    // console.log(array['dry_wet'][2]);
                    if(type == 'beds'){
                        delete units['each'];
                    }
                    if(array['dry_wet'][1] == 'wet'){
                        $('.product-create-fraction-bag').addClass('hide');
                    }
                    $.each(units, function(index, value){
                        var name = value;
                        // console.log(value);
                        // if(value == 'qt-d'){
                        //     name = 'Quart';
                        // }
                        if(value != array['dry_wet'][2]){
                            $('.product-create-standard-unit').append('<option class="product-create-standard-unit-option" value="' + index + '">' + name + '</option>');
                        }
                        $('.product-create-standard-unit-add').append('<option class="product-create-standard-unit-add-option" value="' + index + '">' + name + '</option>');
                    })
                }
                if(array['cups']){
                    $('.product-create-5-cups-container').append(array['cups']);

                }
                // console.log(array['app_rate']);
                // if(array['app_rate']){
                //     $('.product-create-app-rate-container').append(array['app_rate']);
                // }
                if(array['app_rates_chart']){
                    $('.product-create-app-rates-chart-container').append(array['app_rates_chart']);
                }
                var units = indppl_get_units(array['dry_wet'][1]);
                // console.log(units);
                $('.indppl-product-create-chart-app-unit').each(function(){
                    var select = $(this).data('unit');
                    var elem = $(this);
                    // console.log(select);
                    $.each(units, function(index, value){
                        var name = value;
                        // if(value == 'qt-d' || value == 'qt-l'){
                        //     name = 'Quart';
                        // }
                        if(select == index){
                            selected = `selected`;
                        }else{
                            selected = ``;
                        }
                        $(elem).append('<option class="indppl-product-create-chart-app-unit-option" value="' + index + '" ' + selected + '>' + name + '</option>');
                    });
                    // console.log(unit);
                });
                if(array['next_btn']){
                    $('.product-create-save-done-container').append(array['next_btn']);
                }
                if(array['new_product']){
                    $('.product-create-add-product-name').append(array['new_product']);
                }
                if(array['usage_type']){
                    $('.product-create-usage-type').append(array['usage_type']);
                }
                if(array['default']){
                    if(array['default'] == 1){
                        $('.product-create-fraction-bag').hide();
                    }
                }else{
                    $('.product-create-fraction-bag').show();
                }
                if(array['fraction']){
                    $('.product-create-fraction-bag').append(array['fraction']);
                }
                checkIfEach();
                indpplDelLoading();
            }
        });
    });

    $('body').on('click', '.product-create-dry-wet', function(){
        var type = $(this).val();
        array = indppl_get_units(type);
        $('.product-create-standard-unit').empty();
        $.each(array, function(index, value){
            var name = value;
            // if(value == 'qt-d' || value == 'qt-l'){
            //     name = 'Quart';
            // }
            $('.product-create-standard-unit').append('<option class="product-create-standard-unit-option" value="' + value + '">' + name + '</option>');
        })
        if(type == "wet"){
            $('.product-create-fraction-bag').addClass('hide');
            $('.product-create-5-cups-container').addClass('hide');
        }else{
            $('.product-create-fraction-bag').removeClass('hide');
            $('.product-create-5-cups-container').removeClass('hide'); 
        }
    })

    $('body').on('click', '#indppl-product-create-new-size-btn', function(e){
        e.preventDefault();
        var size = $('#indpll-product-create-size-num').val();
        var unit = $('#product-create-standard-unit-add').val();
        var create_new = true;
        $('.indppl-product-create-size-btn').each(function(){
            var this_size = $(this).data('size');
            var this_unit = $(this).data('unit');
            if(this_size == size && this_unit == unit){
                create_new = false;
            }
        })
        // console.log(create_new);
        var weight = ['lb', 'oz', 'g', 'kg'];
        if(create_new == true){
            if($.inArray(unit, weight)){
                $('.product-create-5-cups-container').hide();
            }else{
                $('.product-create-5-cups-container').show();
            }
            var name = unit;
            if(unit == 'qt-l' || unit == 'qt-d'){
                name = 'Quart';
            }
            $('.product-create-size-container').append('<a href="#" class=" indppl-product-create-size-btn margin-right-4 indppl-non-default-package indppl-new-package indppl-size-selected" data-id="0" data-size=' + size + ' data-unit=' + unit + '>' + size + " " + name + '</a>');
        }
        checkIfEach();

    })
    $('body').on('mouseenter', '.indppl-size-selected', function(){
        if($(this).hasClass('indppl-non-default-package')){
            $(this).append('<span class="indppl-x">X</span>');
        }
    })
    
    $('body').on('click', '.indppl-x', function(e){
        // this needs to remove a package in the back end.
        if($(this).parent().hasClass('indppl-non-default-package')){
            // console.log('inside');
            
            $(this).parent().removeClass('indppl-size-selected').addClass('indppl-size-not-selected');
            $(this).parent().hide();
            e.stopPropagation();
        }else{
            $(this).parent().remove();
        }
            
    })
    $('body').on('mouseleave', '.indppl-size-selected', function(){
        setTimeout(function(){

            $(".indppl-x").remove();
        }, 100);
    })
    $('body').on('mouseleave', '.indppl-size-not-selected', function(){
        setTimeout(function(){

            $(".indppl-x").remove();
        }, 100);
    })
    $('body').on('click', '.indppl-product-create-size-btn', function(e){
        e.preventDefault();
        if(!$(this).hasClass('indppl-size-selected')){
            $(this).addClass('indppl-size-selected').removeClass('indppl-size-not-selected');
        }else{
            $(this).removeClass('indppl-size-selected').addClass('indppl-size-not-selected');
        }
    })
    $('body').on('click', '.product-create-submit', function(e){
        e.preventDefault();
        indpplAddLoading();
        $('.indppl-form-required').remove();
        var required = true;
        // console.log($('.indppl-add-product-name').val());
        if($('.indppl-add-product-name').val() == ""){
            $('.indppl-add-product-name').after("<span class='indppl-form-required margin-left-10 margin-top-20 color-red'>Required</span>");
            required = false;
        }
        if(!$('.indppl-size-selected')[0]){
            $('.product-create-new-size-container').append("<span class='indppl-form-required margin-left-10 color-red'>Required</span>");
            required = false;
        }
        console.log($('.product-create-5-cups-container').is(':visible'));
        if($('.product-create-5-cups-inside-container').is(':visible') && $('.product-create-5-cups-container').is(':visible')){
            console.log($('#indpll-product-create-cups-num').val());
            if($('#indpll-product-create-cups-num').val() == ""){
                $('.product-create-5-cups-inside-container').append("<span class='indppl-form-required margin-left-10 color-red'>Required</span>");
                required = false;
            }
        }
        if(required == false){
            indpplDelLoading();
            return;
        }
        // var non_default = $("#container-select-form").find('input').filter('.indppl-non-default-container').serializeArray();
        var type = $('#indppl-modal-product-type').val();
        var product_id = $('#product-create-product').val();
        var brand = $('#product-create-brand').val();
        var store_id = $('#store-id').val();
        var product_unit = $('.indppl-new-package').first().data('unit');
        var fraction = false;
        if($('#product-create-fraction-bag').is(':checked')){
            fraction = true;
        }
        var product_dryliquid = $('.product-create-dry-wet:checked').val();
        if(!product_dryliquid){
            product_dryliquid = $('.product-create-dry-wet').val();
        }
        var product_name = $('.indppl-add-product-name').val();
        if(!$(this).is('#product-create-next')){
            
            var product_select = $("#product-create-form").find('select').filter('.some-kind-of-wonderful').serializeArray();
            var product_input = $("#product-create-form").find('input').filter('.some-kind-of-wonderful').serializeArray();
            var container_id = [];
            $('.bag-apprates-container-title').each(function(){
                container_id.push($(this).data('id'));
            });
            var first_package = {}
            if(fraction){
                first_package['num'] = $('#indppl-how-much-header').data('num');
                first_package['unit'] = $('#indppl-how-much-header').data('unit');
            }else{
                first_package['num'] = $('.bag-apprates-title').data('num');
                first_package['unit'] = $('.bag-apprates-title').data('unit');
            }
        }
        if($(this).is('#product-create-next')){
            $('.create-product-header-instructions').hide();
        }
        
        var cups_num = $('.indppl-product-create-cups-num').val();
        var cups_unit = $('#product-create-5-cups').children("option:selected").val();
        
            // console.log(product_array);
        var elem = $(this);
        var package_array = [];
        var package_remove = [];
        var new_pack = {};
        var i = 0;
        var version_check = 1.0;
        // console.log(product_input);
        // console.log(product_dryliquid);
        if($(this).is('#product-create-next')){
            var next = true;
            $('.indppl-product-create-size-btn').each(function(){
                if($(this).hasClass('indppl-size-selected')){
                    if($(this).hasClass('indppl-new-package')){
                        new_pack[i] = {};
                        new_pack[i]['size'] = $(this).data('size');
                        new_pack[i]['unit'] = $(this).data('unit');
                        new_pack[i]['name'] = brand + " " + $('#product-create-product option:selected').text() + " " + $(this).data('size') + $(this).data('unit');
                        i++;
                    }else{
                        package_array.push($(this).data('id'));
                    }
                }else{
                    if($(this).hasClass('indppl-non-default-package')){
                        package_remove.push({'id': $(this).data('id')})
                    }else{
                        package_remove.push($(this).data('id'));
                    }
                }
            })
        }
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_save_product_ajax',
                product_id: product_id,
                store_id: store_id,
                type: type,
                brand: brand,
                package_array: package_array,
                package_remove: package_remove,
                product_input: product_input,
                product_select: product_select,
                prodcut_unit: product_unit,
                product_dryliquid: product_dryliquid,
                new_pack: new_pack,
                cups_num: cups_num,
                cups_unit: cups_unit,
                fraction: fraction,
                container_id: container_id,
                first_package: first_package,
                product_name: product_name,
                version_check: version_check,
                next: next,
            },
            type: 'POST',
            success: function(e){
                // console.log(type);

                if(type == 'pots'){
                    getProductInfo();
                    $('.slide-in-products-container').removeClass('left-0');
                    setTimeout(function(){
                        $('.slide-in-products-container').remove();
                    }, 1000);
                    // console.log('ummm');
                    indpplDelLoading();
                    indpplAddProduct(type);
                }else{
                    // console.log(e);
                    array = JSON.parse(e);
                    // console.log(array);
                    // console.log(array['console']);
                    
                    if(array['pack_id_array']){
                        var count = 0;
                        $('.indppl-size-selected').each(function(){
                            if($(this).data('id') == "0"){
                                // console.log($(this).data('size'));
                                $(this).data('id', array['pack_id_array'][count]);
                                count++;
                            }
                        })
                    }
                    if(product_id == 'new'){
                        $('.product-create-product').children().last().attr('value', array['product_id']);
                    }
                    $('.product-create-app-rates-chart-container').empty();
                    $('.product-create-app-rates-chart-container').append(array['app_rates']);
                    var units = indppl_get_units(array['dryliquid']);
                    // console.log(units);
                    $('.indppl-product-create-chart-app-unit').each(function(){
                        var select = $(this).data('unit');
                        var elem = $(this);
                        // console.log(select);
                        $.each(units, function(index, value){
                            var name = value;
                            // if(value == 'qt-d' || value == 'qt-l'){
                            //     name = 'Quart';
                            // }
                            if(select == index || (select == 'tbl' && index == 'tbls')){
                                selected = `selected`;
                            }else{
                                selected = ``;
                            }
                            $(elem).append('<option class="indppl-product-create-chart-app-unit-option" value="' + index + '" ' + selected + '>' + name + '</option>');
                        });
                        // console.log(unit);
                    });
                    var bagunits = indppl_get_units('bag');
                    $('.indppl-product-create-chart-bag-unit').each(function(){
                        var select = $(this).data('unit');
                        var elem = $(this);
                        // console.log(select);
                        $.each(bagunits, function(index, value){
                            var name = value;
                            // if(value == 'qt-d' || value == 'qt-l'){
                            //     name = 'Quart';
                            // }
                            if(select == index || (select == 'tbl' && index == 'tbls')){
                                selected = `selected`;
                            }else{
                                selected = ``;
                            }
                            $(elem).append('<option class="indppl-product-create-chart-bag-unit-option" value="' + index + '" ' + selected + '>' + name + '</option>');
                        });
                        // console.log(unit);
                    });
                    if($(elem).is('#product-create-next')){
                        $('.product-create-app-rates-chart-container').slideToggle();
                        $('.product-create-first-part-container').slideToggle();
                        $('.product-create-first-part-container').hide();
                        $('.product-create-product').hide();
                        $('.product-create-brand').hide();
                    }
                    if($(elem).is('#product-create-submit-exit')){
                        // getProductInfo();
                        $('.slide-in-products-container').removeClass('left-0');
                        setTimeout(function(){
                            $('.slide-in-products-container').remove();
                        }, 1000);
                    }
                    if($(elem).is('#product-create-submit')){
                        $('.slide-in-products-container').remove();
                        indpplAddProduct(type);
                    }
                    if($(elem).is('.product-create-submit')){
                        // setTimeout(function(){
                            getProductInfo();
                        // }, 10000);
                    }
                    if(array['default']){
                        $('.slide-in-products-inside-container').append(array['default']);
                    }
                    if(array['update']){
                        $(array['update']).each(function(){
                            var id = $(this)[0];
                            // console.log(id);
                            $('.bag-apprates-container-title').each(function(){
                                if($(this).data('id') == id){
                                    $(this).addClass('color-red');
                                }
                            })
                        })
                    }
                    indpplDelLoading();
                }
                $('#container-select-form').removeClass('ind-first-time');
            }
        });

    });

    $('body').on('click', '.product-create-submit-back', function(e){
        e.preventDefault();
        $('.product-create-app-rates-chart-container').slideToggle();
        $('.product-create-first-part-container').slideToggle();
        $('.indppl-size-selected').removeClass('indppl-new-package');
        $('.create-product-header-instructions').show();
        var is_default = $('#indppl-ground-default-product').data('default');
        if(is_default == 1){
            $('.indppl-add-product-fraction-bag').hide();
        }
    })

    $('body').on('click', '.indppl-product-edit', function(e){
        e.preventDefault();
        var store_id = $(this).data('store');
        var type = $(this).data('type');
        var product_id = $(this).data('product');
        // console.log('edit');
        indpplEditProduct(type, store_id, product_id);
    })

    $('body').on('click', '.indppl-product-delete', function(e){
        e.preventDefault();
        // var load = indpplAddSmallLoading();
        // $(this).parent().parent().append(load);
        var store_id = $(this).data('store');
        var type = $(this).data('type');
        var product_id = $(this).data('product');
        var elem = JSON.stringify($(this));
        var brand = $(this).closest('tr').children().first().text();
        var product = $(this).closest('tr').children().first().next().text();
        
        $('body').prepend("<div class='indppl-loading-background'><div class='store-delete-modal'><div class='store-delete-modal-inside'><h4 class='store-delete-header'>You are about to delete</h4><h4 class='store-delete-header'>" + brand + " " + product + "</h4><h3 class='store-delete-header'>Are you Sure?</h3><div class='ind-flex store-delete-button-container'><a href='#' class='indppl-button button-primary indppl-delete-product-yes' data-store=" + store_id + " data-type=" + type + " data-product="+ product_id + ">YES</a><a href='#' class='indppl-button button-primary indppl-delete-product-no'>NO</a></div></div></div></div>");
    })
    
    $('body').on('click', '.indppl-delete-product-yes', function(e){
        e.preventDefault();
        indpplAddLoading();
        var store_id = $(this).data('store');
        var type = $(this).data('type');
        var product_id = $(this).data('product');
        var elem = $('.indppl-product-delete[data-product=' + product_id + ']');
        var version_check = 1.0;
        
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_remove_package_from_store_ajax',
                product_id: product_id,
                store_id: store_id,
                type: type,
                version_check: version_check,
            },
            type: 'POST',
            success: function(e){
                $(elem).closest('tr').remove();
                // console.log(e);
                indpplDelLoading();
            }
        })
    });

    $('body').on('click', '.indppl-delete-product-no', function(e){
        $('.indppl-loading-background').remove();
    });

    $('body').on('change', '.some-kind-of-wonderful', function(e){
        e.preventDefault();
        if($('#product-create-fraction-bag').is(':checked')){
            updateBagAppRates($(this));
        }else{
            updateAppRates($(this));
        }
    })

    $('body').on('click', '.indppl-add-product-pots-btn', function(e){
        e.preventDefault();
        var type = $(this).data('type');
        indpplAddProduct(type);
    })

    $('body').on('click', '.product-create-pots-submit', function(e){
        e.preventDefault();
        indpplAddLoading();
        var required = true;
        if($('.indppl-add-product-name').val() == ""){
            $('.indppl-add-product-name').after("<span class='indppl-form-required margin-left-10 margin-top-20 color-red'>Required</span>");
            required = false;
        }
        if(!$('.indppl-size-selected')[0]){
            $('.indppl-product-create-size-num-inside-container').append("<span class='indppl-form-required margin-left-10 color-red'>Required</span>");
            required = false;
        }
        if(required == false){
            indpplDelLoading();
            return;
        }
        var store_id = $('#store-id').val();
        var type = $('#indppl-modal-product-type').val();
        var brand = $('#product-create-brand').val();
        var product_id = $('#product-create-product').val();
        var product_unit = $('#product-create-standard-unit').data('unit');
        if(!product_unit){
            product_unit = $('.indppl-size-selected').data('unit');
        }
        var product_dryliquid = $('.product-create-dry-wet').val();
        var product_input = $("#product-create-form").find('input').filter('.some-kind-of-wonderful').serializeArray();
        // var product_select = $("#product-create-form").find('sel ect').filter('.some-kind-of-wonderful').serializeArray();
        var product_name = $('.indppl-add-product-name').val();
        var cups_num = $('.indppl-product-create-cups-num').val();
        var cups_unit = $('#product-create-5-cups').children("option:selected").val();
        var elem = $(this);
        var fraction = false;
        if($('#product-create-fraction-bag').is(':checked')){
            fraction = true;
        }
        var filler = false;
        if($('#indppl-add-product-bulk-filler').is(":checked")){
            filler = true;
        }
        var blend = false;
        if($('#indppl-add-product-additive-blend').is(":checked")){
            blend = true;
        }
        var surface = false;
        if($('#indppl-add-product-additive-surface').is(":checked")){
            surface = true;
        }
        var package_array = [];
        var package_remove = [];
        var new_pack = {};
        // console.log("filler: " + filler);
        // console.log("blended: " + blend);
        // console.log("surface: " + surface);
        var i = 0;
        var version_check = 1.0;
        $('.indppl-product-create-size-btn').each(function(){
            if($(this).hasClass('indppl-size-selected')){
                if($(this).hasClass('indppl-new-package')){
                    new_pack[i] = {};
                    new_pack[i]['size'] = $(this).data('size');
                    new_pack[i]['unit'] = $(this).data('unit');
                    new_pack[i]['name'] = brand + " " + product_name + " " + $(this).data('size') + $(this).data('unit');
                    i++;
                }else{
                    package_array.push($(this).data('id'));
                }
            }else{
                if($(this).hasClass('indppl-non-default-package')){
                    package_remove.push({'id': $(this).data('id')})
                }else{
                    package_remove.push($(this).data('id'));
                }
            }
        })
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_save_pots_product_ajax',
                product_id: product_id,
                store_id: store_id,
                type: type,
                brand: brand,
                package_array: package_array,
                package_remove: package_remove,
                product_unit: product_unit,
                product_input: product_input,
                product_dryliquid: product_dryliquid,
                new_pack: new_pack,
                cups_num: cups_num,
                filler: filler,
                blend: blend,
                surface: surface,
                cups_unit: cups_unit,
                fraction: fraction,
                product_name: product_name,
                version_check: version_check,
            },
            type: 'POST',
            success: function(e){
                // var array = JSON.parse(e);
                // console.log(array['console']);
                // console.log(e);
                if($(elem).attr('id') == 'product-create-pots-finish'){
                    getProductInfo();
                    $('.slide-in-products-container').removeClass('left-0');
                    setTimeout(function(){
                        $('.slide-in-products-container').remove();
                        getAppRates(type);
                    }, 1000);
                    indpplDelLoading();
                }else{
                    getProductInfo();
                    $('.slide-in-products-container').removeClass('left-0');
                    setTimeout(function(){
                        $('.slide-in-products-container').remove();
                        indpplAddProduct(type);
                    }, 1000);
                    indpplDelLoading();
                }
            }
        });
    });
    $('body').on('click', '.indppl-product-pots-edit', function(e){
        e.preventDefault();
        var store_id = $(this).data('store');
        var type = $(this).data('type');
        var product_id = $(this).data('product');
        // console.log('edit');
        indpplEditProduct(type, store_id, product_id);
    });

    $('body').on('click', '.indppl-application-rates-pots-btn', function(e){
        e.preventDefault();
        var type = $(this).data('type');
        getAppRates(type);
    })

    $('body').on('change', '.pots-apprates-filler', function(e){
        get100Percent();
    })

    greyOutAllUnchecked();
    // same as above but it checks on load.
    check_on_load_and_click();
    check_on_load();


    // GUIDES RELATED JS

    $("body").on('click', '.edit-guides', function (e) {

        e.preventDefault();

        $('body').prepend("<div class='slide-in-products-container'><div class='container pad-top-3'><a href='#' class='modal-close'>X</a></div></div>");
        setTimeout(function () {
            $('.slide-in-products-container').addClass('left-0');
            indpplAddLoading('.slide-in-products-container', 'grey', 'grey', 'white-bg-for-loading');
        }, 20);

        var target = $(this).data('target');
        var storeid = $(this).data('storeid');
        var version_check = 1.0;
        $.ajax({
            url: indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_setup_guide_forms_ajax',
                form: target,
                store: storeid,
                version_check: version_check,
            },
            type: 'POST',
            success: function (response) {
               $('.indppl-loading-background').remove();
               $('.slide-in-products-container .container').append(response); 
            }
        });
    });


    // Toggle planting guide sections
    $("body").on('click', '.planting-guide-sections .guide-controls', function(e){
        e.preventDefault();
        $(window).scrollTop(0);
        var target = $(this).data('target');
        var header = $(this).data('header');
        // console.log(header);
        $(this).parents('.planting-guide-options').slideToggle();
        $('.' + target).slideToggle();
        var num = 200;
        $('.planting-guide-content').children().each(function(){
            if($(this).prev().prev().prev().attr('id') == header){
                return false;
            }else{
                num += $(this).outerHeight(true);
            }
        });
        $('.overflow').scrollTop(num);
        
    });

    $("body").on('click', '.planting-guide-instructions label', function() {

        var theInput = $(this).prev('input');
        console.log(theInput.data('content'));
        var content = $("#" + theInput.data('content')).html();
        var image = $("#" + theInput.data('content') + "-image").attr('src');
        console.log(image);
        content = content + "<img src='" + image + "' class='indppl-step-img'>";

        if(content == ''){
            image = $("#" + theInput.data('target') + "-uploaded").html();
            content = '<p>' + $('#' + theInput.data('content')).val() + '</p>' + image;
        }
        var target = theInput.data('target');
        $("#" + target).html(content);
        var products = theInput.parents('ul').data('products');
        productsToStep(products);
    });

    $('body').on('change', '.planting-guide-options input[type=checkbox], .planting-guide-options textarea', function() {
        var products = $(this).parents('.step-product-select').attr('id');
        productsToStep(products);
    });

    $("body").on('change', 'textarea', function(){
        var section = $(this).data('target');
        var container = $(this).parent().prev('.planting-guide-option-input');
        var content = $(this).val();
        var img = $('#' + section + '-uploaded img').attr('src');
        var image = '';
        if(img != undefined && img != ''){
            image = "<img src='" + img + "' class='custom-image'>";
        }
        container.find('input').prop('checked', true);
        $("#" + section + " > p").remove();
        $("#" + section + " ").prepend("<p>" + content + "</p>");
        $("#" + section).find("img").remove();
        $("#" + section + " > p").after(image);
    });

    //Upload custom images
    $('body').on('change', '.indppl-custom-guide-instructions input[type=file]', function(e){
        indpplAddLoading();
        var target = $(this).data('target');
        // console.log(target);
        var option = $(this).data('option');
        var section = $(this).data('section');
        var fd = new FormData();
        var file = $(this).prop('files')[0];
        // console.log(section);
        fd.append('file', file);
        fd.append('version_check', 1.0);
        fd.append('action', 'indppl_upload_guide_image_ajax');
        $.ajax({
            url: indppl_ajax.ajaxurl,
            // dataType: 'json',
            method: 'POST',
            contentType: false,
            processData: false,
            data: fd,
            // processData: true,
            type: 'POST',
            success: function (response) {
                // console.log(response);
                indpplDelLoading();
                var image = "<img src='" + response + "' class='custom-image'>";
                $(target).html(image);
                $(section).find(".custom-image").remove();
                $(section + " > p").after(image);
                $(option).prop('checked', true);
                $(option).parents('ul').find(".instructions-content").removeClass('active');
                $(option).parents('li').find(".instructions-content").addClass('active');
            }
        });
    });

    // Save planting guide content
    $('body').on('click', '#guide-save', function(e){
        e.preventDefault();
        e.stopPropagation();
        var type = $('#planting-guide').data('type');
        var steps = new Array();
        var step = '';
        var content = '';
        var description = '';
        var title = '';
        var custom = false;
        var image = '';
        $('.planting-guide-options').each(function(){
            content = '';
            step = $(this).data('step');
            title = $(this).data('title');
            title = $('#' + title).text();
            $(this).find('.guide-step-description').each(function(){
                custom = false;
                if($(this).is(':checked')){
                    if ($(this).data('custom') == true) {
                        custom = true;
                        content = $('#' + $(this).data('content')).val();
                        image = $("#" + $(this).data('target') + "-uploaded img").attr('src');
                        // console.log(image);
                    } else {
                        content =  $(this).data('content');
                    }
                }
            });
            var products = new Array();
            var id = '';
            var instructions = '';
            $(this).find('input[type=checkbox]').each(function() {
                if($(this).is(':checked')){
                    id = $(this).data('product');
                    instructions = $('#' + $(this).data('instructions')).val();
                    products.push({id : id, instructions : instructions});
                }
            });
            if(!custom){
                description = $('#' + content + " p").text();
                image = $('#' + content + " img").attr('src');
            } else {
                description = content;
            }
            steps.push({title: title, step : step, description : description, products : products, image : image });
        });
        
        $.ajax({
            url : indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data : {
                action : 'indppl_save_guide_ajax',
                steps : steps,
                store : $('#planting-guide').data('store'),
                type : $('#planting-guide').data('type')
            },
            success: function (results){
                closeModal();
            }
        });
    });

    function productsToStep(products){
        var productsThisStep = new Array();
        var section = '';
        var product = '';
        var productStepInstructions = '';
        $("#" + products + " input:checkbox:checked").each(function(e) {
            product = $(this).data('product');
            section = $(this).data('target');
            label = $(this).next('label').text();
            productStepInstructions = $("#" + $(this).data('instructions')).val();
            productsThisStep.push({product : product, instructions: productStepInstructions, label: label });
        });
        
        $.ajax({
            url: indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_guide_products_ajax',
                products : productsThisStep,
            },
            type: 'POST',
            success: function (results) {
                $('#' + section + '-products').html(results);
            }
        });

        $('#' + section).append(productsThisStep);
    }

    $('body').on('click', '#get-planting-guide', function(e){
        e.preventDefault();
        
        var email = $('input[name=email]').val();
        if(validateEmail(email) && email != ''){
            indpplAddLoading();
            var store = $(this).data('store');
            var plants = $(this).data('plants');
            var list = $(this).data('list');
            var ground = $(this).data('ground');

    
            $.ajax({
                url : indppl_ajax.ajaxurl,
                dataType: 'text',
                method: 'POST',
                data : {
                    action : 'indppl_build_guide_ajax',
                    nonce : indppl_ajax.guide_nonce,
                    store : store,
                    plants : plants,
                    list : list,
                    ground: ground,
                    email : email
                }, 
                success : function(response) {
                    // console.log(response);
                    $('.container').last().prepend('<h3 class="planting-guide-sent-text">Your planting guide has been sent to your email.</h3>');
                    $('.keep-going').hide();
                    $('.email-address-add').hide();
                    $('#get-planting-guide').hide();
                    indpplDelLoading();
                }
            });
        }else{
            alert('Please enter a valid email address!');
        }

    })

    $('body').on('click', '.sponsor-link', function(e){
        e.preventDefault();
        // console.log('triggered');
        var content = $(this).parent().find('.sponsor-copy').html();
        var brand = $(this).siblings('.product-name').find('.brand').text();
        var product = $(this).siblings('.product-name').find('.product').text();
        // console.log(product);
        var image = $(this).parents('.guide-product-template').find('.product-guide-image').html();
        if(!image || image == 'undefined'){
            content = $(this).next('.sponsor-copy').html();
            image_url = $(this).parent().find('img').attr('src');
            if(!image_url || image_url == 'undefined'){
                image = '';
            } else {
                image = "<img src='" + image_url + "'>";
            }
        }
        if(!brand || brand == 'undefined'){
            brand = $(this).parents('.sponsorship').next().children().first().html();
        }
        if(!product || product == 'undefined'){
            product = $(this).parents('.sponsorship').next().children().last().html();
        }
        // console.log(brand);
        // console.log(product);
        $('body').prepend("<div class='sponsored-modal'>" + image + "<p class='brand'>" + brand + "</p><h4>" + product + "</h4><p>" + content + "</p></div>");
    });

    $(document).click(function (event) {
        //if you click on anything except the modal itself or the "open modal" link, close the modal
        if (!$(event.target).closest(".sponsored-modal, .sponsor-link").length) {
            $(".sponsored-modal").remove();
        }
    });

    $('body').on('click', '.pots-apprates-save-btn', function(e){
        e.preventDefault();
        if($('.pots-apprates-filler-total').text() != '100'){
            alert('You need your mix to equal 100%!');
        }else{
            indpplAddLoading();
            var store_id = $('#store-id').val();
            var fill_array = {};
            var version_check = 1.0;
            var type = $('#pots-and-beds-type').data('type');
            $('.pots-apprates-filler').each(function(){
                fill_array[$(this).data('product')] = {'amount': $(this).val()};
                if($(this).parent().parent().find('.pots-apprates-filler-radio').is(':checked')){
                    fill_array[$(this).data('product')]['primary'] = true;
                }else{
                    fill_array[$(this).data('product')]['primary'] = false;
                }
            });
            var blend_array = {};
            $('.blended-num').each(function(){
                blend_array[$(this).data('product')] = {'amount': $(this).val()};
                blend_array[$(this).data('product')]['unit'] = $(this).parent().parent().find('.blended-select').val();
            });
            // console.log(blend_array);
            var surface_array = {}
            $('.surface-num').each(function(){
                surface_array[$(this).data('product')] = {'amount': $(this).val()};
                surface_array[$(this).data('product')]['unit'] = $(this).parent().parent().find('.surface-select').val();
                surface_array[$(this).data('product')]['per-sqft'] = $(this).parent().parent().find('.surface-select-sqft').val();
            });
            var each_array = {};
            $('.pots-apprates-each-num-8').each(function(){
                var product = $(this).data('product');
                each_array[product] = {'small': $(this).val()};
                each_array[product]['medium'] = $(this).parent().parent().find('.pots-apprates-each-num-8-24').val();
                each_array[product]['large'] = $(this).parent().parent().find('.pots-apprates-each-num-24').val();
            });
            // console.log(type);
            $.ajax({
                url:indppl_ajax.ajaxurl,
                dataType: 'text',
                method: 'POST',
                data: {
                    action: 'indppl_save_pot_apprates_ajax',
                    store_id: store_id,
                    fill_array: fill_array,
                    blend_array: blend_array,
                    surface_array: surface_array,
                    each_array: each_array,
                    type: type,
                    version_check: version_check,
                },
                type: 'POST',
                success: function(e){
                    // console.log(e);
                    indpplDelLoading();
                    $('.slide-in-products-container').removeClass('left-0');
                    setTimeout(function(){
                        $('.slide-in-products-container').remove();
                    }, 1000)
                }
            });
        }

        
    })
    $('body').on('click', '.indppl-add-sponsor-link', function(e){
        $('body').prepend("<div class='slide-in-sponsor-container'><div class='container pad-top-3'><a href='#' class='sponsor-modal-close'>X</a></div></div>");
        setTimeout(function () {
            $('.slide-in-sponsor-container').addClass('left-0');
            indpplAddLoading('.slide-in-sponsor-container', 'grey', 'grey', 'white-bg-for-loading');
        }, 20);
        var version_check = 1.0;
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_get_sponsorship',
                version_check: version_check,
            },
            type: 'POST',
            success: function(e){
                // console.log(e);
                $('.slide-in-sponsor-container .container').append(e); 
                indpplDelLoading();
            }
        });
    
    });

    $('body').on('click', '.empty-filled .pots', function(){

        var check = $(this).val();
        if(check == 'partial'){
            $(this).parents('.indppl-flex').next('.inches-needed').removeClass('hide');
        } else {
            $(this).parents('.indppl-flex').next('.inches-needed').addClass('hide');
            $(this).parents('.indppl-flex').next('.inches-needed').children('input').val('0');
        }

    });

    $('body').on('submit', '#store-management-form', function(e){
        var phone = validatePhone($('#phone').val());
        // console.log(phone);
        if(phone == false){
            e.preventDefault();
            alert('Phone number must be 10 digits.');
            $('#phonenumber').val('');
            $('#phonenumber').focus();
        }
        var email = validateEmail($('#store-email').val());
        if(email == false){
            e.preventDefault();
            alert('Please enter a valid email address.');
            $('#store-email').val('');
            $('#store-email').focus();
        }
    })

    $('body').on('submit', '#add-sponsor-form', function(e){
        e.preventDefault();
        indpplAddLoading();
        var fd = new FormData();
        var file = $('#add-sponsor-img-file').prop('files')[0];
        fd.append('file', file);
        fd.append('brand_id', $('#indppl-add-sponsor-brand-select').val());
        fd.append('product_id', $('#indppl-add-sponsor-product-select').val());
        fd.append('url', $('#add-sponsor-url').val());
        fd.append('copy', $('#add-sponsor-copy').val());
        fd.append('action', 'indppl_save_sponsorship');
        fd.append('form', $(this).serializeArray());
        fd.append('id', $('#sponsor-save').data('id'));
        fd.append('img', $('#add-sponsor-img').attr('src'));
        fd.append('version_check', 1.0);
        $.ajax({
            url:indppl_ajax.ajaxurl,
            // dataType: 'json',
            method: 'POST',
            contentType: false,
            processData: false,
            data: fd,
            // processData: true,
            type: 'POST',
            success: function(e){
                array = JSON.parse(e);
                // console.log(array['refresh']);
                $('.indppl-add-sponsor-main-container').html(array['refresh']);
                $('#add-sponsor-img').attr('src', array['img']);
                indpplDelLoading();
                $('.sponsor-modal-close').hide();
                $('.slide-in-sponsor-container').removeClass('left-0');
                setTimeout(function(){
                    $('.slide-in-sponsor-container').remove();
                }, 1000);
            }
        });
    })
    $('body').on('click', '.indppl-edit-sponsor-link', function(e){
        e.preventDefault();
        $('body').prepend("<div class='slide-in-sponsor-container'><div class='container pad-top-3'><a href='#' class='sponsor-modal-close'>X</a></div></div>");
        setTimeout(function () {
            $('.slide-in-sponsor-container').addClass('left-0');
            indpplAddLoading('.slide-in-sponsor-container', 'grey', 'grey', 'white-bg-for-loading');
        }, 20);
        var id = $(this).data('id');
        var brand_id = $(this).data('brand');
        var product_id = $(this).data('product');
        var version_check = 1.0;
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_get_sponsorship',
                version_check: version_check,
                id: id,
                brand_id: brand_id,
                product_id: product_id,
            },
            type: 'POST',
            success: function(e){
                // console.log(e);
                $('.slide-in-sponsor-container .container').append(e); 
                indpplDelLoading();
            }
        });
    })
    $('body').on('click', '#indppl-delete-sponsor-btn', function(e){
        e.preventDefault();
        indpplAddLoading();
        var id = $(this).data('id');
        var product_id = $('#indppl-add-sponsor-product-select').val();
        var version_check = 1.0;
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_delete_sponsorship',
                version_check: version_check,
                id: id,
                product_id: product_id,
            },
            type: 'POST',
            success: function(e){
                // console.log(e);
                $('.indppl-add-sponsor-main-container').html(e);
                indpplDelLoading();
                $('.sponsor-modal-close').hide();
                $('.slide-in-sponsor-container').removeClass('left-0');
                setTimeout(function(){
                    $('.slide-in-sponsor-container').remove();
                }, 1000);
            }
        });
    })
    $('body').on('click', '.sponsor-view-count-btn', function(){
        $(this).parent().next().slideToggle();
    })


    $('body').on('keypress', function(e) {
        if(e.which == 13 && $('.slide-in-products-inside-container').length > 0){
            e.preventDefault();
            e.stopPropagation();
        }
    });

    $('body').on('change', '.product-create-dry-wet-container', function(){
        var dry_wet = ($('.product-create-dry-wet:checked').val());
        $('#product-create-standard-unit-add').empty();
        units = indppl_get_units(dry_wet);
        // console.log(array['dry_wet'][2]);
        $.each(units, function(index, value){
            // console.log(value);
            var name = value;
            // if(value == 'qt-d' || value == 'qt-l'){
            //     name = 'Quart';
            // }
            $('.product-create-standard-unit-add').append('<option class="product-create-standard-unit-option" value="' + index + '">' + name + '</option>');
        })
    });

    // geo radius selector
    $('body').on('change', '#geo-radius', function(){
        var radius = $(this).val();
        if(radius == 'custom'){
            $('#geo-radius-custom').removeClass('hide');
        }else{
            if($('#geo-radius-custom').is(':visible')){
                $('#geo-radius-custom').addClass('hide');
            }
            $('#geo-radius-custom').val(radius);
        }
    });

    //Authorize Store Duplication Form
    $("body").on('click', '.indppl-store-auth', function (e) {

        $('body').prepend("<div class='slide-in-products-container'><div class='container pad-top-3'><a href='#' class='modal-close'>X</a></div></div>");
        setTimeout(function () {
            $('.slide-in-products-container').addClass('left-0');
            indpplAddLoading('.slide-in-products-container', 'grey', 'grey', 'white-bg-for-loading');
        }, 20);
        var storeid = $(this).data('store');
        var version_check = 1.0;
        $.ajax({
            url: indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_dup_auth_form_ajax',
                store: storeid,
                version_check: version_check,
            },
            type: 'POST',
            success: function (response) {
                $('.indppl-loading-background').remove();
                $('.slide-in-products-container .container').append(response);
            }
        });
    });

    // Add authorized duplicator
    $("body").on('click', '#indppl-add-auth-user', function (e) {
        e.preventDefault();
        indpplAddLoading();
        var email = $('#auth-email').val();
        var storeid = $('#auth-email').data('store'); 
        var version_check = 1.0;
        $.ajax({
            url: indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_auth_users_ajax',
                store: storeid,
                email: email,
                process: 'add',
                version_check: version_check,
            },
            type: 'POST',
            success: function (response) {
                $('.indppl-loading-background').remove();
                $('#authorized-users').append(response);
                $('#auth-email').val('');
            }
        });
    });

    // Remove authorized duplicator
    $("body").on('click', '.remove-auth', function (e) {
        e.preventDefault();
        // console.log('sdfsfsdfsdf');
        indpplAddLoading();
        var auth = $(this).data('id');
        var entry = $(this);
        var version_check = 1.0;
        $.ajax({
            url: indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_auth_users_ajax',
                auth: auth,
                process: 'remove',
                version_check: version_check,
            },
            type: 'POST',
            success: function (response) {
                // console.log(response);
                $('.indppl-loading-background').remove();
                if(response == 'success'){
                    entry.parent().remove();
                } else {
                    alert('There was an error. Please try again.');
                }
            }
        });
    });


    //Duplicate Store Process
    $("body").on('click', '.indppl-duplicate-store', function (e) {

        e.preventDefault();
        $('body').prepend("<div class='slide-in-products-container'><div class='container pad-top-3'><a href='#' class='modal-close'>X</a></div></div>");
        setTimeout(function () {
            $('.slide-in-products-container').addClass('left-0');
            indpplAddLoading('.slide-in-products-container', 'grey', 'grey', 'white-bg-for-loading');
        }, 20);
        var storeid = $(this).data('store');
        var version_check = 1.0;
        $.ajax({
            url: indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_copy_store_form_ajax',
                store: storeid,
                version_check: version_check,
            },
            type: 'POST',
            success: function (response) {
                $('.indppl-loading-background').remove();
                $('.slide-in-products-container .container').append(response);
            }
        });
    });

    $('body').on('focus', '.duplicate-store-required', function(){
        $(this).parent().find('.duplicate-required-fill').remove();
    });
    $('body').on('focusout', '.duplicate-store-required', function(){
        if(!$(this).val() > 0){
            $(this).parent().append('<span class="duplicate-required-fill">Required</span>');
        }
    });

    $("body").on('click', '#store-duplicate', function (e) {

        e.preventDefault();
        // console.log($("#store-duplication-form input[name=billing]").is(':checked'));
        // Billing confirmation
        // if (!$("#store-duplication-form input[name=billing]").is(":checked") ){
        //     alert("Please indicate that you understand that your subscription will be increased to reflect the new store.");
        //     return;
        // }
        $('.duplicate-required-fill').remove();
        $('.duplicate-store-required').each(function(){
            if(!$(this).val().length > 0){
                $(this).parent().append('<span class="duplicate-required-fill">Required</span>');
            }
        })
        var phone = validatePhone($('#phone').val());
        if(phone == false){
            alert('Phone number must be 10 digits.');
            $('#phonenumber').val('');
            $('#phonenumber').focus();
        }
        var email = validateEmail($('#store-email').val());
        if(email == false || !$('#store-email').val() > 0){
            alert('Please enter a valid email address.');
            $('#store-email').val('');
            $('#store-email').focus();
            email = false;
        }
        if(!$('.duplicate-required-fill').length > 0 && phone == true && email == true){
            indpplAddLoading('.slide-in-products-container', 'grey', 'grey', 'white-bg-for-loading');
            var storeid = $(this).data('store');
            var storeName = $('#store-duplication-form input[name=store-name]').val();
            var address1 = $('#store-duplication-form input[name=address1]').val();
            var address2 = $('#store-duplication-form input[name=address2]').val();
            var city = $('#store-duplication-form input[name=city]').val();
            var state = $('#store-duplication-form select[name=state]').val();
            var zip = $('#store-duplication-form input[name=zip]').val();
            var webURL = $('#store-duplication-form input[name=weburl]').val();
            var phone = $('#store-duplication-form input[name=phone]').val();
            var email = $('#store-duplication-form input[name=store-email]').val();
            var storeName = $('#store-duplication-form input[name=store-name]').val();
            var version_check = 1.0;
            $.ajax({
                url: indppl_ajax.ajaxurl,
                dataType: 'text',
                method: 'POST',
                data: {
                    action: 'indppl_duplicate_store_ajax',
                    store: storeid,
                    storeName: storeName,
                    address1: address1,
                    address2: address2,
                    city: city,
                    state: state,
                    zip: zip,
                    webURL: webURL,
                    phone: phone,
                    email: email,
                    version_check: version_check,
                },
                type: 'POST',
                success: function (response) {
                    // $('body').html(response);
                    location.reload();
                }
            });
        }
    });

    $('body').on('click', '.indppl-delete-store', function(e){
        e.preventDefault();
        var id = $(this).data('store');
        $('body').prepend("<div class='indppl-loading-background'><div class='store-delete-modal'><div class='store-delete-modal-inside'><h4 class='store-delete-header'>You are about to delete this store. You will be billed to the end of this period.</h4><h3 class='store-delete-header'>Are you Sure?</h3><div class='ind-flex store-delete-button-container'><a href='#' class='indppl-button button-primary delete-store-yes' data-store=" + id + ">YES</a><a href='#' class='indppl-button button-primary delete-store-no'>NO</a></div></div></div></div>");
    })

    $('body').on('click', '.delete-store-no', function(e){
        e.preventDefault();
        $('.indppl-loading-background').remove();
    })
    $('body').on('click', '.delete-store-yes', function(e){
        e.preventDefault();
        $('.indppl-loading-background').remove();
        indpplAddLoading();
        var id = $(this).data('store');
        var version_check = 1.0;
        $.ajax({
            url: indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_delete_store_ajax',
                id: id,
                version_check: version_check,
            },
            type: 'POST',
            success: function (response) {
                // console.log(response);
                $('.indppl-my-stores-container').replaceWith(response);
                indpplDelLoading();
            }
        });

    })
    $('body').on('click', '.indppl-live-store', function(e){
        e.preventDefault();
        indpplAddLoading();
        var id = $(this).data('store');
        var version_check = 1.0;
        var elem = $(this);
        $.ajax({
            url: indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_store_go_live_ajax',
                id: id,
                version_check: version_check,
            },
            type: 'POST',
            success: function (response) {
                // console.log(response);
                if(response){
                    $(elem).replaceWith("<a href='#' class='orange-text text-center indppl-store-deactivate' data-store=" + id + " style='display: block; margin - top: 5px;'>Deactivate</a>");
                    $('#status-' + id).removeClass('grey-text').addClass('green-text').text('Online');
                }
                indpplDelLoading();
            }
        });
    })

    $('body').on('click', '.indppl-store-deactivate', function(e){
        e.preventDefault();
        indpplAddLoading();
        var id = $(this).data('store');
        var version_check = 1.0;
        var elem = $(this);
        $.ajax({
            url: indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_store_deactivate_ajax',
                id: id,
                version_check: version_check,
            },
            type: 'POST',
            success: function (response) {
                // console.log(response);
                if(response){
                    $(elem).replaceWith("<a href='#' data-store=" + id + " class='orange-text text-center indppl-live-store' style='display: block; margin - top: 5px;'>Go Live</a>");
                    $('#status-' + id).removeClass('green-text').addClass('grey-text').text('Offline');
                }
                indpplDelLoading();
            }
        });
    })

    $('body').on('click', '#indppl-add-product-additive-blend', function(){
        $('#indppl-add-product-bulk-filler').prop('checked', false);
    })

    $('body').on('click', '#indppl-add-product-bulk-filler', function(){
        $('#indppl-add-product-additive-blend').prop('checked', false);
    })

    $('body').on('change', '.container-date', function(){
        var date = $(this).datepicker('getDate');
        var startformat = $.datepicker.formatDate('yymmdd', date);
        var count = 0;
        var overlap = false;
        var inside = false;
        $('.container-date').each(function(){
            var date = $(this).datepicker('getDate');
            var format = $.datepicker.formatDate('yymmdd', date);
            if(count == 0){
                if(parseInt(startformat) > parseInt(format)){
                    inside = true;
                }
                count++;
            }else{
                if(inside == true && parseInt(startformat) < parseInt(format)){
                    overlap = true;
                }
                inside = false;
                count = 0;
            }
        })
        if(overlap == true){
            $('body').prepend("<div class='indppl-loading-background indppl-date-overlap-modal-background'><div class='container-date-overlap-modal'><div class='container-date-overlap-modal-inside'><div class='indppl-x x-date-overlap-modal'>X</div><h3 class='container-overlap-header'>The seasons overlap and products from both seasons will be available during the overlap.</h3></div></div></div>");
        }

    })

    $('body').on('click', '.x-date-overlap-modal', function(){
        $('.indppl-loading-background').remove();
    })

    $('body').on('click', '.indppl-date-overlap-modal-background', function(){
        $('.indppl-loading-background').remove();
    })

    $('body').on('click', '.next-button', function(e){
        // e.preventDefault();
        $('.next-button-error').remove();
        $('.round-button-error').replaceWith("<p style='margin-bottom: 35px;'></p>");
        var pots_load = true;
        var ground = false;
        var required_array = [];
        var beds_required_array = [];
        var over_height = false;
        var over_height_elem;
        $('.ground-shopping-list').find('.rounded-input').each(function(){
            // console.log($.isNumeric($(this).val()));
            // console.log(ground);
            if($(this).val() > 0 && $.isNumeric($(this).val()) && ground == false){
                ground = true;
                return false;
            }
        })
        var pots_empty = true;
        $('.pots-form').find('.rounded-input.pots').each(function(){
            var count = 0;
            $(this).parent().parent().find('.rounded-input2').each(function(){
                if($(this).val() >= 1){
                    count++;
                    pots_empty = false;
                }else{
                    required_array.push($(this));
                }
            })
            if($(this).val() > 0){
                pots_empty = false;
            }else{
                pots_load = false;
                if(pots_empty == false){
                    required_array.push($(this));
                }
            }
            if(count != 3){
                pots_load = false;
                return false;
            }
        })
        // e.preventDefault();
        var pots_partial_empty = true;
        $(".indppl-pots-partial").each(function(){
            if($(this).is(':checked')){
                var pots_need = $(this).parent().parent().parent().find('.rounded-input3').val();
                // console.log(pots_need);
                if(!pots_need >= 1 && pots_need != null){
                    required_array.push($(this).parent().parent().parent().find('.rounded-input3'));
                    
                    pots_load = false;
                    pots_partial_empty = false;
                    return false;
                }else{
                    var height = $(this).parents('.tacos').find('.height').val();
                    // console.log(height);
                    // console.log(pots_need);
                    if(pots_need > height){
                        over_height = true;
                        over_height_elem = $(this).parent().parent().parent().find('.rounded-input3');
                        // console.log(over_height_elem);
                    }
                }
            }
        })
        if(pots_partial_empty == false){
            pots_empty = false;
        }

        var beds_load = true;
        var beds_empty = true;
        $('.rb-form').find('.rounded-input.beds').each(function(){
            // console.log($(this).val());
            var count = 0;
            $(this).parent().parent().find('.rounded-input2').each(function(){
                // console.log($(this).val());
                if($(this).val() >= 1){
                    count++;
                    beds_empty = false;
                }else{
                    beds_required_array.push($(this));
                }
            })
            if($(this).val() > 0){
                beds_empty = false;
            }else{
                beds_load = false;
                if(beds_empty == false){
                    beds_required_array.push($(this));
                }
            }
            if(count != 3){
                beds_load = false;
                return false;
            }
        })
        var bed_partial_empty = true;
        $(".indppl-beds-partial").each(function(){
            if($(this).is(':checked')){
                var beds_need = $(this).parent().parent().parent().find('.rounded-input3').val();
                if(!beds_need >= 1 && beds_need != null){
                    beds_required_array.push($(this).parent().parent().parent().find('.rounded-input3'));
                    // console.log('testing' + beds_need);
                    beds_load = false;
                    bed_partial_empty = false;
                    return false;
                }else{
                    var height = $(this).parents('.tacos').find('.height').val();
                    if(beds_need > height){
                        over_height = true;
                        over_height_elem = $(this).parent().parent().parent().find('.rounded-input3');
                    }
                }
            }
        })
        if(bed_partial_empty == false){
            beds_empty = false;
        }
        // console.log('ground: ' + ground + " Pots: " + pots_load + " beds: " + beds_load + " pots empty: " + pots_empty + " beds empty: " + beds_empty);
        if((ground == true && (pots_empty == true || pots_load == true) && (beds_load == true || beds_empty == true)) || 
        (pots_load == true && (beds_empty == true || beds_load == true)) ||
        (beds_load == true && (pots_empty == true || pots_load == true) && over_height == false)){
        }else{
            e.preventDefault();
            if(!ground && pots_empty && beds_empty){
                $(this).after('<p class="next-button-error">Nothing filled in</p>');
            }else if(pots_empty == false && beds_empty == true){
                $.each(required_array, function(index, value){
                    $(value).prev().replaceWith('<p class="round-button-error">Required</p>');
                })
                $(this).after('<p class="next-button-error">Fill in all required fields</p>');
            }else if(pots_empty == true && beds_empty == false){
                $.each(beds_required_array, function(index, value){
                    $(value).prev().replaceWith('<p class="round-button-error">Required</p>');
                })
                $(this).after('<p class="next-button-error">Fill in all required fields</p>');
            }else if(pots_empty == false && beds_empty == false){
                $.each(beds_required_array, function(index, value){
                    $(value).prev().replaceWith('<p class="round-button-error">Required</p>');
                })
                $.each(required_array, function(index, value){
                    $(value).prev().replaceWith('<p class="round-button-error">Required</p>');
                })
                $(this).after('<p class="next-button-error">Fill in all required fields</p>');
            }
            if(over_height == true){
                // console.log(over_height_elem);
                $(over_height_elem).prev().replaceWith('<p class="round-button-error">Cannot exceed height</p>')
            }

            // if(pots_load == false || beds_load == false){
            //     alert('You need to fill out each row you start completely.')
            // }else{
            //     alert('Fill out at least one of the plant types to continue.');
            // }
        }
    })

    $('body').on('mouseenter', '.rb-form', function(){
        if(!$(this).hasClass('pb-first')){
            $(this).append('<a href="#" class="pb-remove-button">Remove</a>');
        }
    })

    $('body').on('mouseleave', '.rb-form', function(){
        $(this).find('.pb-remove-button').remove();
    })

    $('body').on('mouseenter', '.pots-form', function(){
        if(!$(this).hasClass('pb-first')){
            $(this).append('<a href="#" class="pb-remove-button">Remove</a>');
        }
    })

    $('body').on('mouseleave', '.pots-form', function(){
        $(this).find('.pb-remove-button').remove();
    })

    $('body').on('click', '.pb-remove-button', function(e){
        e.preventDefault();
        $(this).parent().remove();
    })

    $('body').on('click', '.product-create-exit', function(e){
        e.preventDefault();
        closeModal();
        $('.modal-close').remove();
    })

    $('body').on('mouseenter', 'tr.indppl-containers-row', function(){
        // console.log($(this).find('.indppl-container-edit-title'));
        if($(this).find('.indppl-container-edit-title').length > 0){
            var id = $(this).find('.container-available').children(0).data('container');
            if(typeof id === 'undefined'){
                id = $(this).attr('id');
            }
            // console.log(id);
            $(this).last().children().last().append('<a class="container-delete" data-id=' + id + '>Delete</a>');
        }
    });

    $('body').on('mouseleave', 'tr.indppl-containers-row', function(){
        $('.container-delete').remove();
    })

    $('body').on('click', '.container-delete', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        $('body').prepend('<div class="del-container-message-container"><div class="del-container-message-inside-container"><h3>Are you sure  you want to delete this container?</h3><div class="ind-yes-no"><a href="#" class="del-container-yes" data-id=' + id + '>Yes</a><a href="#" class="del-container-no">No</a></div></div></div>');
    });

    $('body').on('click', '.del-container-no', function(e){
        e.preventDefault();
        $('.del-container-message-container').remove();
    });

    $('body').on('click', '.del-container-yes', function(e){
        e.preventDefault();
        indpplAddLoading();
        var id = $(this).data('id');
        if($.type(id) === "string"){
            console.log(id);
            $('#' + id).remove();
            $('.del-container-message-container').remove();
            indpplDelLoading();
        }else{
            var store_id = $('#store-id').val();
            var version_check = 1.0;
            elem = $(this);
            $.ajax({
                url: indppl_ajax.ajaxurl,
                dataType: 'text',
                method: 'POST',
                data: {
                    action: 'indppl_delete_container_ajax',
                    id: id,
                    store_id: store_id,
                    version_check: version_check,
                },
                type: 'POST',
                success: function (response) {
                    $('#' + response + '-container-available').parents('.indppl-containers-row').remove();
                    $('.del-container-message-container').remove();
                    indpplDelLoading();
                }
            });
        }
    });

    $('body').on('click', '.indppl-green-button-not-selected', function(){
        $(this).parent().find('.indppl-green-button-selected').addClass('indppl-green-button-not-selected');
        $(this).parent().find('.indppl-green-button-selected').removeClass('indppl-green-button-selected');
        $(this).removeClass('indppl-green-button-not-selected').addClass('indppl-green-button-selected');
    });

    $('body').on('click', '.indppl-create-new-size', function(e){
        e.preventDefault();
        $('.indppl-product-create-size-num-inside-container').removeClass('indppl-hide');
        $(this).hide();
    })

    if($("#keep-going-container").length > 0) {
        setTimeout(function(){
            // $(document).scrollTop( $("#keep-going-container").offset().top ); 
            $('html, body').animate({scrollTop:$("#keep-going-container").offset().top}, 1200);
        }, 1000);
    }

    $('body').on('change', '.guide-product-select', function(e) {

        var fill = checkBox;

        if($(this).is(':checked')){
            fill = checkMark
        }

        $(this).parent('.planting-guide-products').find('.product-instructions-section').toggleClass('active');
        
        $(this).next('label').html(fill);
    });

    $('body').on('click', '.instructions-edit', function(e){
        e.preventDefault();
        if(indpplP){
            // Create a textarea & add the current text to it
            var theText = $(this).parent('.instructions-content-text').find('p');
            var theHeight = theText.height();
            var editTools = "<div class='indppl-text-editor indppl-flex hide'><textarea style='height: " + theHeight + "px; '></textarea><button class='indppl-btn guide-save'>Save</button ><button class='indppl-btn grey-bg'>Cancel</button></div>";
            
            $(this).parent('.instructions-content-text').prepend(editTools);
            
            var editorSection = $(this).parent('.instructions-content-text').find('.indppl-text-editor');
            var editor = $(this).parent('.instructions-content-text').find('.indppl-text-editor textarea');
            editor.val(theText.text());

            // Hide the current text
            theText.addClass('hide');
            $(this).addClass('hide');
            
            // Reveal textarea with save & cancel buttons
            editorSection.removeClass('hide');

        } else {
            // Sales Pitch
            var message = "<div class='store-delete-modal indppl-loading-background'><div class='store-delete-modal-inside'><h3>Feature Not Available</h3><p>This feature is not available at your current subscription level.</p><p>When you upgrade to pro you'll have access to edit this text, plus many other tools to boost your sales!<a href='/pricing' class='indppl-btn'>Upgrade Now</a> <a href='#' class='indppl-btn grey-bg indppl-delete-product-no'>Later</a></div></div>";
            $('body').append(message);
        }
    });

    $('body').on('click', '.indppl-text-editor button', function(){
        if($(this).hasClass('guide-save')){

            // Set the text
            var newContent = $(this).parent('.indppl-text-editor').find('textarea').val();
            var target = $(this).parents('ul').find('.indppl-custom textarea');
            target.val(newContent);

            // Copy the image

            var image = $(this).parents('.instructions-content').find('img').clone();
            $(this).parents('ul').find('.custom-image-container').html(image);

            $(this).parents('ul').find('.indppl-custom .planting-guide-option-input label').trigger('click');

            alert('Your changes will be saved to the custom section.');

        }

        // Cleanup
        $(this).parents('.instructions-content-text').find('p').removeClass('hide');
        $(this).parents('.instructions-content-text').find('.instructions-edit').removeClass('hide');
        $(this).parent('.indppl-text-editor').remove();

    });

    $('<div><label>Coupon Code</label><input type="text" id="temp-mepr_coupon_code-22" name="mepr_coupon_code" value="" /></div>').insertAfter('.mp-password-strength-area');
    $('#mepr_coupon_code-22').remove();
    
    // FUNCTIONS!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    $('body').on('click', '.indppl-apprate-primary-radio', function(){
        $('.apprate-circle-fill').addClass('hide');
        $(this).find('.apprate-circle-fill').removeClass('hide');
    });

    $('body').on('click', '.apprates-close', function(e){
        e.preventDefault();
        $('.slide-in-products-container').removeClass('left-0');
        setTimeout(function(){
            $('.slide-in-products-container').remove();
        }, 1000);
    })


    function bagControlsNEG(elem){
        hold_end = false;
        
        var num = $(elem).parent().parent().find('.indppl-product-create-chart-app-rate-num').first().val();
        var unit = $(elem).parent().parent().find('.indppl-product-create-chart-bag-unit').first().val();
        num = parseFloat(num) - .01;
        var number = parseFloat(num).toFixed(2);
        if (number < 0.01){
            number = 0;
        }
        $(elem).parent().parent().find('.indppl-product-create-chart-app-rate-num').first().val(number);
        $(elem).parent().parent().find('.indppl-bag-rate-num').first().text(number);
        isDown = -.01;
    };

    function bagControlsPOS(elem){        
        hold_end = false;
        var num = $(elem).parent().parent().find('.indppl-product-create-chart-app-rate-num').first().val();
        var unit = $(elem).parent().parent().find('.indppl-product-create-chart-bag-unit').first().val();
        num = parseFloat(num) + .01;
        var number = parseFloat(num).toFixed(2);
        if (number < 0.01){
            number = 0;
        }
        $(elem).parent().parent().find('.indppl-product-create-chart-app-rate-num').first().val(number);
        $(elem).parent().parent().find('.indppl-bag-rate-num').first().text(number);
        isDown = .01;
    };

    var isDown = 0.0;
    var delay = 350;
    var nextTime = 0;
    var current_button;
    var hold_end = false;
    var timeout;
    var load_app_rates;
    requestAnimationFrame(watcher);

    $('body').on('mousedown', '.indppl-bag-controls-neg', function(e){handleMouseDown(e, $(this));});
    $('body').on('mouseup', '.indppl-bag-controls-neg', function(e){handleMouseUp(e, $(this));});
    $('body').on('mouseout', '.indppl-bag-controls-neg', function(e){handleMouseout(e, $(this));});
    $('body').on('mousedown', '.indppl-bag-controls-pos', function(e){handleMouseDown(e, $(this));});
    $('body').on('mouseup', '.indppl-bag-controls-pos', function(e){handleMouseUp(e, $(this));});
    $('body').on('mouseout', '.indppl-bag-controls-pos', function(e){handleMouseout(e, $(this));});

    function handleMouseDown(e, elem){
        e.preventDefault();
        e.stopPropagation();
        clearTimeout(load_app_rates);
        current_button = $(elem);
        if($(elem).hasClass('indppl-bag-controls-neg')){
            timeout = setTimeout(function(){
                if(isDown != 0){
                    isDown = parseFloat(-.1);
                }
            }, 1000);
            timeout = setTimeout(function(){
                if(isDown != 0){
                    isDown = parseFloat(-1);
                }
            }, 3000);
            if(isDown == 0){
                bagControlsNEG(elem);
            }
            
        }else{
            timeout = setTimeout(function(){
                if(isDown != 0){
                    isDown = parseFloat(.1);
                }
            }, 1000);
            timeout = setTimeout(function(){
                if(isDown != 0){
                    isDown = parseFloat(1);
                }
            }, 3000);
            if(isDown == 0){
                bagControlsPOS(elem);
            }
        }
    }
    function handleMouseout(e, elem){
        e.preventDefault();
        e.stopPropagation();
        clearTimeout(timeout);
        isDown=0;
        hold_end = true;
    }

    function handleMouseUp(e, elem){
        e.preventDefault();
        e.stopPropagation();
        clearTimeout(timeout);
        isDown=0;
        hold_end = true;
        load_app_rates = setTimeout(function(){
            updateBagAppRates($(elem).parent().parent().find('.some-kind-of-wonderful').first());
        }, 2000);
    }

    function watcher(time){
        requestAnimationFrame(watcher);
        if(time<nextTime){return;}
        nextTime=time+delay;
        if(isDown != 0){
            var num = $(current_button).parent().parent().find('.indppl-product-create-chart-app-rate-num').first().val();
            num = parseFloat(num) + parseFloat(isDown);
            var number = parseFloat(num).toFixed(2);
            if (number < 0.01){
                number = 0;
            }
            $(current_button).parent().parent().find('.indppl-product-create-chart-app-rate-num').first().val(parseFloat(number));
            $(current_button).parent().parent().find('.indppl-bag-rate-num').first().text(parseFloat(number));
        }
    }

    $('body').on('change', ".planting-guide-option-input input", function(){
        // console.log;
        // console.log($(this));
        $(this).parents('ul').find(".instructions-content").removeClass('active');
        $(this).parents('li').find(".instructions-content").addClass('active');
    });



    // start of functions
    function validateEmail($email) {
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        return emailReg.test( $email );
    }

    function validatePhone(phone){
        phone = phone.replace(/[^0-9]/g,'');
        if (phone.length != 10){
            return false;
        }else{
            return true;
        }
    }

    function get100Percent(){
        

        var total = 0;
        jQuery('.pots-apprates-filler').each(function(){
            total = total + Number(jQuery(this).val());
        })
        if(total == 100){
            jQuery('.pots-apprates-filler-total').removeClass('color-red');
            jQuery('.pots-apprates-filler-total').addClass('color-green');
            jQuery('.pots-apprates-filler-message').removeClass('color-red');
            jQuery('.pots-apprates-filler-message').addClass('color-green');
            jQuery('.pots-apprates-filler-message').html('<p>Good Work! This mix adds up to 100%.</p>');
        }else{
            if(jQuery('.pots-apprates-filler-total').hasClass('color-green')){
                jQuery('.pots-apprates-filler-total').removeClass('color-green');
                jQuery('.pots-apprates-filler-total').addClass('color-red');
                jQuery('.pots-apprates-filler-message').removeClass('color-green');
                jQuery('.pots-apprates-filler-message').addClass('color-red');
                jQuery('.pots-apprates-filler-message').html("<p>Oops! This mix doesn't add up to 100%.</p><p>Please check your numbers and try again.</p>");
            }
        }
        $('.pots-apprates-filler-total').text(total);
    };

    function getProductInfo(){
        var version_check = 1.0;
        (function($){

            var store_id = $('#store-id').val();
            $.ajax({
                url:indppl_ajax.ajaxurl,
                dataType: 'text',
                method: 'POST',
                data: {
                    action: 'indppl_product_save_exit_ajax',
                    store_id: store_id,
                    version_check: version_check,
                },
                type: 'POST',
                success: function(e){
                    // console.log(e);
                    $('#indppl-tab-3 #pp-store-products').empty();
                    $('#indppl-tab-3 #pp-store-products').append(e);
                }
            });
        })(jQuery);
    }

    function greyOutAllUnchecked(){
        jQuery('.container-not-available-in-store').each(function(){
            jQuery(this).parent().parent().parent().parent().prepend("<div class='greyed-out-section'></div>");
        })
    }

    function indppl_get_units($type){
        if($type == undefined){
            $type = 'dry';
        }
        if($type == 'dry'){
            return {'tsp': 'Teaspoon', 'tbls': 'Tablespoon', 'qt-d': 'Quart', 'cuft': 'Cubic Feet', 'lb': 'Pounds', 'g': 'Gram', 'kg': 'Killogram', 'oz': 'Ounce', 'mL': 'Milliliter', 'L': 'Liter', 'cup': 'Cup', 'each': 'Each'};
        }else if($type == 'bag'){
            return {'ppc': 'plants per bag / container', 'cpp': 'bags / containers per plant'};
        }else{
            return {'tsp': 'Teaspoon', 'tbls': 'Tablespoon', 'floz': 'Fluid Ounce', 'qt-l': 'Quart', 'gal': 'Gallon', 'mL': 'Milliliter', 'L': 'Liter', 'cup': 'Cup'};
        }  
    }


    function check_on_load_and_click(){
        (function($){

            var add = 0;
            var user_status = $('#user-status').val();
            // console.log(user_status);
            add = $('.indppl-container-edit-title').length;
            // console.log(add);
            if(user_status == 'paidaccountpro' && add > 24){
                $('.add-container-btn').remove();
            }else if(user_status != 'paidaccountpro' && add > 4){
                $('.add-container-btn').remove();
        
            }
        })(jQuery);
        
    }

    function checkIfEach(){
        var notEach = true;
        $('.indppl-product-create-size-btn').each(function(){
            if($(this).data('unit') == 'each' && $(this).is(':visible')){
                $('.indppl-add-product-usage-type').addClass('hide');
                notEach = false;
            }
        })
        if(notEach == true){
            $('.indppl-add-product-usage-type').removeClass('hide');
        }
    }


    function check_on_load(){
        (function($){
            var user_status = $('#user-status').val();
            if(user_status != 'paidaccountpro'){
                $('.indppl-containers-table').prepend('<div class="greyed-out-form"><div class="up-sell-overlay"><h2 class="up-sell-title">Upgrade to Pro to gain these features and more!</h2><a href="/garden-center-pricing/" class="indppl-button up-sell-link">Upgrade Now!</a></div></div>');
            }
        })(jQuery);
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

    function indpplAddLoading(location, primary, secondary, background){
        console.log(location);
        if(location == undefined){
            location = 'body';
        }
        if(primary == undefined){
            primary = 'white';
        }
        if(secondary == undefined){
            secondary = 'white';
        }
        if(background == undefined){
            background = 'indppl-loading-background'; 
        }
        jQuery(location).append("<div class='indppl-loading-background'><div class=" + background + "><div id='indppl-loading-icon'><svg class='image' width='100' height='100'><path d='M5,50 a1,1 0 0,0 90,0' fill='none' stroke-opacity='0.9' stroke='" + primary + "' stroke-width='9'/></svg><svg class='image-rev' width='100' height='100'><path d='M2,50 a1,1 0 0,1 96,0' fill='none' stroke-opacity='0.7' stroke='" + secondary + "' stroke-width='3.6'/></svg><svg class='image-rev-2' width='100' height='100'><path d='M10,50 a40,40 0 0,0  40,40' stroke-width='6' stroke-opacity='0.7' stroke='" + secondary + "' fill='none'</></svg></div></div></div>");
    }

    function indpplDelLoading(){
        jQuery('.indppl-loading-background').remove();
    }
    function indpplAddProduct(type){
        var version_check = 1.0;
        (function($){

            $('body').prepend("<div class='slide-in-products-container'></div>");
            setTimeout(function(){
                $('.slide-in-products-container').addClass('left-0');
                indpplAddLoading('.slide-in-products-container', 'grey', 'grey', 'white-bg-for-loading');
            }, 20);
            $.ajax({
                url:indppl_ajax.ajaxurl,
                dataType: 'text',
                method: 'POST',
                data: {
                    action: 'indppl_add_new_product_ajax',
                    type: type,
                    version_check: version_check,
                },
                type: 'POST',
                success: function(e){
                    // console.log(e);
                    $('.slide-in-products-container').prepend(e);
        
                    indpplDelLoading();
                }
            })
        })(jQuery);
    }

    function indpplEditProduct(type, store_id, product_id){
        $('body').prepend("<div class='slide-in-products-container'></div>");
        setTimeout(function(){
            $('.slide-in-products-container').addClass('left-0');
            indpplAddLoading('.slide-in-products-container', 'grey', 'grey', 'white-bg-for-loading');
        }, 20)
        var version_check = 1.0;
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_get_product_info_ajax',
                type: type,
                store_id: store_id,
                product_id: product_id,
                version_check: version_check,
                edit: true,
            },
            type: 'POST',
            success: function(e){
                array = JSON.parse(e);
                console.log(array['console']);
                $('.product-create-brand-cut-off').children().each(function(){
                    $(this).empty();
                })
                if(array['container']){
                    $('.slide-in-products-container').append(array['container']);
                    $('#product-create-product').hide();
                    $('#product-create-brand').hide();
                    $('#product-create-brand').append('<option value=' + array['brand'] + ' selected></option>')
                    $('#product-create-product').append('<option value=' + product_id + ' selected></option>')
                    // $('#product-create-form').prev().html('Edit ' + array['product'] + ' by ' + array['brand']);
                }
                // if(array["standard_unit"]){
                //     $('.product-create-standard-unit-container').append(array["standard_unit"]);
                // }
                if(array['size']){
                    $('.product-create-size-container').append(array['size']);
                }
                if(array['new_size']){
                    $('.product-create-new-size-container').append(array['new_size']);
                }
                if(array['dry_wet']){
                    $('.product-create-dry-wet-container').append(array['dry_wet'][0]);
                    units = indppl_get_units(array['dry_wet'][1]);
                    // console.log(array['dry_wet'][2]);
                    if(array['dry_wet'][1] == 'wet'){
                        $('.product-create-fraction-bag').addClass('hide');
                        $('.product-create-5-cups-container').addClass('hide');
                    }
                    if(type == 'beds'){
                        delete units['each'];
                    }
                    $.each(units, function(index, value){
                        // console.log(value);
                        if(value != array['dry_wet'][2]){
                            $('.product-create-standard-unit').append('<option class="product-create-standard-unit-option" value="' + index + '">' + value + '</option>');
                            
                        }
                        $('.product-create-standard-unit-add').append('<option class="product-create-standard-unit-add-option" value="' + index + '">' + value + '</option>');
                    })
                }
                if(array['cups']){
                    $('.product-create-5-cups-container').append(array['cups']);

                }
                // console.log(array['app_rate']);
                // if(array['app_rate']){
                //     $('.product-create-app-rate-container').append(array['app_rate']);
                // }
                if(array['app_rates_chart']){
                    $('.product-create-app-rates-chart-container').append(array['app_rates_chart']);
                }
                var units = indppl_get_units(array['dry_wet'][1]);
                // console.log(units);
                $('.indppl-product-create-chart-app-unit').each(function(){
                    var select = $(this).data('unit');
                    var elem = $(this);
                    // console.log(select);
                    $.each(units, function(index, value){
                        if(select == index){
                            selected = `selected`;
                        }else{
                            selected = ``;
                        }
                        $(elem).append('<option class="indppl-product-create-chart-app-unit-option" value="' + index + '" ' + selected + '>' + value + '</option>');
                    });
                    // console.log(unit);
                });
                if(array['next_btn']){
                    $('.product-create-save-done-container').append(array['next_btn']);
                }
                if(array['fraction']){
                    $('.product-create-fraction-bag').append(array['fraction']);
                }
                if(array['default']){
                    if(array['default'] == 1){
                        $('.product-create-fraction-bag').hide();
                    }
                }
                if(array['usage_type']){
                    $('.product-create-usage-type').append(array['usage_type']);
                }
                
                indpplDelLoading();
            }

        })
        
    }

    function indpplAddSmallLoading(){
        var host = window.location.protocol + '//' + window.location.host + "/";
        if(window.location.host ==  "127.0.0.1"){
            host = host + "plantpal/";
        }
        var img = host + "wp-content/plugins/planting-pal/assets/img/small_loading.png";
        var send = "<img class='spining-loader' src='" + img + "'>";
        return send;
    }

    function indpplDelSmallLoading(){
        $('.spining-loader').remove();
    }

    function updateAppRates(elem){
        var img = indpplAddSmallLoading();
        jQuery(elem).parent().parent().append(img);
        if(jQuery(elem).hasClass('indppl-product-create-chart-app-rate-num')){
            var cont_id = jQuery(elem).attr('name');
            var num = jQuery(elem).attr('value');
            var unit = jQuery(elem).next().val();
            if(!jQuery.isNumeric(num)){
                jQuery(elem).attr('value', 1);
            }
        }else if(jQuery(elem).hasClass('indppl-product-create-chart-app-unit')){
            var cont_id = jQuery(elem).attr('name');
            var num = jQuery(elem).prev().attr('value');
            var unit = jQuery(elem).val();
            if(!jQuery.isNumeric(num)){
                jQuery(elem).prev().attr('value' , 1);
            }
        }
        if(num == null || !jQuery.isNumeric(num)){
            num = 1;
        }
        if(unit == null){
            unit = 'lb';
        }
        // console.log(num);
        // console.log(unit);
        var type = jQuery('#indppl-modal-product-type').val();
        var product_id = jQuery('#product-create-product').val();
        var brand = jQuery('#product-create-brand').val();
        var store_id = jQuery('#store-id').val();
        var current_pack = {};
        var i = 0;
        var version_check = 1.0;
        jQuery('.indppl-product-create-size-btn').each(function(){
            if(jQuery(this).hasClass('indppl-size-selected')){
                current_pack[i] = {};
                current_pack[i]['size'] = jQuery(this).data('size');
                current_pack[i]['unit'] = jQuery(this).data('unit');
                i++;
            }
        });
        console.log(num);
        jQuery.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_update_app_rates_ajax',
                type: type,
                store_id: store_id,
                product_id: product_id,
                brand: brand,
                current_pack: current_pack,
                container_id: cont_id,
                container_num: num,
                container_unit: unit,
                version_check: version_check,
            },
            type: 'POST',
            success: function(e){
                console.log(e);
                array = JSON.parse(e);
                // console.log(elem);
                console.log(array['console']);
                jQuery.each(array['app_rates'], function(index, value){
                    // console.log(index);

                    jQuery(elem).parent().siblings().eq(1+index).html(`<p class="green-text text-align-center margin-0 plant-num-text">`+value+`</p><p class="white-text green-bg text-align-center margin-0">plants</p>`);
                });
                indpplDelSmallLoading();
            }
        });
        
    }

    function updateBagAppRates(elem){
        var elem = elem;
        var load = indpplAddSmallLoading();
        $(elem).parent().parent().append(load);
        var store_id = $('#store-id').val();
        var type = $('#indppl-modal-product-type').val();
        var product_id = $('#product-create-product').val();
        var val = $(elem).parent().parent().find('.indppl-product-create-chart-app-rate-num').first().val();
        var ppc = $(elem).parent().find('.indppl-product-create-chart-bag-unit').val();
        var product_num = $('#indppl-how-much-header').first().data('num');
        var product_unit = $('#indppl-how-much-header').first().data('unit');
        var cont_id = $(elem).parent().parent().find('.bag-apprates-container-title').data('id');
        var version_check = 1.0;
        if(val == null || !$.isNumeric(val)){
            val = 1;
            $(elem).parent().children().val(1);
        }
        console.log('--------');
        console.log(elem);
        console.log(cont_id);
        console.log(product_num);
        console.log(product_unit);
        console.log(val);
        jQuery.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_update_bag_app_rates_ajax',
                type: type,
                store_id: store_id,
                product_id: product_id,
                val: val,
                ppc: ppc,
                product_num: product_num,
                product_unit: product_unit,
                cont_id: cont_id,
                version_check: version_check,
            },
            type: 'POST',
            success: function(e){
                // console.log(e);
                $('.product-create-app-rates-chart-container').empty();
                $('.product-create-app-rates-chart-container').html(e);
                var bagunits = indppl_get_units('bag');
                $('.indppl-product-create-chart-bag-unit').each(function(){
                    var select = $(this).data('unit');
                    var elem = $(this);
                    // console.log(select);
                    
                    $.each(bagunits, function(index, value){
                        if(select == index || (select == 'tbl' && index == 'tbls')){
                            selected = `selected`;
                        }else{
                            selected = ``;
                        }
                        $(elem).append('<option class="indppl-product-create-chart-bag-unit-option" value="' + index + '" ' + selected + '>' + value + '</option>');
                    });
                    // console.log(unit);
                });
                indpplDelSmallLoading();
            }
        });
    }

    function monitorProgress(store){
        var store = store;

        $(document).ajaxComplete(function (event, jqxhr, settings){
            // console.log(settings);
            // console.log(jqxhr);
            // console.log(event);
            var args = settings.data;
            if(args.search('stopAjaxComplete' ) < 0){
                $(".log").text("Triggered ajaxComplete handler.");
                // console.log(store);
                jQuery.ajax({
                    url:indppl_ajax.ajaxurl,
                    dataType: 'text',
                    method: 'POST',
                    data: {
                        action: 'indppl_store_progress_bar_ajax',
                        store_id: store,
                        stopAjaxComplete : true,
                    },
                    type: 'POST',
                    success: function(results){
                        $('.indppl-progress-container').html(results);
                    }
                });
            }
        });
    }

    function containerSubmit(){
        indpplAddLoading();
        $first_time = $('.ind-first-time').length;
        console.log($first_time);
        if($('.indppl-update-apps').length > 0 && $first_time == 0){
            alert("We've added your containers, please verify the amounts are correct in your in ground application rates.");
        }
        var empty = false;
        var empty_elem;
        $('.indppl-container-edit-title').each(function(){
            console.log($(this).val());
            if($(this).val() == ""){
                empty = true;
                empty_elem = $(this);
                indpplDelLoading();
            }
        });
        if(empty == true){
            $(empty_elem).attr('placeholder', 'Required');
        }else{
            $('.container-available').each(function(){
                $(this).removeClass('indppl-update-apps');
            });
            var date = $("#container-select-form").find('input').filter('.container-date').serializeArray();
            var available = [];
            var default_container = $("#container-select-form").find('input').filter('.indppl-default-container').serializeArray();
            var non_default = $("#container-select-form").find('input').filter('.indppl-non-default-container').serializeArray();
            var store_id = $('#store-id').val();
            var not_available = [];
            var remove_dot = [];
            var new_array = {};
            var array_num = 0;
            var version_check = 1.0;
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
                if($(this).attr('name').indexOf("new-container") >= 0){
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

            // console.log(new_array);
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
                    version_check: version_check,
                },
                type: 'POST',
                success: function(e){
                    getProductInfo();
                    // console.log(e);
                    var new_array = jQuery.parseJSON(e);
                    console.log(new_array['update']);
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
        }

    }

    function getAppRates(type){
        
        $('body').prepend("<div class='slide-in-products-container'></div>");
        setTimeout(function(){
            $('.slide-in-products-container').addClass('left-0');
            indpplAddLoading('.slide-in-products-container', 'grey', 'grey', 'white-bg-for-loading');
        }, 20);
        var store_id = $('#store-id').val();
        var version_check = 1.0;
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_get_pot_apprates_ajax',
                store_id: store_id,
                type: type,
                version_check: version_check,
            },
            type: 'POST',
            success: function(e){
                // console.log(e);
                $('.slide-in-products-container').append(e);
                get100Percent();
                indpplDelLoading();
            }
        })
    }

});