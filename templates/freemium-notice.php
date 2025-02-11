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
        </td></tr>
        <tr>
            <th scope="row" colspan="2" id="wc-sort-freemium-notice">
                Unlock powerful freemium features like customizable sorting labels, SEO-friendly sorting URLs, and full control
                over
                time intervals—all automated for your convenience.
                <br>
                By enabling, you agree to share your website's admin email and domain
                name—no extra steps required.
                <br><br>
                <strong>Rest assured, we value your privacy and promise never to spam you!</strong>
            </th>
        </tr>
        <tr>
            <td>
		<?php
		return ob_get_clean();
	};