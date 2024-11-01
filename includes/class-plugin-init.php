<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class sms_notification_plugin_init extends sendOrderViaSms
{

	use wcSmsOrderNotificationSettings;

	public function __construct($file)
	{
		add_action('woocommerce_order_status_pending', [$this, 'orderStatus']); //
		add_action('woocommerce_order_status_processing', [$this, 'orderStatus']); //
		add_action('woocommerce_order_status_completed', [$this, 'orderStatus']);
		add_action('woocommerce_order_status_refunded', [$this, 'orderStatus']);
		add_action('woocommerce_order_status_cancelled', [$this, 'orderStatus']);
		add_action('woocommerce_order_status_pending', [$this, 'orderStatus']);
		add_action('woocommerce_order_status_failed', [$this, 'orderStatus']);
		add_action('woocommerce_order_status_on-hold', [$this, 'orderStatus']);
		add_action('deactivate_wc-sms-notification',[$this, 'deactivate']);
		register_deactivation_hook($file, [$this, 'deactivate']);
		wcSmsOrderNotificationSettings::instance();
	}

	/**
	 * @param $order_id
	 */
	public function orderStatus($order_id)
	{
		  parent::sendSms($order_id);
	}

	public  function deactivate()
	{
		delete_option('wc_order_sms_notification');
	}

}
