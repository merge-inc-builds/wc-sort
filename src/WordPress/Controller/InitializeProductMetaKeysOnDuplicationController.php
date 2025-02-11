<?php
declare(strict_types=1);

namespace MergeInc\WcSort\WordPress\Controller;

use Exception;
use WC_Product;
use MergeInc\WcSort\WordPress\DataHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class InitializeProductMetaKeysOnDuplicationController extends AbstractController {

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
	 * @param WC_Product $duplicate
	 * @param WC_Product $product
	 *
	 * @return void
	 * @throws Exception
	 */
	public function __invoke( WC_Product $duplicate, WC_Product $product ): void {
		$this->dataHelper->setProductSales( $duplicate->get_id(), array() );

		foreach ( array( 7, 15, 30, 90, 180, 365 ) as $interval ) {
			$this->dataHelper->setProductIntervalSalesByInterval(
				$duplicate->get_id(),
				$interval,
				0
			);
		}
	}
}
