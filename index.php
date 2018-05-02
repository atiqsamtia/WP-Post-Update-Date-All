<?php
/*
 * Plugin Name: Bulk Post Update Date
 * Version: 1.0.0
 * Description: Change the Post Update date for all posts in one click. This will help your blog in search engines and your blog will look alive. Do this every week or month. (Tip By Waqas Jawed in Bloggers Funda - facebook group)
 * Author: Atiq Samtia
 * Author URI: http://atiqsamtia.com
 * Plugin URI: https://github.com/atiqsamtia/WP-Post-Update-Date-All
 * Text Domain: tb-post-update-all
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


function tb_post_update_all_menu() {
	add_options_page( 'Bulk Post Update Date', 'Bulk Post Update Date', 'manage_options', 'tb-post-update-all', 'tb_post_update_all_options' );
}

add_action( 'admin_menu', 'tb_post_update_all_menu' );

function tb_post_update_all_options() {

	$settings_saved = 0;

	if ( isset( $_POST[ 'do' ] ) ) {

        // get All posts IDs
        //TODO: ADD categories and date filtering functionality in next Release.

        $ids = get_posts(
            array(
                'numberposts' => -1,
                'fields'        => 'ids'
            )
        );

        foreach($ids as $id){

            //Random Seconds to substract from time so that it would not set the same time for every post

            $seconds = rand(1, 60 * 30);

            $time = current_time('mysql');
            $time = strtotime($time);
            $time = $time - $seconds;
            $time = date("Y-m-d H:i:s",$time);

            wp_update_post(
                array (
                    'ID'            => $id,
                    'post_modified_date'     => $time,
                    'post_modified_date_gmt' => get_gmt_from_date( $time )
                )
            );
        }
        $settings_saved = count($ids);
	}

	?>

	<div class="wrap">
		<h1><?php _e( 'Bulk Post Update Date', 'tb-post-update-all' ); ?></h1>
		<?php if ( $settings_saved > 0 ) : ?>
			<div id="message" class="updated fade">
				<p><strong><?php _e( "$settings_saved Posts Update date refreshed." ) ?></strong></p>
			</div>
		<?php endif ?>
		<h2>
			<?php _e( 'refresh the post modified date for all of your articles in one click, click REFRESH now', 'tb-post-update-all' ) ?>
		</h2>
		<form method="post" action="">
			<div>
				

			</div>
			<p class="submit">
				<input class="button-primary" name="do" type="submit" value="<?php _e( 'Refresh' ) ?>" />
			</p>
		</form>
	</div>

<?php
}