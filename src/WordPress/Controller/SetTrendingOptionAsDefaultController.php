<?php
declare(strict_types=1);

namespace MergeInc\WcSort\WordPress\Controller;

use MergeInc\WcSort\WordPress\DataHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SetTrendingOptionAsDefaultController extends AbstractController {

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
	 * @return false|string
	 */
	public function __invoke() {
		return $this->dataHelper->isActivated() && $this->dataHelper->isDefault() ?
			$this->dataHelper->getTrendingOptionNameUrl() : false;
	}
}
