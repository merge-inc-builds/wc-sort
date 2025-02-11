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
				echo esc_html( Constants::SETTINGS_FIELDS_FREEMIUM_ACTIVATED )
				?>
				"
				name="
				<?php
				echo esc_html( Constants::SETTINGS_FIELDS_FREEMIUM_ACTIVATED )
				?>
				"/>
		<?php
		return ob_get_clean();
	};
