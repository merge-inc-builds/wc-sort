<?php
declare(strict_types=1);

namespace MergeInc\WcSort\WordPress\Controller;

use Exception;
use MergeInc\WcSort\WordPress\DataHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class GetCatalogArgumentsForOrderingController extends AbstractController {

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
	 * @param array $args
	 *
	 * @return array
	 * @throws Exception
	 */
	public function __invoke( array $args ): array {
		if ( ! $this->dataHelper->isActivated() ) {
			return $args;
		}

		if ( ( $_GET['orderby'] ?? null ) === $this->dataHelper->getTrendingOptionNameUrl()
			|| ( ( $_GET['orderby'] ?? null ) === null && $this->dataHelper->isDefault() )
		) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = $this->dataHelper->getTrendingMetaKey();
			$args['order']    = 'DESC';
		}

		return $args;
	}
}
