<?php
declare(strict_types=1);

namespace MergeInc\WcSort\WordPress\Controller;

use MergeInc\WcSort\WordPress\DataHelper;
use Automattic\WooCommerce\Utilities\FeaturesUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class DeclareHposCompatibilityController extends AbstractController {

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
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', "{$this->dataHelper->getAppRoot()}/wc-sort.php" );
		}
	}
}
