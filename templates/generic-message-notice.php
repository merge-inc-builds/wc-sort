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
        <div id="wc-sort-generic-message-container"
             class="notice woocommerce-message woocommerce-admin-promo-messages is-dismissible wc-sort-notice-hidden wc-sort-notice-styled">
            <div id="wc-sort-paragraph-imitator">
                <div id="wc-sort-message-title">
                    <img src="<?=$data["logoUrl"]?>" id="wc-sort-notice-logo" alt=""/> | Sales Order Ranking Tool
                </div>
                <div id="wc-sort-generic-message"></div>
            </div>
        </div>
		<?php
		return ob_get_clean();
	};
