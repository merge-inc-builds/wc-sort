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
        <input type="checkbox"
               value="yes"
			<?php
			echo esc_html($data["checked"] ? "checked" : "") ?>
               id="<?php
			   echo esc_html(Constants::SETTINGS_FIELDS_DEFAULT) ?>"
               name="<?php
			   echo esc_html(Constants::SETTINGS_FIELDS_DEFAULT)
			   ?>"/>
		<?php
		return ob_get_clean();
	};
