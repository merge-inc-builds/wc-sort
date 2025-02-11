<?php
declare(strict_types=1);

namespace MergeInc\WcSort\WordPress\Controller;

use WP_REST_Response;
use MergeInc\WcSort\Sort;
use MergeInc\WcSort\WordPress\Constants;
use MergeInc\WcSort\WordPress\ProductsHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class AjaxMetaKeysCreatorApiExposeController extends AbstractController {

	/**
	 * @return void
	 */
	public function __invoke(): void {
		register_rest_route(
			Constants::WP_JSON_API_BASE_URL,
			Constants::WP_JSON_API_CREATE_META_KEYS,
			array(
				'methods'             => 'GET',
				'callback'            => function (): WP_REST_Response {
					/**
					 * @var ProductsHelper $productsHelper
					 */
					$productsHelper = Sort::construct()->getFromContainer( ProductsHelper::class );

					$lastProcessedPage = $productsHelper->createMetaKeys()['page'];

					return new WP_REST_Response( array( 'nextPageToProcess' => $lastProcessedPage ) );
				},
				'permission_callback' => '__return_true',
			)
		);
	}
}
