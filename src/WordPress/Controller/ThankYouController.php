<?php
declare(strict_types=1);

namespace MergeInc\WcSort\WordPress\Controller;

use Exception;
use MergeInc\WcSort\WordPress\DataHelper;
use MergeInc\WcSort\WordPress\OrderRecorder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ThankYouController extends AbstractController {

	/**
	 * @var OrderRecorder
	 */
	private OrderRecorder $orderRecorder;

	/**
	 * @var DataHelper
	 */
	private DataHelper $dataHelper;

	/**
	 * @param OrderRecorder $orderRecorder
	 * @param DataHelper    $dataHelper
	 */
	public function __construct( OrderRecorder $orderRecorder, DataHelper $dataHelper ) {
		$this->orderRecorder = $orderRecorder;
		$this->dataHelper    = $dataHelper;
	}

	/**
	 * @param int $orderId
	 *
	 * @return void
	 * @throws Exception
	 */
	public function __invoke( int $orderId ): void {
		if ( $order = $this->dataHelper->getOrderById( $orderId ) ) {
			$this->orderRecorder->record( $order );
		}
	}
}
