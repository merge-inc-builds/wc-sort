<?php
declare(strict_types=1);

namespace MergeInc\WcSort\WordPress\Controller;

use MergeInc\WcSort\Globals\Engine;
use MergeInc\WcSort\WordPress\Constants;
use MergeInc\WcSort\WordPress\DataHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class AdminNoticesController extends AbstractController {

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
		if ( ( $_GET['page'] ?? null ) === Constants::ADMIN_MENU_PAGE_SLUG ) {
			return;
		}

		echo $this->engine->render( 'generic-message-notice', array( 'logoUrl' => $this->dataHelper->getLogoUrl( '16' ) ), true );
	}
}
