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
        var split = location.search.replace('?', '').split('=')
        $store_id = $('#store-id').val();
        var url = window.location.href;
        url = url + "?store-id=" + $store_id;
        console.log(split);
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
        // console.log(store_id);
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
                // console.log(e);
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

        // console.log(available);
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
                // console.log(e);
                var new_array = jQuery.parseJSON(e);
                // console.log(new_array);
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
        var type = $(this).data('type');
        indpplAddProduct(type);
    })
    $('body').on('click', '.modal-close', function(e){
        $(this).hide();
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
                var status = $('#user-status').val();
                // console.log(status);
                if(status != 'paidaccountpro'){
                    $('.product-create-product').append('<option value="new">Add Product</option>');
                }
                indpplDelLoading();
            }
        })
    });
    $('body').on('change', '#product-create-product', function(e){
        indpplAddLoading();
        var product_id = $(this).val();
        var store_id = $('#store-id').val();
        var type = $('#indppl-modal-product-type').val();
        
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_get_product_info_ajax',
                product_id: product_id,
                store_id: store_id,
                type: type,
            },
            type: 'POST',
            success: function(e){
                array = JSON.parse(e);
                // console.log(array['console']);
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
                    // console.log(array['dry_wet'][2]);
                    $.each(units, function(index, value){
                        // console.log(value);
                        if(value != array['dry_wet'][2]){
                            $('.product-create-standard-unit').append('<option class="product-create-standard-unit-option" value="' + index + '">' + value + '</option>');
                            
                        }
                        $('.product-create-standard-unit-add').append('<option class="product-create-standard-unit-add-option" value="' + index + '" selected>' + value + '</option>');
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
        $('.product-create-size-container').append('<a href="#" class=" indppl-product-create-size-btn margin-right-4 indppl-non-default-package indppl-new-package indppl-background-green" data-id="0" data-size=' + size + ' data-unit=' + unit + '>' + size + " " + unit + '</a>');

    })
    $('body').on('mouseenter', '.indppl-background-green', function(){
        if($(this).hasClass('indppl-non-default-package')){
            $(this).append('<span class="indppl-x">X</span>');
        }
    })
    $('body').on('mouseleave', '.indppl-background-green', function(){
        $(".indppl-x").remove();
    })
    
    $('body').on('click', '.indppl-x', function(){
        // this needs to remove a package in the back end.
        if($(this).parent().hasClass('indppl-non-default-package')){
            // console.log('inside');
            $(this).parent().hide();
            setTimeout(function(){
                $(this).parent().removeClass('indppl-background-green');
            }, 200);
        }else{
            $(this).parent().remove();
        }

    })
    $('body').on('click', '.indppl-product-create-size-btn', function(e){
        e.preventDefault();
        if(!$(this).hasClass('indppl-background-green')){
            $(this).addClass('indppl-background-green');
        }else{
            $(this).removeClass('indppl-background-green');
        }
    })
    $('body').on('click', '.product-create-submit', function(e){
        e.preventDefault();
        indpplAddLoading();
        
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
        var product_dryliquid = $('.product-create-dry-wet').val();
        var product_name = $('.indppl-add-product-name').val();
        if(!$(this).is('#product-create-next')){
            var product_input = $("#product-create-form").find('input').filter('.some-kind-of-wonderful').serializeArray();
            var product_select = $("#product-create-form").find('select').filter('.some-kind-of-wonderful').serializeArray();
            var container_id = [];
            $('.bag-apprates-container-title').each(function(){
                container_id.push($(this).data('id'));
            });
            var first_package = {}
            first_package['num'] = $('.bag-apprates-title').data('num');
            first_package['unit'] = $('.bag-apprates-title').data('unit');
        }else{
            var cups_num = $('.indppl-product-create-cups-num').val();
            var cups_unit = $('.product-create-5-cups').val();
        }
            // console.log(product_array);
        var elem = $(this);
        var package_array = [];
        var package_remove = [];
        var new_pack = {};
        var i = 0;
        // console.log(product_input);
        // console.log(product_select);
        if($(this).is('#product-create-next')){
            $('.indppl-product-create-size-btn').each(function(){
                if($(this).hasClass('indppl-background-green')){
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
        console.log(package_remove);
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
                    indpplDelLoading();
                }else{
                    // console.log(e);
                    array = JSON.parse(e);
                    // console.log(array);
                    console.log(array['console']);
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
                            if(select == index || (select == 'tbl' && index == 'tbls')){
                                selected = `selected`;
                            }else{
                                selected = ``;
                            }
                            $(elem).append('<option class="indppl-product-create-chart-app-unit-option" value="' + index + '" ' + selected + '>' + value + '</option>');
                        });
                        // console.log(unit);
                    });
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
                    indpplDelLoading();
                }
            }
        });

    });
    $('body').on('click', '.product-create-submit-back', function(e){
        e.preventDefault();
        $('.product-create-app-rates-chart-container').slideToggle();
        $('.product-create-first-part-container').slideToggle();
        $('.indppl-background-green').removeClass('indppl-new-package');
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
        var load = indpplAddSmallLoading();
        $(this).parent().parent().append(load);
        var store_id = $(this).data('store');
        var type = $(this).data('type');
        var product_id = $(this).data('product');
        var elem = $(this);
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_remove_package_from_store_ajax',
                product_id: product_id,
                store_id: store_id,
                type: type,
            },
            type: 'POST',
            success: function(e){
                $(elem).parent().parent().remove();
                // console.log(e);
                indpplDelSmallLoading();
            }
        })
    })

    $('body').on('change', '.some-kind-of-wonderful', function(){
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
        var store_id = $('#store-id').val();
        var type = $('#indppl-modal-product-type').val();
        var brand = $('#product-create-brand').val();
        var product_id = $('#product-create-product').val();
        var product_unit = $('.indppl-new-package').last().data('unit');
        var product_dryliquid = $('.product-create-dry-wet').val();
        var product_input = $("#product-create-form").find('input').filter('.some-kind-of-wonderful').serializeArray();
        var product_select = $("#product-create-form").find('sel ect').filter('.some-kind-of-wonderful').serializeArray();
        var product_name = $('.indppl-add-product-name').val();
        var cups_num = $('.indppl-product-create-cups-num').val();
        var cups_unit = $('.product-create-5-cups').val();
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
        // console.log(type);
        var i = 0;
        if($(this).is('#product-create-pots-next')){
            $('.indppl-product-create-size-btn').each(function(){
                if($(this).hasClass('indppl-background-green')){
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
        }
        // console.log(filler);
        // console.log(blend);
        // console.log(surface);
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
                prodcut_unit: product_unit,
                product_input: product_input,
                product_select: product_select,
                product_dryliquid: product_dryliquid,
                new_pack: new_pack,
                cups_num: cups_num,
                filler: filler,
                blend: blend,
                surface: surface,
                cups_unit: cups_unit,
                fraction: fraction,
                product_name: product_name,
            },
            type: 'POST',
            success: function(e){
                // array = JSON.parse(e);
                // console.log(array);
                console.log(e);
                getProductInfo();
                $('.slide-in-products-container').removeClass('left-0');
                setTimeout(function(){
                    $('.slide-in-products-container').remove();
                }, 1000);
                indpplDelLoading();
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
        $('body').prepend("<div class='slide-in-products-container'></div>");
        setTimeout(function(){
            $('.slide-in-products-container').addClass('left-0');
            indpplAddLoading('.slide-in-products-container', 'grey', 'grey', 'white-bg-for-loading');
        }, 20);
        var store_id = $('#store-id').val();
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_get_pot_apprates_ajax',
                store_id: store_id,
                type: type,
            },
            type: 'POST',
            success: function(e){
                // console.log(e);
                $('.slide-in-products-container').append(e);
                get100Percent();
                indpplDelLoading();
            }
        })
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

        $.ajax({
            url: indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_setup_guide_forms_ajax',
                form: target,
                store: storeid,
            },
            type: 'POST',
            success: function (response) {
               $('.indppl-loading-background').remove();
               $('.slide-in-products-container .container').append(response); 
            }
        });
    });


    // Toggle planting guide sections
    $("body").on('click', '.planting-guide-sections .indppl-button', function(e){
        e.preventDefault();
        var target = $(this).data('target');
        console.log(target);
        $(this).parents('.planting-guide-options').slideToggle();
        $('.' + target).slideToggle();
    });

    $("body").on('click', '.planting-guide-instructions input[type=radio]', function() {
        var content = $("#" + $(this).data('content')).text();
        var target = $(this).data('target');
        $("#" + target).html(content);
        var myContainer = $('.planting-guide-preview')

        var scrollTo = $("#" + target);
        console.log(scrollTo);
        myContainer.animate({
            scrollTop: scrollTo.offset().top - myContainer.offset().top + myContainer.scrollTop()
        });
    });

    $('body').on('click', '.pots-apprates-save-btn', function(e){
        e.preventDefault();
        indpplAddLoading();
        console.log('something');
        var store_id = $('#store-id').val();
        var fill_array = {};
        var type = $('#indppl-modal-product-type').val();
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
            },
            type: 'POST',
            success: function(e){
                console.log(e);
                indpplDelLoading();
            }
        });
    })

    
});

function get100Percent(){
    

    var total = 0;
    jQuery('.pots-apprates-filler').each(function(){
        total = total + Number(jQuery(this).val());
    })
    console.log(total);
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
    (function($){

        var store_id = $('#store-id').val();
        $.ajax({
            url:indppl_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indppl_product_save_exit_ajax',
                store_id: store_id,
            },
            type: 'POST',
            success: function(e){
                // console.log(e);
                $('#indppl-tab-3').empty();
                $('#indppl-tab-3').append(e);
            }
        });
    })(jQuery);
}

function greyOutAllUnchecked(){
    (function($){
        if(!$(this).find('input').is(":checked")){
            $('.container-available').each(function(){
            $(this).parent().parent().prepend("<div class='greyed-out-section'></div>");
                // console.log('this');
            })
        }
    })(jQuery);
}

function indppl_get_units($type = 'dry'){
    if($type == 'dry'){
        return {'tsp': 'Teaspoon', 'tbls': 'Tablespoon', 'qt-d': 'Quart', 'cuft': 'Cubic Feet', 'lb': 'Pounds', 'g': 'Gram', 'kg': 'Killogram', 'oz': 'Ounce', 'mL': 'Milliliter', 'L': 'Liter', 'cup': 'Cup', 'each': 'Each', 'Bag': 'Bag'};
    }else if($type == 'bag'){
        return {'ppc': 'plants per bag / contianer', 'cpp': 'bags / containers per plant'};
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

function check_on_load(){
    (function($){
        var user_status = $('#user-status').val();
        if(user_status != 'paidaccountpro'){
            $('.indppl-containers-table').prepend('<div class="greyed-out-form"><div class="up-sell-overlay"><h2 class="up-sell-title">Upgrade to Pro to gain these features and more!</h2><a href="#" class="indppl-button up-sell-link">Upgrade Now!</a></div></div>');
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

function indpplAddLoading(location = 'body', primary = 'white', secondary = 'white', background = 'indppl-loading-background'){
    jQuery(location).append("<div class='indppl-loading-background'><div class=" + background + "><div id='indppl-loading-icon'><svg class='image' width='100' height='100'><path d='M5,50 a1,1 0 0,0 90,0' fill='none' stroke-opacity='0.9' stroke='" + primary + "' stroke-width='9'/></svg><svg class='image-rev' width='100' height='100'><path d='M2,50 a1,1 0 0,1 96,0' fill='none' stroke-opacity='0.7' stroke='" + secondary + "' stroke-width='3.6'/></svg><svg class='image-rev-2' width='100' height='100'><path d='M10,50 a40,40 0 0,0  40,40' stroke-width='6' stroke-opacity='0.7' stroke='" + secondary + "' fill='none'</></svg></div></div></div>");
}

function indpplDelLoading(){
    jQuery('.indppl-loading-background').remove();
}
function indpplAddProduct(type){
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
    $.ajax({
        url:indppl_ajax.ajaxurl,
        dataType: 'text',
        method: 'POST',
        data: {
            action: 'indppl_get_product_info_ajax',
            type: type,
            store_id: store_id,
            product_id: product_id,
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
                $('#product-create-form').prev().html('Edit ' + array['product'] + ' by ' + array['brand']);
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
                $.each(units, function(index, value){
                    // console.log(value);
                    if(value != array['dry_wet'][2]){
                        $('.product-create-standard-unit').append('<option class="product-create-standard-unit-option" value="' + index + '">' + value + '</option>');
                        
                    }
                    $('.product-create-standard-unit-add').append('<option class="product-create-standard-unit-add-option" value="' + index + '" selected>' + value + '</option>');
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
    }else if(jQuery(elem).hasClass('indppl-product-create-chart-app-unit')){
        var cont_id = jQuery(elem).attr('name');
        var num = jQuery(elem).prev().attr('value');
        var unit = jQuery(elem).val();
    }
    if(num == null){
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
    jQuery('.indppl-product-create-size-btn').each(function(){
        if(jQuery(this).hasClass('indppl-background-green')){
            current_pack[i] = {};
            current_pack[i]['size'] = jQuery(this).data('size');
            current_pack[i]['unit'] = jQuery(this).data('unit');
            i++;
        }
    });
    // console.log(unit);
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
        },
        type: 'POST',
        success: function(e){
            array = JSON.parse(e);
            // console.log(elem);
            // console.log(array);
            jQuery.each(array['app_rates'], function(index, value){
                // console.log(index);

               jQuery(elem).parent().siblings().eq(1+index).text(value + " Plants");
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
    var val = $(elem).parent().children().val();
    var ppc = $(elem).parent().find('.indppl-product-create-chart-bag-unit').val();
    var product_num = $('.bag-apprates-title').first().data('num');
    var product_unit = $('.bag-apprates-title').first().data('unit');
    var cont_id = $(elem).parent().prev().data('id');
    console.log(product_num);
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
