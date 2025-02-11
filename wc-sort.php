<?php
declare(strict_types=1);

namespace MergeInc\WcSort;

use Error;
use Exception;
use MergeInc\WcSort\Globals\Engine;
use MergeInc\WcSort\Globals\FileHelper;
use MergeInc\WcSort\WordPress\Constants;
use MergeInc\WcSort\Globals\SalesEncoder;
use MergeInc\WcSort\WordPress\DataHelper;
use MergeInc\WcSort\WordPress\HttpClient;
use MergeInc\WcSort\Globals\SalesCalculator;
use MergeInc\WcSort\WordPress\OrderRecorder;
use MergeInc\WcSort\WordPress\ProductsHelper;
use MergeInc\WcSort\WordPress\Controller\ThankYouController;
use MergeInc\WcSort\WordPress\Controller\ControllerRegistrar;
use MergeInc\WcSort\WordPress\Controller\MessageProxyController;
use MergeInc\WcSort\WordPress\Controller\OrderDeletedController;
use MergeInc\WcSort\WordPress\Controller\OrderUpdatedController;
use MergeInc\WcSort\WordPress\Controller\AdminNoticesController;
use MergeInc\WcSort\WordPress\Controller\DeactivateHookController;
use MergeInc\WcSort\WordPress\Controller\MenuPageRegistrationController;
use MergeInc\WcSort\WordPress\Controller\SettingsRegistrationController;
use MergeInc\WcSort\WordPress\Controller\InjectAdminJavascriptController;
use MergeInc\WcSort\WordPress\Controller\UpdateCronJobIntervalsController;
use MergeInc\WcSort\WordPress\Controller\DeclareHposCompatibilityController;
use MergeInc\WcSort\WordPress\Controller\SetTrendingOptionAsDefaultController;
use MergeInc\WcSort\WordPress\Controller\RunRegisterSubscriberActionController;
use MergeInc\WcSort\WordPress\Controller\AjaxMetaKeysCreatorApiExposeController;
use MergeInc\WcSort\WordPress\Controller\PageDetectorAndDataInjectionController;
use MergeInc\WcSort\WordPress\Controller\GetCatalogArgumentsForOrderingController;
use MergeInc\WcSort\WordPress\Controller\RunProductsMetaKeysCreationActionController;
use MergeInc\WcSort\WordPress\Controller\InitializeProductMetaKeysOnDuplicationController;
use MergeInc\WcSort\WordPress\Controller\AddTrendingOptionInCategorySortingOptionsController;

/**
 * Plugin Name: Sales Order Ranking Tool
 * Author URI: https://sort.joinmerge.gr
 * Description: A WooCommerce extension designed to enhance your store's product sorting and ranking capabilities. Sort
 * products dynamically using sales data, trends, and other criteria to optimize customer experience and maximize conversions.
 * Version: 5.2.1
 * Author: Merge Inc
 * GitHub Plugin URI: https://github.com/merge-inc-builds/sort
 * Plugin URI: https://sort.joinmerge.gr/sort
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 6.2.0
 * Tested up to: 6.7.1
 * WC requires at least: 7.4.0
 * WC tested up to: 9.6.1
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 *
 * @package MergeInc\WcSort
 */
final class Sort {

	/**
	 *
	 */
	public const VERSION = '5.2.1';


	public const FILE = __FILE__;

	/**
	 * @var Sort|null
	 */
	private static ?Sort $self = null;

	/**
	 * @var array
	 */
	private array $container;

	/**
	 * @return Sort
	 */
	public static function construct(): Sort {
		/**
		 * The singleton pattern
		 */
		if ( self::$self === null ) {
			self::$self = new Sort();
		}

		return self::$self;
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	final public function init(): void {
		$this->autoload();

		/**
		 * @var ControllerRegistrar $controllerRegistrar
		 */
		$controllerRegistrar = $this->getFromContainer( ControllerRegistrar::class );

		$controllerRegistrar->register( 'cron_schedules', UpdateCronJobIntervalsController::class );
		if ( ! wp_next_scheduled( Constants::ACTION_CREATE_PRODUCTS_META_KEYS ) ) {
			wp_schedule_event( time(), Constants::CRON_INTERVAL_FIFTEEN_MINUTES, Constants::ACTION_CREATE_PRODUCTS_META_KEYS );
		}

		$controllerRegistrar->register(
			Constants::ACTION_CREATE_PRODUCTS_META_KEYS,
			RunProductsMetaKeysCreationActionController::class,
		);
		if ( ! wp_next_scheduled( Constants::ACTION_REGISTER_SUBSCRIBER ) ) {
			wp_schedule_event( time(), 'daily', Constants::ACTION_REGISTER_SUBSCRIBER );
		}

		$controllerRegistrar->register(
			Constants::ACTION_REGISTER_SUBSCRIBER,
			RunRegisterSubscriberActionController::class,
		);

		$controllerRegistrar->register( 'woocommerce_thankyou', ThankYouController::class );

		$controllerRegistrar->register( 'woocommerce_order_status_changed', OrderUpdatedController::class );

		$controllerRegistrar->register( 'woocommerce_delete_order', OrderDeletedController::class );

		$controllerRegistrar->register( Constants::FILTER_ADMIN_DATA, PageDetectorAndDataInjectionController::class );

		$controllerRegistrar->register( 'admin_menu', MenuPageRegistrationController::class );

		$controllerRegistrar->register( 'admin_init', SettingsRegistrationController::class );

		$controllerRegistrar->register( 'admin_footer', InjectAdminJavascriptController::class );

		$controllerRegistrar->register(
			'woocommerce_catalog_orderby',
			AddTrendingOptionInCategorySortingOptionsController::class,
			11
		);

		$controllerRegistrar->register( 'admin_notices', AdminNoticesController::class, -99 );

		$controllerRegistrar->register( 'before_woocommerce_init', DeclareHposCompatibilityController::class );

		$controllerRegistrar->register(
			'pre_option_woocommerce_default_catalog_orderby',
			SetTrendingOptionAsDefaultController::class
		);

		$controllerRegistrar->register(
			'woocommerce_get_catalog_ordering_args',
			GetCatalogArgumentsForOrderingController::class,
			11
		);

		$controllerRegistrar->register(
			'woocommerce_product_duplicate',
			InitializeProductMetaKeysOnDuplicationController::class,
			10,
			2
		);

		$controllerRegistrar->register( 'rest_api_init', MessageProxyController::class );

		$controllerRegistrar->register( 'rest_api_init', AjaxMetaKeysCreatorApiExposeController::class );

		/**
		 * @var DataHelper $dataHelper
		 */
		$dataHelper = $this->getFromContainer( DataHelper::class );
		register_deactivation_hook(
			"{$dataHelper->getAppRoot()}/wc-sort.php",
			( new DeactivateHookController() )->getDeactivationHook()
		);
	}

	/**
	 * @return void
	 */
	public function autoload(): void {
		require_once __DIR__ . '/src/Globals/FileHelper.php';
		$fileHelper = new FileHelper();

		$requiredFiles = $fileHelper->getAllFilesRecursively( __DIR__ . '/src', 'php' );

		foreach ( $requiredFiles as $requiredFile ) {
			require_once $requiredFile;
		}
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function getFromContainer( string $key ) {
		if ( ! ( $this->_container ?? false ) ) {
			$fileHelper      = new FileHelper();
			$engine          = new Engine( __DIR__ . '/templates' );
			$salesCalculator = new SalesCalculator();
			$salesEncoder    = new SalesEncoder();
			$dataHelper      = new DataHelper( $salesEncoder );
			$httpClient      = new HttpClient();
			$orderRecorder   = new OrderRecorder( $salesCalculator, $dataHelper );
			$productsHelper  = new ProductsHelper( $dataHelper, $salesCalculator );
			$addTrendingOptionInCategorySortingOptionsController =
				new AddTrendingOptionInCategorySortingOptionsController( $dataHelper );
			$adminNoticesController                              = new AdminNoticesController( $engine, $dataHelper );
			$ajaxMetaKeysCreatorApiExposeController              = new AjaxMetaKeysCreatorApiExposeController();
			$controllerRegistrar                                 = new ControllerRegistrar();
			$declareHposCompatibilityController                  = new DeclareHposCompatibilityController( $dataHelper );
			$getCatalogArgumentsForOrderingController            = new GetCatalogArgumentsForOrderingController( $dataHelper );
			$initializeProductMetaKeysOnDuplicationController    =
				new InitializeProductMetaKeysOnDuplicationController( $dataHelper );
			$injectAdminJavascriptController                     = new InjectAdminJavascriptController( $dataHelper );
			$menuPageRegistrationController                      = new MenuPageRegistrationController( $engine, $dataHelper );
			$messageProxyController                              = new MessageProxyController( $httpClient );
			$orderDeletedController                              = new OrderDeletedController( $orderRecorder, $dataHelper );
			$orderUpdatedController                              = new OrderUpdatedController( $orderRecorder, $dataHelper );
			$pageDetectorAndDataInjectionController              = new PageDetectorAndDataInjectionController( $dataHelper );
			$runProductsMetaKeysCreationActionController         = new RunProductsMetaKeysCreationActionController( $productsHelper );
			$runRegisterSubscriberActionController               = new RunRegisterSubscriberActionController( $dataHelper, $httpClient );
			$settingsRegistrationController                      = new SettingsRegistrationController( $engine, $dataHelper );
			$setTrendingOptionAsDefaultController                = new SetTrendingOptionAsDefaultController( $dataHelper );
			$thankYouController                                  = new ThankYouController( $orderRecorder, $dataHelper );
			$updateCronJobIntervalsController                    = new UpdateCronJobIntervalsController();

			$this->container[ FileHelper::class ]      = $fileHelper;
			$this->container[ Engine::class ]          = $engine;
			$this->container[ SalesCalculator::class ] = $salesCalculator;
			$this->container[ SalesEncoder::class ]    = $salesEncoder;
			$this->container[ DataHelper::class ]      = $dataHelper;
			$this->container[ HttpClient::class ]      = $httpClient;
			$this->container[ OrderRecorder::class ]   = $orderRecorder;
			$this->container[ ProductsHelper::class ]  = $productsHelper;
			$this->container[ AddTrendingOptionInCategorySortingOptionsController::class ] =
				$addTrendingOptionInCategorySortingOptionsController;
			$this->container[ AdminNoticesController::class ]                              = $adminNoticesController;
			$this->container[ AjaxMetaKeysCreatorApiExposeController::class ]              = $ajaxMetaKeysCreatorApiExposeController;
			$this->container[ ControllerRegistrar::class ]                                 = $controllerRegistrar;
			$this->container[ DeclareHposCompatibilityController::class ]                  = $declareHposCompatibilityController;
			$this->container[ GetCatalogArgumentsForOrderingController::class ]            = $getCatalogArgumentsForOrderingController;
			$this->container[ InitializeProductMetaKeysOnDuplicationController::class ]    =
				$initializeProductMetaKeysOnDuplicationController;
			$this->container[ InjectAdminJavascriptController::class ]                     = $injectAdminJavascriptController;
			$this->container[ MenuPageRegistrationController::class ]                      = $menuPageRegistrationController;
			$this->container[ MessageProxyController::class ]                              = $messageProxyController;
			$this->container[ OrderDeletedController::class ]                              = $orderDeletedController;
			$this->container[ OrderUpdatedController::class ]                              = $orderUpdatedController;
			$this->container[ PageDetectorAndDataInjectionController::class ]              = $pageDetectorAndDataInjectionController;
			$this->container[ RunProductsMetaKeysCreationActionController::class ]         = $runProductsMetaKeysCreationActionController;
			$this->container[ RunRegisterSubscriberActionController::class ]               = $runRegisterSubscriberActionController;
			$this->container[ SettingsRegistrationController::class ]                      = $settingsRegistrationController;
			$this->container[ SetTrendingOptionAsDefaultController::class ]                = $setTrendingOptionAsDefaultController;
			$this->container[ ThankYouController::class ]                                  = $thankYouController;
			$this->container[ UpdateCronJobIntervalsController::class ]                    = $updateCronJobIntervalsController;
		}

		if ( substr( $key, 0, strlen( __NAMESPACE__ ) ) !== __NAMESPACE__ ) {
			add_action(
				'admin_notices',
				function () use ( $key ): void {
					$engine = new Engine( __DIR__ . '/templates' );
					echo $engine->render( 'error-notice', array( 'e' => new Exception( "Invalid namespace in '$key'" ) ), true );
				},
				-1,
			);
		}

		return $this->container[ $key ];
	}
}

if ( function_exists( 'add_action' ) ) {
	add_action(
		'plugins_loaded',
		function () {
			if ( class_exists( 'WC_Product' ) ) {
				try {
					Sort::construct()->init();
				} catch ( Error | Exception $e ) {
					add_action(
						'admin_notices',
						function () use ( $e ): void {
							require_once __DIR__ . '/src/Globals/Engine.php';

							$engine = new Engine( __DIR__ . '/templates' );
							echo $engine->render( 'error-notice', array( 'e' => $e ), true );
						},
						-1,
					);
				}
			}
		}
	);
}
