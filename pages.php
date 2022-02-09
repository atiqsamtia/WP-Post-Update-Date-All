<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
                <tr valign="top">
                    <th scope="row"><label for="pages"><?php _e( 'Select pages', 'bulk-post-update-date' ); ?></label></th>
                    <td>

                        <select multiple="multiple" id="pages" name="pages[]">

		                    <?php
		                    $args       = array(
			                    'sort_column' => 'post_title',
                               // 'exclude' => '1'
		                    );
		                    $pages = get_pages( $args );
		                    foreach ( $pages as $page ) { ?>

                                <option value="<?php echo $page->ID; ?>">
				                    <?php echo $page->post_title; ?>
                                </option>
		                    <?php } //endforeach ?>
                        </select>
                            <p class="description">
                                <?php _e( 'Will apply on all pages if no page is selected. Select multiple pages by holding Ctrl or Command key while selecting.', 'bulk-post-update-date' ); ?>
                            </p>
                    </td>
                </tr>