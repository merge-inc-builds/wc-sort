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
        <div class="notice notice-error is-dismissible">
            <p><strong>Sort | Error</strong></p>
            <table style="width: 100%">
                <tr>
                    <td>Message</td>
                    <td>
                        <code>
							<?php
							echo wp_kses($data["e"]->getMessage(), NULL)
							?>
                        </code>
                    </td>
                </tr>
                <tr>
                    <td>File</td>
                    <td>
                        <code>
							<?php
							echo wp_kses($data["e"]->getFile(), NULL)
							?>
                        </code>
                    </td>
                </tr>
                <tr>
                    <td>Line</td>
                    <td>
                        <code>
							<?php
							echo wp_kses($data["e"]->getLine(), NULL)
							?>
                        </code>
                    </td>
                </tr>
            </table>
        </div>
		<?php
		return ob_get_clean();
	};
