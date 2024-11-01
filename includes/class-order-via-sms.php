<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

interface sendSms
{
	public function sendSms(int $order_id);
}

class sendOrderViaSms implements sendSms
{
	const sender_url = 'http://smsoffice.ge/api/v2/send/';

	private $order_status = '';

	/**
	 * @param int $order_id
	 * @return void
	 */
	public function sendSms(int $order_id)
	{
		$order = wc_get_order($order_id);
		$phone_number = $order->get_billing_phone();
		$items = $order->get_items();
		$product_name = '';
		foreach ($items as $item) {
			$product_name = $item['name'];
		}
		$order_status = $order->get_status();
		switch ($order_status) {
			case "completed":
				$this->order_status = __('Completed', 'sms-order-notification-for-woocommerce');
				break;
			case "processing":
				$this->order_status = __('Processing', 'sms-order-notification-for-woocommerce');
				break;
			case "on-hold":
				$this->order_status = __('On hold', 'sms-order-notification-for-woocommerce');
				break;
			case "cancelled":
				$this->order_status = __('Cancelled', 'sms-order-notification-for-woocommerce');
				break;
			case "refunded":
				$this->order_status = __('Refunded', 'sms-order-notification-for-woocommerce');
				break;
			case "failed":
				$this->order_status = __('Failed', 'sms-order-notification-for-woocommerce');
				break;
			case "pending":
				$this->order_status = __('Pending', 'sms-order-notification-for-woocommerce');
				break;
			case "pending-payment":
				$this->order_status = __('Pending payment', 'sms-order-notification-for-woocommerce');
				break;
		}

		$options = get_option('wc_order_sms_notification');
		$search = ['%user_name%', '%product%', '%price%', '%order_number%', '%shipping_price%', '%order_status%'];
		$replace = [$order->get_user()->nickname,$product_name,$order->get_total(). $order->get_currency(), $order->get_id(), $order->get_shipping_total(), $this->order_status];
		$content = str_replace($search, $replace, $options['message_content']);
		$resp = wp_remote_post(self::sender_url, [
			'body' => [
				'key' => trim($options['sms_office_key']),
				'destination' => trim($phone_number),
				'sender' => trim($options['sms_office_sender']),
				'content' => $content
			]
		]);
		$body = json_decode($resp['body']);
		if (!$body->Success) {
			$this->error_logs($resp['body']);
		}
	}

	/**
	 * @param string $log
	 */
	private function error_logs(string $log)
	{
		$date = date('d.m.Y h:i:s');
		error_log("$date - $log", 3, plugin_dir_path(__DIR__) . 'logs/errors.log', "\r\n");
	}

	protected function balance()
	{
		echo 'sasa';
	}

}
