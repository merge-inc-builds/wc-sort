<?php
if(!defined("ABSPATH")) {
	exit;
}

return
	/**
	 * @param array $data
	 * @return string
	 */
	function(array $data): string {
		ob_start();
		?>
        <div class="wrap">
            <h1><img src="<?
				echo esc_html($data["logoUrl"]) ?>" id="wc-sort-settings-logo" alt=""/> |
				<?php
				echo esc_html($data["title"])
				?>
            </h1>
            <div class="notice notice-info wc-sort-notice-styled">
                <h2>Important Information About Data Collection and Indexes</h2>
                <p>The <strong>SORT</strong> plugin collects sales data to generate indexes for custom product sorting. Please
                    note the
                    following:</p>
                <ol>
                    <li>
                        <strong>Data Collection Starts From Activation:</strong>
                        The plugin begins recording sales data <em>only from the moment it is first activated</em>. Past sales
                        data from
                        before activation is not included in the indexes.
                    </li>
                    <li>
                        <strong>Consistent Data Requires Continuous Activation:</strong>
                        For accurate and consistent data, the plugin must remain <em>continuously activated</em>. If the plugin
                        is
                        deactivated, no sales data will be recorded during that time. This can lead to inconsistencies in the
                        sorting
                        indexes.
                    </li>
                    <li>
                        <strong>Performance Mirroring:</strong>
                        Depending on the volume of orders and products, the actual performance of products in terms of sales
                        will
                        typically be reflected on the website within <em>24-48 hours</em>.
                    </li>
                </ol>
                <p><strong>Recommendation:</strong> To ensure your sorting indexes remain accurate and reliable, avoid
                    deactivating the
                    plugin. If you need to disable it temporarily, consider the impact on your data consistency and re-activate
                    it as
                    soon as possible.</p>
            </div>
            <form action="options.php" method="post">
				<?php
				echo wp_kses($data["pageContent"], $data["commonHtmlElements"])
				?>
            </form>
        </div>
		<?php
		return ob_get_clean();
	};