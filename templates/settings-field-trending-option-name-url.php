<?php

use MergeInc\WcSort\WordPress\Constants;

if(!defined("ABSPATH")) {
	exit;
}

return
	/**
	 * @param array $data
	 *
	 * @return string
	 */
	function(array $data): string {
		ob_start();
		?>
        <input
                type="text"
                class="regular-text"
                id="<?php
				echo esc_html(Constants::SETTINGS_FIELD_TRENDING_OPTION_NAME_URL) ?>"
                name="<?php
				echo esc_html(Constants::SETTINGS_FIELD_TRENDING_OPTION_NAME_URL)
				?>"
                value="<?php
				echo esc_html($data["value"]) ?>"
			<?php
			echo esc_html($data["freemiumActivated"] ? "" : "disabled=\"disabled\"") ?> />
		<?php
		return ob_get_clean();
	};
