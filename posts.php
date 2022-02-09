<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
                <tr valign="top">
                    <th scope="row"><label for="categories"><?php _e( 'Select Categories', 'bulk-post-update-date' ); ?></label></th>
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
                            <?php _e( 'Will apply on all posts if no category is selected. Select multiple categories by holding Ctrl or Command key while selecting.', 'bulk-post-update-date' ); ?>
                        </p>
                    </td>
                </tr>