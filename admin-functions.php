<?php

// addmin section
// add_action('admin_menu', 'planting_pal_admin_menu');

// function planting_pal_admin_menu(){
//     add_menu_page('Planting Pal Admin Menu', 'Planting Pal', 'manage_options', 'planting-pal-admin', 'planting_pal_init');
// }

// function planting_pal_init(){

// }


function ppl_user_fields(WP_User $user){
    ?>
    <div class='sponsor-wrap'>
        <h3>Planting Pal Sponsor Fields</h3>
        <label for='is_sponsor' id='is-sponsor'>Sponsor</label>
        <input type='checkbox' value='1' <?php if (get_user_meta($user->ID, 'is_sponsor', true) == 1) echo 'checked="checked"'; ?> name='is_sponsor' id='is_sponsor'/>
        <div class='sponsor-hidden'>
            <?php
            $brand_array = get_terms( array(
                'taxonomy' => 'brand',
                'hide_empty' => false,
            ));
            ?>
            <div class='sponsor-brand-container'>

                <?php
                foreach($brand_array as $key => $value){
                    $brand_id = $value->term_id;
                    $brand_name = $value->name;
                    $brand_slug = $value->slug;
                    $brand_save = $brand_slug . "-" . $brand_id;
                    ?>
                    <p>

                        <input type='checkbox' value='1' <?php if(get_user_meta($user->ID, $brand_save, true) == 1) echo 'checked="checked"'; ?> name="<?php echo $brand_save; ?>" id="<?php echo $brand_save; ?>">
                        <label for='<?php echo $brand_save; ?>'><?php echo $brand_name; ?> </label>
                    </p>
                        <?php
                }
                ?>
            </div>
            <input type='number' min='0' max='100' class='sponsor-count' name='sponsor_count' id='sponsor_count' value='<?php echo get_user_meta($user->ID, "sponsor_count", true); ?>'/>
            <label for='sponsor_count' class='sponsor-count-label'># of Sponsorships</label>

        </div>
    </div>
    <?php


}
add_action( 'show_user_profile', 'ppl_user_fields' );
add_action( 'edit_user_profile', 'ppl_user_fields' );

function ppl_user_fields_save($user_id){
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }
 
    update_user_meta($user_id, 'is_sponsor', $_REQUEST['is_sponsor']);
    update_user_meta($user_id, 'sponsor_count', $_REQUEST['sponsor_count']);
    $brand_array = get_terms( array(
        'taxonomy' => 'brand',
        'hide_empty' => false,
    ));
    foreach($brand_array as $key => $value){
        $brand_id = $value->term_id;
        $brand_slug = $value->slug;
        $brand_save = $brand_slug . "-" . $brand_id;
        update_user_meta($user_id, $brand_save, $_REQUEST[$brand_save]);
    }
}
add_action('personal_options_update', 'ppl_user_fields_save');
add_action('edit_user_profile_update', 'ppl_user_fields_save');