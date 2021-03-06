<?php

// retrieve all purchases for the current user
$purchases = edd_get_users_purchases();

if($purchases) : ?>
	<table id="edd_user_history">
		<thead>
			<tr class="edd_purchase_row">
				<?php do_action('edd_purchase_history_header_before'); ?>
				<th class="edd_purchase_id"><?php _e('Purchase ID', 'edd'); ?></th>
				<th class="edd_purchase_date"><?php _e('Date', 'edd'); ?></th>
				<th class="edd_purchase_amount"><?php _e('Amount', 'edd'); ?></th>
				<th class="edd_purchased_files"><?php _e('Files', 'edd'); ?></th>
				<?php do_action('edd_purchase_history_header_after'); ?>
			</tr>
		</thead>
		<?php foreach($purchases as $post) : setup_postdata( $post ); ?>
			<?php $purchase_data = edd_get_payment_meta( $post->ID ); ?>
			<?php do_action('edd_purchase_history_body_start', $purchase, $purchase_data); ?>
			<tr class="edd_purchase_row">
				<td class="edd_purchase_id">#<?php echo absint( $post->ID ); ?></td>
				<td class="edd_purchase_date"><?php echo date_i18n(get_option('date_format'), strtotime( get_post_field( 'post_date', $post->ID ) ) ); ?></td>
				<td class="edd_purchase_amount"><?php echo edd_currency_filter( $purchase_data['amount'] ); ?></td>
				<td class="edd_purchased_files">
					<?php
						// show a list of downloadable files
						$downloads = edd_get_downloads_of_purchase($post->ID);
						if($downloads) {
							foreach($downloads as $download) {
								$id = isset($purchase_data['cart_details']) ? $download['id'] : $download;
								$price_id = isset($download['options']['price_id']) ? $download['options']['price_id'] : null;
								$download_files = edd_get_download_files( $id, $price_id );
								echo '<div class="edd_purchased_download_name">' . esc_html( get_the_title($id) ) . '</div>';
								if( ! edd_no_redownload() ) {
									if($download_files) {
										foreach($download_files as $filekey => $file) {
											$download_url = edd_get_download_file_url($purchase_data['key'], $purchase_data['email'], $filekey, $id);
											echo '<div class="edd_download_file"><a href="' . esc_url( $download_url ) . '" class="edd_download_file_link">' . esc_html( $file['name'] ) . '</a></div>';
										} 
									} else {
										_e('No downloadable files found.', 'edd');
									}
								}
							}
						}
					?>
				</td>
			</tr>
			<?php do_action('edd_purchase_history_body_end', $purchase, $purchase_data); ?>
		<?php endforeach; ?>
	</table>
<?php else : ?>
	<p class="edd-no-purchases"><?php _e('You have not made any purchases', 'edd'); ?></p>
<?php endif;