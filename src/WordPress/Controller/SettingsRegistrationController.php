<?php
declare(strict_types=1);

namespace MergeInc\WcSort\WordPress\Controller;

use Exception;
use MergeInc\WcSort\Globals\Engine;
use MergeInc\WcSort\WordPress\Constants;
use MergeInc\WcSort\WordPress\DataHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsRegistrationController extends AbstractController {

	/**
	 * @var Engine
	 */
	private Engine $engine;

	/**
	 * @var DataHelper
	 */
	private DataHelper $dataHelper;

	/**
	 * @param Engine     $engine
	 * @param DataHelper $dataHelper
	 */
	public function __construct( Engine $engine, DataHelper $dataHelper ) {
		$this->engine     = $engine;
		$this->dataHelper = $dataHelper;
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	public function __invoke(): void {
		register_setting(
			Constants::ADMIN_MENU_OPTION_GROUP,
			Constants::SETTINGS_FIELDS_ACTIVATED,
			array(
				'type'              => 'string',
				'sanitize_callback' => array(
					$this,
					'sanitizeCheckbox',
				),
			)
		);

		register_setting(
			Constants::ADMIN_MENU_OPTION_GROUP,
			Constants::SETTINGS_FIELDS_FREEMIUM_ACTIVATED,
			array(
				'type'              => 'string',
				'sanitize_callback' => array(
					$this,
					'sanitizeCheckbox',
				),
			)
		);

		register_setting(
			Constants::ADMIN_MENU_OPTION_GROUP,
			Constants::SETTINGS_FIELDS_DEFAULT,
			array(
				'type'              => 'string',
				'sanitize_callback' => array(
					$this,
					'sanitizeCheckbox',
				),
			)
		);

		register_setting(
			Constants::ADMIN_MENU_OPTION_GROUP,
			Constants::SETTINGS_FIELD_TRENDING_LABEL,
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			Constants::ADMIN_MENU_OPTION_GROUP,
			Constants::SETTINGS_FIELD_TRENDING_INTERVAL,
			array(
				'type'              => 'string',
				'sanitize_callback' => array(
					$this,
					'sanitizeIntervals',
				),
			)
		);

		register_setting(
			Constants::ADMIN_MENU_OPTION_GROUP,
			Constants::SETTINGS_FIELD_TRENDING_OPTION_NAME_URL,
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		add_settings_section(
			Constants::SETTINGS_SECTION_ACTIVATION,
			esc_html( '🔌 Activation Settings' ),
			function (): void {
				echo wp_kses( '<hr>', $this->dataHelper->getCommonHtmlElements() );
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
		);

		add_settings_field(
			Constants::SETTINGS_FIELDS_ACTIVATED,
			esc_html( 'Enabled' ) .
			wp_kses(
				$this->engine->render(
					'tooltip',
					array(
						'text' => esc_html( 'Enable or disable the plugin. When disabled, the plugin will continue to collect sales order data.' ),
					),
					true
				),
				$this->dataHelper->getCommonHtmlElements()
			),
			function (): void {
				echo wp_kses(
					$this->engine->render(
						'settings-field-activated',
						array(
							'checked'  => $this->dataHelper->isActivated(),
							'disabled' => $this->dataHelper->isActivatedDisabled(),
						),
						true
					),
					$this->dataHelper->getCommonHtmlElements()
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_ACTIVATION,
		);

		add_settings_field(
			Constants::SETTINGS_FIELDS_FREEMIUM_ACTIVATED,
			esc_html( 'Freemium Activation' ),
			function (): void {
				echo wp_kses(
					$this->engine->render(
						'settings-field-freemium-activated',
						array(
							'checked' => $this->dataHelper->isFreemiumActivated(),
						),
						true
					),
					$this->dataHelper->getCommonHtmlElements()
				);

				echo wp_kses( $this->engine->render( 'freemium-notice', array(), true ), $this->dataHelper->getCommonHtmlElements() );
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_ACTIVATION,
		);

		add_settings_section(
			Constants::SETTINGS_SECTION_BASIC,
			esc_html( '⚙️ Basic Settings' ),
			function (): void {
				echo wp_kses( '<hr>', $this->dataHelper->getCommonHtmlElements() );
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
		);

		add_settings_field(
			Constants::SETTINGS_FIELDS_DEFAULT,
			esc_html( 'Set as Default' ) .
			wp_kses(
				$this->engine->render(
					'tooltip',
					array(
						'text' => esc_html( 'Set this sorting option as the default. Customers will see this sorting applied automatically unless they choose a different one.' ),
					),
					true
				),
				$this->dataHelper->getCommonHtmlElements()
			),
			function (): void {
				echo wp_kses(
					$this->engine->render(
						'settings-field-default',
						array(
							'checked' => $this->dataHelper->isDefault(),
						),
						true
					),
					$this->dataHelper->getCommonHtmlElements()
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_BASIC,
		);

		add_settings_section(
			Constants::SETTINGS_SECTION_FREEMIUM,
			esc_html( '🌟  Freemium Settings' ),
			function (): void {
				echo wp_kses( '<hr>', $this->dataHelper->getCommonHtmlElements() );
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
		);

		add_settings_field(
			Constants::SETTINGS_FIELD_TRENDING_LABEL,
			esc_html( 'Sorting Option Label' ) .
			wp_kses(
				$this->engine->render(
					'tooltip',
					array(
						'text' => esc_html( 'Define the label for this sorting option as it will appear in the WooCommerce sorting dropdown.' ),
					),
					true
				),
				$this->dataHelper->getCommonHtmlElements()
			),
			function (): void {
				echo wp_kses(
					$this->engine->render(
						'settings-field-trending-label',
						array(
							'freemiumActivated' => $this->dataHelper->isFreemiumActivated(),
							'value'             => $this->dataHelper->getTrendingLabel(),
						),
						true
					),
					$this->dataHelper->getCommonHtmlElements()
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_FREEMIUM,
		);

		add_settings_field(
			Constants::SETTINGS_FIELD_TRENDING_INTERVAL,
			esc_html( 'Sorting Days Interval' ) .
			wp_kses(
				$this->engine->render(
					'tooltip',
					array( 'text' => esc_html( 'Set the time period (e.g., 7, 15, 30 days) to use sales data for sorting.', 'wc-sort' ) ),
					true
				),
				$this->dataHelper->getCommonHtmlElements()
			),
			function (): void {
				echo $this->engine->render(
					'settings-field-trending-interval',
					array(
						'freemiumActivated' => $this->dataHelper->isFreemiumActivated(),
						'intervals'         => array( 7, 15, 30, 90, 180, 365 ),
						'daysLabel'         => esc_html( 'Days' ),
						'value'             => $this->dataHelper->getTrendingInterval(),
					),
					true
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_FREEMIUM,
		);

		add_settings_field(
			Constants::SETTINGS_FIELD_TRENDING_OPTION_NAME_URL,
			esc_html( 'Sorting Option Key', 'wc-sort' ) .
			wp_kses(
				$this->engine->render(
					'tooltip',
					array(
						'text' => esc_html( 'Define the value for the orderby query string parameter used to identify this sorting method in URLs. This is important for SEO to ensure clean, descriptive, and crawlable URLs.' ),
					),
					true
				),
				$this->dataHelper->getCommonHtmlElements()
			),
			function (): void {
				echo wp_kses(
					$this->engine->render(
						'settings-field-trending-option-name-url',
						array(
							'freemiumActivated' => $this->dataHelper->isFreemiumActivated(),
							'value'             => $this->dataHelper->getTrendingOptionNameUrl(),
						),
						true
					),
					$this->dataHelper->getCommonHtmlElements()
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_FREEMIUM,
		);
	}

	/**
	 * @param string|null $input
	 *
	 * @return false|string|null
	 */
	public function sanitizeCheckbox( ?string $input ) {
		return in_array( $input, array( 'yes', '', null ) ) ? $input : false;
	}

	/**
	 * @param string|null $interval
	 *
	 * @return false|string
	 */
	public function sanitizeIntervals( ?string $interval ) {
		return in_array( $interval, array( 7, 15, 30, 90, 180, 365 ) ) ? $interval : false;
	}
}
