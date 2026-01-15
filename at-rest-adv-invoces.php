<?php
/**
 * Plugin Name: At Rest Adv Invoices
 * Description: A plugin to create invoices for advertisements
 * Version: 1.0.1
 * Author: Na-Gora
 */

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>At Rest Adv Invoices requires WooCommerce to be activated.</p></div>';
    });
    return;
}

define('AT_REST_ADV_INVOICES_DIR', plugin_dir_path(__FILE__));
define('AT_REST_ADV_INVOICES_URL', plugin_dir_url(__FILE__));
define('AT_REST_ADV_INVOICES_ACTIVE', true);

require_once AT_REST_ADV_INVOICES_DIR . 'src/AdvertisementOrderManager.php';
require_once AT_REST_ADV_INVOICES_DIR . 'src/SettingsPage.php';
require_once AT_REST_ADV_INVOICES_DIR . 'src/AdvertisementMetaBox.php';
require_once AT_REST_ADV_INVOICES_DIR . 'src/AdvertisenemtCustomColumn.php';

$orderManager = new \Supernova\AtRestAdvInvoices\AdvertisementOrderManager();
$settings = new \Supernova\AtRestAdvInvoices\SettingsPage();
new \Supernova\AtRestAdvInvoices\AdvertisementMetaBox($settings, $orderManager);
new \Supernova\AtRestAdvInvoices\AdvertisenemtCustomColumn($orderManager);