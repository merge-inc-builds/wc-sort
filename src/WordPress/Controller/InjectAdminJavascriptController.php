<?php
declare(strict_types=1);

namespace MergeInc\WcSort\WordPress\Controller;

use MergeInc\WcSort\Sort;
use MergeInc\WcSort\WordPress\Constants;
use MergeInc\WcSort\WordPress\DataHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class InjectAdminJavascriptController extends AbstractController {

	/**
	 * @var DataHelper
	 */
	private DataHelper $dataHelper;

	/**
	 * @param DataHelper $dataHelper
	 */
	public function __construct( DataHelper $dataHelper ) {
		$this->dataHelper = $dataHelper;
	}

	/**
	 * @return void
	 */
	public function __invoke(): void {
		echo wp_kses( "<div id='wc-sort-frontend-admin'></div>", $this->dataHelper->getCommonHtmlElements() );

		$version = $this->isDev() ? hash( 'crc32', (string) microtime( true ) ) : Sort::VERSION;

		wp_enqueue_script(
			Constants::HANDLE_ADMIN_FRONTEND,
			"{$this->dataHelper->getAppUrl()}/frontend/admin/dist/js/admin.js",
			false,
			$version
		);

		wp_enqueue_style(
			Constants::HANDLE_ADMIN_FRONTEND,
			"{$this->dataHelper->getAppUrl()}/frontend/admin/dist/css/admin.css",
			false,
			$version
		);

		$data = apply_filters( Constants::FILTER_ADMIN_DATA, array( 'sort' => true ) );
		wp_localize_script( Constants::HANDLE_ADMIN_FRONTEND, Constants::HANDLE_ADMIN_FRONTEND_DATA, $data );
	}

	/**
	 * @return bool
	 */
	private function isDev(): bool {
		return str_contains( get_site_url(), 'localhost' );
	}
}
