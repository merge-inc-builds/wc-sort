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
		<select
				id="
				<?php
				echo esc_html( Constants::SETTINGS_FIELD_TRENDING_INTERVAL )
				?>
				"
				name="
				<?php
				echo esc_html( Constants::SETTINGS_FIELD_TRENDING_INTERVAL )
				?>
				"
		>
			<?php
			foreach ( $data['intervals'] as $interval ) :
				?>
				<option 
				<?php
				echo esc_html( $data['freemiumActivated'] ? '' : ( $interval > 7 ? 'disabled="disabled"' : '' ) )
				?>
						value="
						<?php
						echo esc_html( $interval )
						?>
						"
					<?php
					echo esc_html(
						$data['value'] ===
						$interval ? 'selected' : ''
					);
					?>
					>
					<?php
					echo esc_html( $interval )
					?>
					<?php
					echo esc_html( $data['daysLabel'] )
					?>
					</option>
				<?php
			endforeach
			?>
		</select>
		<?php
		return ob_get_clean();
	};
