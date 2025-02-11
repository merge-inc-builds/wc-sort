<?php
declare(strict_types=1);

namespace MergeInc\WcSort\WordPress;

use WC_Order;
use Exception;
use WC_Product;
use ReflectionClass;
use MergeInc\WcSort\Sort;
use MergeInc\WcSort\Globals\SalesEncoder;

final class DataHelper {

	/**
	 * @var SalesEncoder
	 */
	private SalesEncoder $salesEncoder;

	/**
	 * @var array
	 */
	private array $cache = array(
		'orders'   => array(),
		'products' => array(),
	);

	/**
	 * @param SalesEncoder $salesEncoder
	 */
	public function __construct( SalesEncoder $salesEncoder ) {
		$this->salesEncoder = $salesEncoder;
	}

	/**
	 * @param int $id
	 *
	 * @return bool
	 */
	public function isOrderRecorded( int $id ): bool {
		$order = $this->getOrderById( $id );
		if ( ! $order ) {
			return false;
		}

		return $order->get_meta( Constants::META_KEY_ORDER_RECORDED ) === 'yes';
	}

	/**
	 * @param int $id
	 *
	 * @return WC_Order|null
	 */
	public function getOrderById( int $id ): ?WC_Order {
		if ( ! $order = ( $this->cache['orders'][ $id ] ?? false ) ) {
			$order = wc_get_order( $id );
			if ( ! $order ) {
				return null;
			}
			$this->cache['orders']['id'] = $order;
		}

		return $order;
	}

	/**
	 * @param int $id
	 *
	 * @return void
	 */
	public function setOrderRecorded( int $id ): void {
		if ( $order = $this->getOrderById( $id ) ) {
			$order->update_meta_data( Constants::META_KEY_ORDER_RECORDED, 'yes' );
			$order->save();
		}
	}

	/**
	 * @param int $id
	 *
	 * @return void
	 */
	public function deleteOrderRecorded( int $id ): void {
		if ( $order = $this->getOrderById( $id ) ) {
			$order->delete_meta_data( Constants::META_KEY_ORDER_RECORDED );
			$order->save();
		}
	}

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	public function getProductSales( int $id ): array {
		if ( $product = $this->getProductById( $id ) ) {
			return $this->salesEncoder->decode( (string) $product->get_meta( Constants::META_KEY_PRODUCT_SALES ) );
		}

		return array();
	}

	/**
	 * @param int $id
	 *
	 * @return WC_Order|null
	 */
	public function getProductById( int $id ): ?WC_Product {
		if ( ! $product = ( $this->cache['products'][ $id ] ?? false ) ) {
			$product = wc_get_product( $id );
			if ( ! $product ) {
				return null;
			}

			$this->cache['products']['id'] = $product;
		}

		return $product;
	}

	/**
	 * @param int   $id
	 * @param array $sales
	 *
	 * @return void
	 */
	public function setProductSales( int $id, array $sales ): void {
		if ( $product = $this->getProductById( $id ) ) {
			$product->update_meta_data( Constants::META_KEY_PRODUCT_SALES, $this->salesEncoder->encode( $sales ) );
			$product->save();
		}
	}

	/**
	 * @param int $id
	 * @param int $interval
	 *
	 * @return int|null
	 * @throws Exception
	 */
	public function getProductIntervalSalesByInterval( int $id, int $interval ): ?int {
		if ( $product = $this->getProductById( $id ) ) {
			$intervalSales = $product->get_meta( $this->getMetaKeyNameByInterval( $interval ) );

			return is_numeric( $intervalSales ) ? (int) $intervalSales : null;
		}

		return null;
	}

	/**
	 * @param int $interval
	 *
	 * @return string
	 * @throws Exception
	 */
	public function getMetaKeyNameByInterval( int $interval ): string {
		$intervalWord = $this->getWordByInterval( $interval );

		$metaKey = "_wc-sort_{$intervalWord}_sales";

		$class         = new ReflectionClass( Constants::class );
		$constants     = $class->getConstants();
		$metaKeyExists = false;
		foreach ( $constants as $constant => $value ) {
			if ( $metaKey === $value ) {
				$metaKeyExists = true;
			}
		}

		if ( ! $metaKeyExists ) {
			throw new Exception( "Meta key '$metaKey' does not exist" );
		}

		return $metaKey;
	}

	/**
	 * @param int  $interval
	 * @param bool $uppercase
	 *
	 * @return string
	 */
	private function getWordByInterval( int $interval, bool $uppercase = false ): string {
		$intervals = array(
			7   => 'weekly',
			15  => 'biweekly',
			30  => 'monthly',
			90  => 'quarterly',
			180 => 'half_yearly',
			365 => 'yearly',
		);

		$word = $intervals[ $interval ];

		return $uppercase ? strtoupper( $word ) : $word;
	}

	/**
	 * @param int  $id
	 * @param int  $interval
	 * @param int  $sales
	 * @param bool $forceUpdate
	 *
	 * @return WC_Product|null
	 * @throws Exception
	 */
	public function setProductIntervalSalesByInterval(
		int $id,
		int $interval,
		int $sales,
		bool $forceUpdate = false
	): ?WC_Product {
		if ( $product = $this->getProductById( $id ) ) {
			$product->update_meta_data( $this->getMetaKeyNameByInterval( $interval ), $sales );
			if ( $forceUpdate ) {
				$product->save();
			}

			return $product;
		}

		return null;
	}

	/**
	 * @return bool
	 */
	public function isActivated(): bool {
		return get_option( Constants::SETTINGS_FIELDS_ACTIVATED, 'no' ) === 'yes';
	}

	/**
	 * @return bool
	 */
	public function isActivatedDisabled(): bool {
		return get_option( Constants::OPTION_NAME_META_KEYS_ONE_ROUND_COMPLETED ) !== 'yes';
	}

	/**
	 * @return bool
	 */
	public function isDefault(): bool {
		return get_option( Constants::SETTINGS_FIELDS_DEFAULT, 'no' ) === 'yes';
	}

	/**
	 * @return string
	 */
	public function getTrendingLabel(): string {
		return $this->isFreemiumActivated() ?
			( get_option( Constants::SETTINGS_FIELD_TRENDING_LABEL ) ?: Constants::DEFAULT_TRENDING_LABEL ) :
			Constants::DEFAULT_TRENDING_LABEL;
	}

	/**
	 * @return bool
	 */
	public function isFreemiumActivated(): bool {
		return get_option( Constants::SETTINGS_FIELDS_FREEMIUM_ACTIVATED, 'no' ) === 'yes';
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function getTrendingMetaKey(): string {
		return $this->getMetaKeyNameByInterval( $this->getTrendingInterval() );
	}

	/**
	 * @return int
	 */
	public function getTrendingInterval(): int {
		return $this->isFreemiumActivated() ? (int) ( get_option( Constants::SETTINGS_FIELD_TRENDING_INTERVAL ) ?: 30 ) : 7;
	}

	/**
	 * @return string
	 */
	public function getTrendingOptionNameUrl(): string {
		return $this->isFreemiumActivated() ?
			( get_option( Constants::SETTINGS_FIELD_TRENDING_OPTION_NAME_URL ) ?: Constants::DEFAULT_TRENDING_OPTION_NAME_URL ) :
			Constants::DEFAULT_TRENDING_OPTION_NAME_URL;
	}

	/**
	 * @param string $size
	 *
	 * @return string
	 */
	public function getLogoUrl( string $size = '32' ): string {
		return "{$this->getAppUrl()}/assets/icon-{$size}x$size.png";
	}

	/**
	 * @return string
	 */
	public function getAppUrl(): string {
		return plugin_dir_url( Sort::FILE );
	}

	/**
	 * @return string
	 */
	public function getAppRoot(): string {
		return plugin_dir_path( Sort::FILE );
	}

	/**
	 * @param string $size
	 *
	 * @return string
	 */
	public function getBannerUrl( string $size = '1544x500' ): string {
		return "{$this->getAppUrl()}/assets/banner-$size.jpg";
	}

	/**
	 * @return array
	 */
	public function getCommonHtmlElements(): array {
		$commonAttributes      =
			array(
				'id'           => array(),
				'style'        => array(),
				'class'        => array(),
				'scope'        => array(),
				'data-tooltip' => array(),
				'type'         => array(),
				'name'         => array(),
				'value'        => array(),
				'disabled'     => array( 'disabled' => array() ),
				'src'          => array(),
				'alt'          => array(),
				'checked'      => array(),
				'method'       => array(),
				'action'       => array(),
				'href'         => array(),
				'selected'     => array(),
			);
		$commonTableAttributes = array_merge( array( 'colspan' => array() ), $commonAttributes );

		return array(
			'td'     => $commonTableAttributes,
			'tr'     => $commonTableAttributes,
			'th'     => $commonTableAttributes,
			'table'  => $commonTableAttributes,
			'span'   => $commonAttributes,
			'div'    => $commonAttributes,
			'br'     => $commonAttributes,
			'strong' => $commonAttributes,
			'input'  => $commonAttributes,
			'ol'     => $commonAttributes,
			'li'     => $commonAttributes,
			'img'    => $commonAttributes,
			'h1'     => $commonAttributes,
			'h2'     => $commonAttributes,
			'p'      => $commonAttributes,
			'hr'     => $commonAttributes,
			'a'      => $commonAttributes,
			'form'   => $commonAttributes,
			'select' => $commonAttributes,
			'option' => $commonAttributes,
		);
	}
}
