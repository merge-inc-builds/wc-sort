<?php
declare(strict_types=1);

namespace MergeInc\WcSort\WordPress\Controller;

use MergeInc\WcSort\WordPress\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UpdateCronJobIntervalsController extends AbstractController {

	/**
	 * @param array $schedules
	 *
	 * @return array
	 */
	public function __invoke( array $schedules ): array {
		if ( ! isset( $schedules[ Constants::CRON_INTERVAL_FIFTEEN_MINUTES ] ) ) {
			$schedules[ Constants::CRON_INTERVAL_FIFTEEN_MINUTES ] = array(
				'interval' => 900,
				'display'  => esc_html( 'Every 15 Minutes' ),
			);
		}

		return $schedules;
	}
}
