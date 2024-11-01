<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait wcSmsOrderNotificationSettings
{

	public static function instance()
	{
		add_action('admin_menu', [__CLASS__, 'wc_sms_admin_settings_api']);
		add_action('admin_init', [__CLASS__, 'sms_notification_settings_init']);
		add_action('plugins_loaded', function () {
			load_plugin_textdomain('wc-sms-notification', FALSE, basename(dirname(__FILE__)) . '/languages/');
		});
       add_action( 'admin_enqueue_scripts', [__CLASS__, 'sms_order_notification_wc_scripts'] );
	}

    public static function sms_order_notification_wc_scripts( $hook ) {
        wp_enqueue_script( 'sms_notification_scripts', plugin_dir_url( __FILE__ ) . '../assets/js/sms.js', array(), '1.0' );
        wp_enqueue_style( 'sms_notification_styles', plugin_dir_url( __FILE__ ) . '../assets/css/sms.css', array(), '1.0' );
    }

	public static function wc_sms_admin_settings_api()
	{
		add_submenu_page(
			'woocommerce',
			'WC SMS NOTIFICATIONS',
			'WC SMS NOTIFICATIONS',
			'manage_options',
			'sms-notifications',
			[__CLASS__, 'sms_notification_settings_form']);
	}

	public static function sms_notification_settings_init()
	{
		register_setting('wc_order_notification_options', 'wc_order_sms_notification');
		add_settings_section(
			'sms_notification_pluginPage_section',
			null,
			null,
			'wc_order_notification_options'
		);
		add_settings_field(
			'sms_office_key',
			__('SMS Office api Key', 'sms-order-notification-for-woocommerce'),
			[__CLASS__, 'sms_notification_st_render'],
			'wc_order_notification_options',
			'sms_notification_pluginPage_section',
			['label_for' => 'sms_office_key']
		);
		add_settings_field(
			'sms_office_sender',
			__('Sender', 'wc-sms-notification'),
			[__CLASS__, 'sms_notification_st_sender_render'],
			'wc_order_notification_options',
			'sms_notification_pluginPage_section',
			['label_for' => 'smsoffice_sender']
		);
		add_settings_field(
			'message_content',
			__('Message Content', 'wc-sms-notification'),
			[__CLASS__, 'message_content'],
			'wc_order_notification_options',
			'sms_notification_pluginPage_section',
			['label_for' => 'message_content']
		);
	}

	public static function message_content()
	{
		$options = get_option('wc_order_sms_notification');
		?>
		<p><strong>Message Content Variables: </strong></p>
		<p>
         <code>%user_name% , %product% , %price% , %order_number% , %shipping_price% , %order_status%</code></p>
		<?php
		wp_editor( trim(isset($options['message_content']) ? $options['message_content'] : ''), 'message_content', [
			'textarea_name' => 'wc_order_sms_notification[message_content]',
			'editor_height' => 250,
			'media_buttons' => false,
			'tinymce' => false,
			'teeny' => false,
			'quicktags' => false,
			'dfw' => false,
		]);
	}

	public static function sms_notification_st_sender_render()
	{
		$options = get_option('wc_order_sms_notification');
		?>
		<input type='text' name='wc_order_sms_notification[sms_office_sender]'
			   value='<?php  echo isset($options['sms_office_sender']) ? $options['sms_office_sender'] : ''; ?>'
			   id="smsoffice_sender">
		<?php
	}



	public static function sms_notification_st_render()
	{
		$options = get_option('wc_order_sms_notification');
		?>
		<input type='text' name='wc_order_sms_notification[sms_office_key]'
			   value='<?php echo isset($options['sms_office_key']) ? $options['sms_office_key'] : '' ?>' id="smsofficekey">
		<p>Register <a href="https://smsoffice.ge/you/profile/integration/" target="_blank">SMSOFFICE</a> And Fill in Key
			Field </p>
		<?php
	}

	public static function sms_notification_settings_form()
	{
		?>
		<form action='options.php' method='post'>
			<h2><?php echo __('WC Order SMS Notification Settings', 'sms-order-notification-for-woocommerce') ?></h2>
			<?php
			settings_fields('wc_order_notification_options');
			do_settings_sections('wc_order_notification_options');
			$key = isset(get_option('wc_order_sms_notification')['sms_office_key']) ? get_option('wc_order_sms_notification')['sms_office_key'] : '';
			$get_balance = wp_remote_get(sprintf("https://smsoffice.ge/api/getBalance/?key=%s", $key));
			?>
			<div id="result" style="display: none;">
				<div class="text"><?php echo __('Number of messages', 'sms-order-notification-for-woocommerce'); ?>: <?php echo isset($get_balance['body']) ? $get_balance['body'] : '' ?></div>
				<div class="close_bl"><?php echo __('Close', 'sms-order-notification-for-woocommerce'); ?></div>
			</div>
			<div class="button button-primary" id="get_balance">Check Number of messages</div>
			<?php
			submit_button(__('Save Changes', 'sms-order-notification-for-woocommerce'));
			?>
		</form>
		<?php
		?>
		<?php
	}

}
