<?php
/*
 * Plugin Name: Bulk Post Update Date
 * Version: 1.1.2
 * Description: Change the Post Update date for all posts in one click. This will help your blog in search engines and your blog will look alive. Do this every week or month. (Tip By Waqas Jawed in Bloggers Funda - facebook group)
 * Author: Atiq Samtia
 * Author URI: http://atiqsamtia.com
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

	$now = current_time( 'timestamp', 0 );;


	if ( isset( $_POST[ 'tb_refresh' ] ) && wp_verify_nonce( $_POST['tb_refresh'],'tb-refresh' ) && current_user_can( 'manage_options' ) ) {

        // get All posts IDs

        $params = array(
		        'numberposts' => -1,
		        'fields'        => 'ids'
	        );

        if(isset( $_POST['categories'])){
            $params['cat'] = implode( ',', $_POST['categories']);
        }

        $ids = get_posts(
                $params
        );

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

	        $time = rand($from,$to);
            $time = date("Y-m-d H:i:s",$time);

            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE $wpdb->posts SET post_modified='%s', post_modified_gmt='%s' WHERE ID = %d",
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

	?>
    <style>
      .form-table input, .form-table select{
          min-width: 250px;
      }
    </style>

	<div class="wrap">
		<h1><?php _e( 'Bulk Post Update Date', 'bulk-post-update-date' ); ?></h1>
		<?php if ( $settings_saved > 0 ) : ?>
			<div id="message" class="updated fade">
				<p><strong><?php _e( "$settings_saved Posts Update date refreshed." ) ?></strong></p>
			</div>
		<?php endif ?>
		<p>
			<?php _e( 'Change the Post Update date for all posts in one click. This will help your blog in search engines and your blog will look alive. Do this every week or month.', 'bulk-post-update-date' ) ?>
        </p>
        <p>
            <?php _e( 'A small yet powerful utility  provided by <a href="http://atiqsamtia.com">Atiq Samtia</a>', 'bulk-post-update-date' ) ?>
		</p>
		<form method="post" action="">

            <table class="form-table">

                <tr valign="top">
                    <th scope="row"><label for="distribute">Distribute into Last</label></th>
                    <td>

                        <select type="text" id="distribute" name="distribute">
                            <option value="<?php echo strtotime( '-1 hour',$now);?>">1 hour</option>
                            <option value="<?php echo strtotime( '-1 day',$now);?>">1 Day</option>
                            <option value="<?php echo strtotime( '-15 days',$now);?>">15 Days</option>
                            <option value="<?php echo strtotime( '-1 month',$now);?>">1 Month</option>
                            <option value="<?php echo strtotime( '-2 month',$now);?>">2 Months</option>
                            <option value="<?php echo strtotime( '-3 month',$now);?>">3 Months</option>
                            <option value="<?php echo strtotime( '-6 month',$now);?>">6 Months</option>
                            <option value="0">Custom Range</option>
                        </select>
                        <p class="description">
                            Select range of date in which you want to spread the dates of posts to look more realistic.
                        </p>
                    </td>
                </tr>
                <tr id="range_row" valign="top" style="display: none;">
                    <th scope="row"><label for="range">Custom Date Range</label></th>
                    <td>

                        <input type="text" id="range" name="range" value="<?php echo date('m/d/y',strtotime( '-3 days',$now));?> - <?php echo date('m/d/y',$now);?>" />
                        <p class="description">
                            Select range of date in which you want to spread the dates of posts to look more realistic.
                        </p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label for="categories">Select Categories</label></th>
                    <td>

                        <select multiple="multiple" id="categories" name="categories[]">

		                    <?php
		                    $args       = array(
			                    'orderby' => 'name',
                               // 'exclude' => '1'
		                    );
		                    $categories = get_categories( $args );
		                    foreach ( $categories as $category ) { ?>

                                <option value="<?php echo $category->term_id; ?>">
				                    <?php echo $category->cat_name . ' (' . $category->category_count . ')'; ?>
                                </option>
		                    <?php } //endforeach ?>
                        </select>
                            <p class="description">
                            Will apply on all posts if no category is selected. Select multiple categories by holding Ctrl or Command key while selecting.
                        </p>
                    </td>
                </tr>

            </table>

			<p class="submit">
                <input name="tb_refresh" type="hidden" value="<?php echo wp_create_nonce('tb-refresh'); ?>" />
				<input class="button-primary" name="do" type="submit" value="<?php _e( 'Update Post Dates' ) ?>" />
			</p>
		</form>
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