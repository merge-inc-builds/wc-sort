<?php
declare(strict_types=1);

namespace MergeInc\WcSort\WordPress\Controller;

use MergeInc\WcSort\WordPress\Constants;
use MergeInc\WcSort\WordPress\DataHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class PageDetectorAndDataInjectionController extends AbstractController {

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
	 * @param array $data
	 *
	 * @return array
	 */
	public function __invoke( array $data ): array {
		$screen = get_current_screen();

		$data['dev'] = str_contains( get_site_url(), 'localhost' );

		/**
		 * TODO: Add more pages (product edit, category, etc)
		 */
		$data['page'] = null;
		if ( $screen && $screen->base === 'edit' && $screen->post_type === 'product' ) {
			$data['page'] = 'product-listing';
		}

		if ( ( $_GET['page'] ?? null ) === Constants::ADMIN_MENU_PAGE_SLUG ) {
			$data['page'] = 'settings-page';
		}

		$data['externalUrlMessage']          = '/wp-json/' . Constants::WP_JSON_API_BASE_URL . '/' . Constants::WP_JSON_API_MESSAGE_URL;
		$data['externalUrlMetaKeysCreation'] =
			'/wp-json/' . Constants::WP_JSON_API_BASE_URL . '/' . Constants::WP_JSON_API_CREATE_META_KEYS;

		$data['settings']                      = array();
		$data['settings']['freemiumActivated'] = $this->dataHelper->isFreemiumActivated();

		$data['settings']['defaults'][ Constants::SETTINGS_FIELD_TRENDING_LABEL ]           = Constants::DEFAULT_TRENDING_LABEL;
		$data['settings']['defaults'][ Constants::SETTINGS_FIELD_TRENDING_INTERVAL ]        = '7';
		$data['settings']['defaults'][ Constants::SETTINGS_FIELD_TRENDING_OPTION_NAME_URL ] =
			Constants::DEFAULT_TRENDING_OPTION_NAME_URL;

		return $data;
	}
}
