=== Sales Order Ranking Tool ===
Tags: woocommerce, sales ranking, ecommerce
Requires at least: 6.2.0
Tested up to: 6.7.1
Requires PHP: 7.4
WC requires at least: 7.4.0
WC tested up to: 9.6.1
Stable tag: 5.3.2
License: GPLv3
License URI: [http://www.gnu.org/licenses/gpl-3.0.html](http://www.gnu.org/licenses/gpl-3.0.html)

Sort (Sales Order Ranking Tool) optimizes WooCommerce product sorting by using sales data to enable dynamic ordering based on custom criteria.

== Description ==

The Sort plugin enhances WooCommerce stores by tracking and utilizing product sales data for custom sorting functionality. Here are the key features:

- Automatically tracks sales data for each product whenever an order is created, updated, or deleted.
- Retains sales data for the past year, ensuring optimal performance and relevance.
- Generates summarized sales data for specific time intervals such as weekly, monthly, and yearly.
- Supports customizable sorting options, allowing store owners to determine product visibility based on sales trends.
- Provides flexibility in naming and configuring sorting options to align with business needs.
- Adjusts sales data dynamically when orders are deleted, ensuring accuracy.

== Installation ==

- Upload the `wc-sort` folder to the `/wp-content/plugins/` directory.
- Activate the plugin through the 'Plugins' menu in WordPress.
- Configure sorting options in the admin menu 'SORT'.

== Frequently Asked Questions ==

= How does the plugin track sales? =
The plugin tracks sales data for each product whenever an order is processed, helping store owners understand sales performance over time.

= Can I customize the sorting options? =
Yes, you can rename and configure the sorting options to suit your store's requirements and preferences.

= Is old sales data automatically removed? =
Yes, the plugin retains relevant sales data for the past year and removes outdated records to ensure efficiency.

== External Services ==

This plugin utilizes two external services to provide additional functionality. Details of these services are outlined below:

1. Freemium Feature Updates

= When freemium features are activated, the plugin communicates with an external URL defined in `Constants::EXTERNAL_URL_SORT_INSTALLATION`. This allows the plugin to update information about the site where it is installed, including the admin's information and the site's URL.

= The collection and usage of this data are clearly disclosed in the plugin panel under the freemium features section.

= If the freemium features are deactivated, the plugin automatically removes any previously submitted data.

= To ensure optimal performance, this check is performed only once per day.

2. Admin Notice Messages

= The plugin connects to an external URL defined in `Constants::EXTERNAL_URL_SORT_MESSAGE` to retrieve messages displayed in admin notices when a dashboard page is accessed.

= These messages are specific to the plugin's development company, "Merge, Inc," and provide relevant updates or information.

= Retrieved messages are cached both in the browser and the website to minimize live calls and enhance performance.

These integrations have been designed with care to ensure transparency and efficiency while providing valuable features for the plugin users.

== Build Files Information ==

= The CSS and JavaScript files located in the `frontend/admin/dist` directory are build files. These files are generated from the source files found in the `source-files/js` directory.

== Changelog ==

= 5.2.1 - 2025-02-11 =
* Added: Initial Release