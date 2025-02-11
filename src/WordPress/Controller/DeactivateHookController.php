<?php
declare(strict_types=1);

namespace MergeInc\WcSort\WordPress\Controller;

use MergeInc\WcSort\WordPress\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class DeactivateHookController extends AbstractController {

	/**
	 * @return callable
	 */
	public function getDeactivationHook(): callable {
		return function (): void {
			$timestamp = wp_next_scheduled( Constants::ACTION_CREATE_PRODUCTS_META_KEYS );
			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, Constants::ACTION_CREATE_PRODUCTS_META_KEYS );
			}

			$timestamp = wp_next_scheduled( Constants::ACTION_REGISTER_SUBSCRIBER );
			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, Constants::ACTION_REGISTER_SUBSCRIBER );
			}
		};
	}
}
