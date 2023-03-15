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

                <tr valign="top">
                    <th scope="row"><label for="tags"><?php _e( 'Select Tags', 'bulk-post-update-date' ); ?></label></th>
                    <td>

                        <select multiple="multiple" id="tags" name="tags[]">

		                    <?php
		                    $args       = array(
			                    'orderby' => 'name',
                               // 'exclude' => '1'
		                    );
		                    $tags = get_tags( $args );
		                    foreach ( $tags as $tag ) { ?>

                                <option value="<?php echo $tag->slug; ?>">
				                    <?php echo $tag->name . ' (' . $tag->count . ')'; ?>
                                </option>
		                    <?php } //endforeach ?>
                        </select>
                            <p class="description">
                            <?php _e( 'Will apply on all posts if no tag is selected. Select multiple tags by holding Ctrl or Command key while selecting.', 'bulk-post-update-date' ); ?>
                        </p>
                    </td>
                </tr>