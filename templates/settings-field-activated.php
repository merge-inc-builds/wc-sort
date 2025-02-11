<?php

use MergeInc\WcSort\WordPress\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return /**
	 * @param array $data
	 *
	 * @return string
	 */
	function ( array $data ): string {
		ob_start();
		?>
		<input type="checkbox"
				value="yes"
			<?php
			echo esc_html( $data['checked'] ? 'checked' : '' )
			?>
				id="
				<?php
				echo esc_html( Constants::SETTINGS_FIELDS_ACTIVATED )
				?>
				"
				name="
				<?php
				echo esc_html( Constants::SETTINGS_FIELDS_ACTIVATED )
				?>
				"
			<?php
			echo esc_html( ( $data['disabled'] ?? false ) ? 'disabled="disabled"' : '' )
			?>
			/>
		<?php
		if ( ( $data['disabled'] ?? false ) ) :
			?>
			<span>
				This option is disabled because not all products are fully processed yet.
				This typically happens automatically in the background and may take some time.
				<br>If you want to process the products manually,
				<a href="#" id="wc-sort-start-products-creation-ajax">click here</a>.
				<em>Please note:</em> This process may take some time, and you should not close or refresh
				the page while the keys are being created.
				<span id="wc-sort-meta-keys-progress"></span>
			</span>
			<?php
		endif
		?>
		<?php
		return ob_get_clean();
	};
