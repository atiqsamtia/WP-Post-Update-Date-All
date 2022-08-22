<?php
/*
 * Plugin Name: Bulk Post Update Date
 * Version: 1.4.0
 * Description: Change the Post Update date for all posts in one click. This will help your blog in search engines and your blog will look alive. Do this every week or month. (Tip By Waqas Jawed in Bloggers Funda - facebook group)
 * Author: Atiq Samtia
 * Author URI: https://atiq.dev
 * Plugin URI: https://github.com/atiqsamtia/WP-Post-Update-Date-All
 * Text Domain: bulk-post-update-date
 * Domain Path: /languages
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 

    Bulk Post Update Date is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    any later version.
    
    Bulk Post Update Date is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
    along with Bulk Post Update Date. If not, see {URI to Plugin License}.
*/


function bulk_post_update_date_menu() {
	add_options_page( 'Bulk Post Update Date', 'Bulk Post Update Date', 'manage_options', 'bulk-post-update-date', 'bulk_post_update_date_options' );
}

add_action( 'admin_menu', 'bulk_post_update_date_menu' );

function bulk_post_update_date_options() {
    global $wpdb;
    $settings_saved = 0;
    
    $tab = isset($_GET['tab']) ? $_GET['tab'] : 'posts';

    $type = $tab;

    //Extra Check for url bug
    $tab = ($tab == 'pages' || $tab == 'posts') ? $tab : 'custom';

	$now = current_time( 'timestamp', 0 );;


	if ( isset( $_POST[ 'tb_refresh' ] ) && wp_verify_nonce( $_POST['tb_refresh'],'tb-refresh' ) && current_user_can( 'manage_options' ) ) {

        // get All posts IDs

        $field =   $_POST['field'];

        $field =   $field == 'published' ? 'post_date' : 'post_modified';

        $ids = array();

        if($type == 'posts'){

            $params = array(
		        'numberposts' => -1,
                'post_status' => 'publish',
		        'fields'        => 'ids'
	        );

            if(isset( $_POST['categories'])){
                $params['cat'] = implode( ',', $_POST['categories']);
            }

            $ids = get_posts(
                    $params
            );

        } else if($type == 'pages'){

            if(isset($_POST['pages'])){
            
                $ids = $_POST['pages'];
            
            } else{

                $pages_ = get_pages();
                $ids = wp_list_pluck( $pages_, 'ID' );
            
            }

        } else {
            $params = array(
		        'numberposts' => -1,
                'post_status' => 'publish',
		        'fields'        => 'ids',
                'post_type' => $type
	        );

            if(isset( $_POST['tax'])){

                foreach($_POST['tax'] as $tax => $terms){
                    $params['tax_query'][] = array(
                        'taxonomy' => $tax,
                        'field' => 'term_id',
                        'terms' => $terms
                    );
                }

                $relation = isset( $_POST['tax_relation']) ? $_POST['tax_relation'] : 'OR';
                $params['tax_query']['relation'] = $relation;

            }

            // echo '<pre>';
            // print_r($params);
            // return;

            $ids = get_posts(
                    $params
            );

            // print_r($ids);
            // return;
        }

        $from = $_POST['distribute'];
        $to = current_time( 'timestamp', 0 );

        if($from == 0){
            $range = explode( '-',$_POST['range']);
            if(count( $range) == 2){
                    $from = strtotime( $range[0],$now);
                    $to = strtotime( $range[1],$now);
            } else {
                $from = strtotime( '-3 hours',$now);
            }
        }

        foreach($ids as $id){

            //TODO Get Last modified and published date and never backdate modified date
	        $time = rand($from,$to);
            $time = date("Y-m-d H:i:s",$time);

            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE $wpdb->posts SET $field ='%s', {$field}_gmt='%s' WHERE ID = %d",
                     $time,
                     get_gmt_from_date($time),
                     $id
                    )
                );
        }
        $settings_saved = count($ids);
	}

	wp_enqueue_script('momentjs','https://cdn.jsdelivr.net/momentjs/latest/moment.min.js');
	wp_enqueue_script('daterangepicker','https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js');
    wp_enqueue_style('daterangepicker','https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css');
	wp_enqueue_style('bulkupdatedate',plugins_url( '/style.css', __FILE__ ));
    

	?>

	<div class="wrap">
		<h1 class="title"><?php _e( 'Bulk Post Update Date', 'bulk-post-update-date' ); ?></h1>
        <div>
			<?php _e( 'Change the Post Update date for all posts in one click. This will help your blog in search engines and your blog will look alive. Do this every week or month.', 'bulk-post-update-date' ) ?>
        </div>
        

		<?php if ( $settings_saved > 0 ) : ?>
			<div id="message" class="updated fade">
				<p><strong><?php _e( "$settings_saved ". ucfirst($tab) ." Update date refreshed.", 'bulk-post-update-date' ) ?></strong></p>
			</div>
		<?php endif ?>

            <hr/>

        <div class="top-sharebar">
            <a class="share-btn rate-btn" href="https://wordpress.org/support/plugin/bulk-post-update-date/reviews/?filter=5#new-post" target="_blank" title="Please rate 5 stars if you like Bulk Post Update Date"><span class="dashicons dashicons-star-filled"></span> Rate 5 stars</a>
            <a class="share-btn twitter" href="https://twitter.com/intent/tweet?text=Checkout%20Bulk%20Post%20Update%20Date,%20a%20%23WordPress%20plugin%20that%20updates%20last%20modified%20date%20and%20time%20on%20pages%20and%20posts%20very%20easily.&amp;tw_p=tweetbutton&amp;url=https://wordpress.org/plugins/bulk-post-update-date/&amp;via=atiqsamtia" target="_blank"><span class="dashicons dashicons-twitter"></span> Tweet about Bulk Post Update Date</a>
        </div>

        <h2 class="nav-tab-wrapper">
            <a href="?page=bulk-post-update-date&tab=posts" class="nav-tab <?php echo $tab =='posts' ? 'nav-tab-active' : ''; ?>"> <span class="dashicons dashicons-admin-post" style="padding-top: 2px;"></span> <?php _e( 'Posts') ?></a>
            <a href="?page=bulk-post-update-date&tab=pages" class="nav-tab <?php echo $tab =='pages' ? 'nav-tab-active' : ''; ?>"> <span class="dashicons dashicons-admin-page" style="padding-top: 2px;"></span> <?php _e( 'Pages' ) ?></a>
        
            <?php

            $args = array(
                'public'   => true,
                '_builtin' => false
            );
            
            $output = 'objects'; // 'names' or 'objects' (default: 'names')
            $operator = 'and'; // 'and' or 'or' (default: 'and')
            
            $post_types = get_post_types( $args, $output, $operator );
            
            if ( $post_types ) { // If there are any custom public post types.
            
            
                foreach ( $post_types  as $post_type ) {
                    ?>
                    <a href="?page=bulk-post-update-date&tab=<?php echo $post_type->name; ?>"
                    class="nav-tab <?php echo $type == $post_type->name ? 'nav-tab-active' : ''; ?>">
                    <?php if (strpos($post_type->menu_icon, 'dashicon') !== false){?> 
                        <span class="dashicons <?php echo $post_type->menu_icon; ?>" style="padding-top: 2px;"></span> 
                        <?php } else {?>
                    <img src="<?php echo $post_type->menu_icon; ?>" style="vertical-align: middle;margin-right: 3px;margin-top: -2px;"> 
                    <?php } ?>
                    <?php _e( $post_type->label ) ?>
                </a>
                <?php }
            
            
            }

            ?>
        
        </h2>

	

		<form method="post" action="">

            <table class="form-table">

                <tr valign="top">
                    <th scope="row"><label for="distribute"><?php _e( 'Distribute into Last', 'bulk-post-update-date' ); ?></label></th>
                    <td>

                        <select type="text" id="distribute" name="distribute">
                            <option value="<?php echo strtotime( '-1 hour',$now);?>"><?php _e( '1 hour', 'bulk-post-update-date' ); ?></option>
                            <option value="<?php echo strtotime( '-1 day',$now);?>"><?php _e( '1 Day', 'bulk-post-update-date' ); ?></option>
                            <option value="<?php echo strtotime( '-15 days',$now);?>"><?php _e( '15 Days', 'bulk-post-update-date' ); ?></option>
                            <option value="<?php echo strtotime( '-1 month',$now);?>"><?php _e( '1 Month', 'bulk-post-update-date' ); ?></option>
                            <option value="<?php echo strtotime( '-2 month',$now);?>"><?php _e( '2 Months', 'bulk-post-update-date' ); ?></option>
                            <option value="<?php echo strtotime( '-3 month',$now);?>"><?php _e( '3 Months', 'bulk-post-update-date' ); ?></option>
                            <option value="<?php echo strtotime( '-6 month',$now);?>"><?php _e( '6 Months', 'bulk-post-update-date' ); ?></option>
                            <option value="0">Custom Range</option>
                        </select>
                        <p class="description">
                        <?php _e( 'Select range of date in which you want to spread the dates', 'bulk-post-update-date' ); ?>
                        </p>
                    </td>
                </tr>
                <tr id="range_row" valign="top" style="display: none;">
                    <th scope="row"><label for="range"><?php _e( 'Custom Date Range', 'bulk-post-update-date' ); ?></label></th>
                    <td>

                        <input type="text" id="range" name="range" value="<?php echo date('m/d/y',strtotime( '-3 days',$now));?> - <?php echo date('m/d/y',$now);?>" />
                        <p class="description">
                        <?php _e( 'Select range of date in which you want to spread the dates', 'bulk-post-update-date' ); ?>
                        </p>
                    </td>
                </tr>


                <?php

                include_once "$tab.php";

                ?>

                    <tr id="field_row" valign="top" >
                    <th scope="row"><label for="field"><?php _e( 'Date field to update', 'bulk-post-update-date' ); ?></label></th>
                    <td>
                    
                        <input type="radio" id="published" name="field" value="published">
                        <label for="published"><?php _e( 'Published Date', 'bulk-post-update-date' ); ?></label>
                        
                        <input type="radio" id="modified" name="field" value="modified" checked>
                        <label for="modified"><?php _e( 'Modified Date', 'bulk-post-update-date' ); ?></label>

                        <p class="description">
                        <?php _e( 'Updating modified date is recommended.', 'bulk-post-update-date' ); ?>
                        </p>
                    </td>
                </tr>

            </table>

			<p class="submit">
                <input name="tb_refresh" type="hidden" value="<?php echo wp_create_nonce('tb-refresh'); ?>" />
				<input class="button-primary" name="do" type="submit" value="<?php _e( 'Update Post Dates', 'bulk-post-update-date' ) ?>" />
			</p>
		</form>
	</div>



    <div class="coffee-box">
        <div class="coffee-amt-wrap">
         
            <a class="button button-primary buy-coffee-btn" style="margin-left: 2px;" href="https://www.fiverr.com/atiqsamtia/code-or-fix-php-html-css-jquery-mysql-or-wordpress" target="_blank">Buy me a coffee!</a>
        </div>
        <span class="coffee-heading">Buy me a coffee!</span>
        <p style="text-align: justify;">Thank you for using <strong>Bulk Post Update Date</strong>. If you found the plugin useful buy me a coffee! Your donation will motivate and make me happy for all the efforts. You can donate via Fiverr.</p>
        <p style="text-align: justify; font-size: 12px; font-style: italic;">Developed with <span style="color:#e25555;">♥</span> by <a href="https://atiq.dev" target="_blank" style="font-weight: 500;">Atiq Samtia</a> | <a href="https://github.com/atiqsamtia/WP-Post-Update-Date-All" target="_blank" style="font-weight: 500;">GitHub</a> | <a href="https://wordpress.org/support/plugin/bulk-post-update-date" target="_blank" style="font-weight: 500;">Support</a> | <a href="https://translate.wordpress.org/projects/wp-plugins/bulk-post-update-date" target="_blank" style="font-weight: 500;">Translate</a> | <a href="https://wordpress.org/support/plugin/bulk-post-update-date/reviews/?rate=5#new-post" target="_blank" style="font-weight: 500;">Rate it</a> (<span style="color:#ffa000;">★★★★★</span>) on WordPress.org, if you like this plugin.</p>
    </div>

    <script>
        jQuery(function(){

            jQuery('input[name="range"]').daterangepicker({
                maxDate: '<?php echo date('m/d/y');?>'
            });

            jQuery('#distribute').change(function(){
                let val = jQuery(this).val();
                if(val == 0)
                    jQuery('#range_row').fadeIn();
                else
                    jQuery('#range_row').fadeOut();

            })

        });
    </script>

<?php
}