<?php
declare(strict_types=1);

namespace MergeInc\WcSort\WordPress\Controller;

use MergeInc\WcSort\Globals\Engine;
use MergeInc\WcSort\WordPress\Constants;
use MergeInc\WcSort\WordPress\DataHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MenuPageRegistrationController extends AbstractController {

	/**
	 * @var Engine
	 */
	private Engine $engine;

	/**
	 * @var DataHelper
	 */
	private DataHelper $dataHelper;

	/**
	 * @param Engine     $engine
	 * @param DataHelper $dataHelper
	 */
	public function __construct( Engine $engine, DataHelper $dataHelper ) {
		$this->engine     = $engine;
		$this->dataHelper = $dataHelper;
	}

	/**
	 * @return void
	 */
	public function __invoke(): void {
		add_menu_page(
			esc_html( 'Settings' ),
			esc_html( 'SORT' ),
			'manage_options',
			Constants::ADMIN_MENU_PAGE_SLUG,
			function () {
				if ( ! current_user_can( 'manage_options' ) ) {
					return;
				}

				ob_start();
				settings_fields( Constants::ADMIN_MENU_OPTION_GROUP );
				do_settings_sections( Constants::ADMIN_MENU_PAGE_SLUG );
				submit_button( esc_html( 'Save Settings' ) );
				$pageContent = ob_get_clean();
				echo wp_kses(
					$this->engine->render(
						'settings-page',
						array(
							'logoUrl'            => $this->dataHelper->getLogoUrl(),
							'bannerUrl'          => $this->dataHelper->getBannerUrl(),
							'title'              => esc_html( get_admin_page_title() ),
							'pageContent'        => $pageContent,
							'commonHtmlElements' => $this->dataHelper->getCommonHtmlElements(),
						),
						true
					),
					$this->dataHelper->getCommonHtmlElements()
				);
			},
			'dashicons-sort',
			20,
		);
	}
}
