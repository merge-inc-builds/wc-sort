<?php
declare(strict_types=1);

namespace MergeInc\WcSort\WordPress;

use Exception;
use WC_Product;
use WC_Product_Query;
use MergeInc\WcSort\Globals\SalesCalculator;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ProductsHelper {

	/**
	 * @var bool|null
	 */
	private ?bool $haveAllProductsSortMetaKeys = null;

	/**
	 * @var DataHelper
	 */
	private DataHelper $dataHelper;

	/**
	 * @var SalesCalculator
	 */
	private SalesCalculator $salesCalculator;

	/**
	 * @param DataHelper      $dataHelper
	 * @param SalesCalculator $salesCalculator
	 */
	public function __construct( DataHelper $dataHelper, SalesCalculator $salesCalculator ) {
		$this->dataHelper      = $dataHelper;
		$this->salesCalculator = $salesCalculator;
	}

	/**
	 * @return bool
	 * @throws Exception
	 */
	public function haveAllProductsSortMetaKeys(): bool {
		if ( $this->haveAllProductsSortMetaKeys !== null ) {
			return $this->haveAllProductsSortMetaKeys;
		}

		$query = new WC_Product_Query(
			array(
				'limit'   => 1,
				'orderby' => 'rand',
				'status'  => 'publish',
			)
		);

		$products = $query->get_products();

		/**
		 * @var WC_Product $product
		 */
		$product = ! empty( $products ) ? $products[0] : null;
		if ( ! $product ) {
			return false;
		}

		foreach ( array( 7, 15, 30, 90, 180, 365 ) as $interval ) {
			$productSales = $this->dataHelper->getProductIntervalSalesByInterval( $product->get_id(), $interval );

			if ( $productSales === null ) {
				return $this->haveAllProductsSortMetaKeys = false;
			}
		}

		return $this->haveAllProductsSortMetaKeys = true;
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	function createMetaKeys( bool $forceRestart = false ): array {
		$batchSize         = $this->calculateMemoryBasedBatchSize();
		$lastProcessedPage = (int) get_option( Constants::OPTION_NAME_LAST_PROCESSED_PAGE, 1 );
		if ( $forceRestart || $lastProcessedPage < 1 ) {
			$lastProcessedPage = 1;
		}

		$args = array(
			'limit'   => $batchSize,
			'orderby' => 'ID',
			'order'   => 'ASC',
			'status'  => 'publish',
			'return'  => 'ids',
			'page'    => $lastProcessedPage,
		);

		$query      = new WC_Product_Query( $args );
		$productIds = $query->get_products();

		if ( is_array( $productIds ) && ! empty( $productIds ) ) {
			foreach ( $productIds as $productId ) {
				$productId    = (int) $productId;
				$productSales = $this->dataHelper->getProductSales( $productId );
				$product      = wc_get_product( $productId );
				if ( ! $product ) {
					continue;
				}

				foreach ( array( 7, 15, 30, 90, 180, 365 ) as $interval ) {
					$product->update_meta_data(
						$this->dataHelper->getMetaKeyNameByInterval( $interval ),
						$this->salesCalculator->getSalesByInterval( $productSales, $interval )
					);
				}

				$product->save();
			}

			update_option( Constants::OPTION_NAME_LAST_PROCESSED_PAGE, $lastProcessedPage + 1 );
		} else {
			delete_option( Constants::OPTION_NAME_LAST_PROCESSED_PAGE );
			update_option( Constants::OPTION_NAME_META_KEYS_ONE_ROUND_COMPLETED, 'yes' );
		}

		$sample = (int) ceil( $batchSize * 0.10 );

		return array(
			'page'             => get_option( Constants::OPTION_NAME_LAST_PROCESSED_PAGE, 0 ),
			'batchSize'        => $batchSize,
			'sampleProductIds' => array_slice( $productIds, rand( 0, count( $productIds ) - ( $sample ) + 1 ), $sample ),
		);
	}

	/**
	 * @return int
	 * @throws Exception
	 */
	private function calculateMemoryBasedBatchSize(): int {
		$memoryLimit          = ini_get( 'memory_limit' );
		$baseMemoryLimitBytes = 256 * 1024 * 1024;
		$currentUsage         = memory_get_usage();

		$memoryLimitBytes = (int) filter_var( $memoryLimit, FILTER_SANITIZE_NUMBER_INT ) * 1024 * 1024;
		if ( $memoryLimitBytes < $baseMemoryLimitBytes ) {
			$memoryLimitBytes = $baseMemoryLimitBytes;
		}
		$availableMemory = $memoryLimitBytes - $currentUsage;

		// 6 are the meta keys to be set
		$memoryPerProduct = 100000 * 6;

		$batchSize = (int) floor( $availableMemory / $memoryPerProduct );

		return max( 10, min( 50, $batchSize ) );
	}
}
