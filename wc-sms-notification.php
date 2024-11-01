<?php
/**
 * Plugin Name:     SMS Order Notification for WooCommerce
 * Plugin URI:      http://arDigital.ge
 * Description:     WC Send SMS Notifications Order Status And Etc using smsoffice.ge.
 * Author:          ArDigital
 * Author URI:      http://arDigital.ge
 * Text Domain:     sms-order-notification-for-woocommerce
 * Domain Path:     /languages/
 * Version:         0.1.3
 *
 * @package SMS_Order_Notification_for_WooCommerce
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

new sms_notification_plugin_init(__FILE__);
