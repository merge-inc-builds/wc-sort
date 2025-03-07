<?php
declare(strict_types=1);

namespace MergeInc\WcSort\WordPress\Controller;

use Exception;
use MergeInc\WcSort\WordPress\ProductsHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class RunProductsMetaKeysCreationActionController extends AbstractController {

	/**
	 * @var ProductsHelper
	 */
	private ProductsHelper $productsHelper;

	/**
	 * @param ProductsHelper $productsHelper
	 */
	public function __construct( ProductsHelper $productsHelper ) {
		$this->productsHelper = $productsHelper;
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	final function __invoke(): void {
		$time             = microtime( true );
		$info             = $this->productsHelper->createMetaKeys();
		$batchSize        = $info['batchSize'];
		$sampleProductIds = implode( ',', $info['sampleProductIds'] ?? array() );
		$end              = round( microtime( true ) - $time, 3 );
		$date             = date( 'Y-m-d H:i:s' );
		$output           = "$date -- Batch size: '$batchSize'";
		$output          .= PHP_EOL;
		$output          .= "$date -- Sample Product IDs: $sampleProductIds";
		$output          .= PHP_EOL;
		$output          .= "$date -- Sort meta keys were created in {$end}s";
		$output          .= PHP_EOL;

		echo $output;
	}
}
