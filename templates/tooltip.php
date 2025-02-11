<?php
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
        <span class="wc-sort-tooltip" data-tooltip="<?php
		echo esc_html($data["text"]) ?>">?</span>
		<?php
		return ob_get_clean();
	};