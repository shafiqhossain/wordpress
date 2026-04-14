<?php
function __cmma_hero_contact_details() {
	ob_start();
	$name         = get_field( 'contact_details_contact_name' );
	$title        = get_field( 'contact_details_title' );
	$address      = get_field( 'contact_details_address' );
	$email        = get_field( 'contact_details_email' );
	$phone        = get_field( 'contact_details_phone_number' );
	$contact_info = $name;

	if ( $title ) :
		$contact_info .= ', ' . $title;
	endif;

	if ( $address ) :
		$contact_info .= ', ' . $address;
	endif;

	if ( ! empty( $contact_info ) || ! empty( $email ) || ! empty( $phone ) ) :
		?>
		<div class="cmma-contact-details ">
			<h5>Questions? Contact:</h5>
			<div class="contact-info"><?= $contact_info; ?></div>
			<?php if ( $email ) : ?><a href="mailto:<?= $email; ?>">Email</a><?php endif; ?>
			<?php if ( $phone ) : ?><a href="tel:<?= $phone; ?>"><?= $phone; ?></a><?php endif; ?>
		</div>
	<?php
	endif;

	return ob_get_clean();
}
add_shortcode( 'cmma_hero_contact_details', '__cmma_hero_contact_details' );
