<?php

namespace WsInpostMapOnCheckout\App;

if (!defined('ABSPATH')) exit;

class WsInpostActions
{
  public function __construct()
  {
    $this->addActions();
  }

  public function addActions()
  {
    $options = get_option('ws_inpost_plugin_options');
    if (class_exists("woocommerce")) {
      if (isset($options["active-button"]) && $options["active-button"] === "1") {
        add_action("woocommerce_checkout_before_customer_details", [$this, "displayMap"], 10);
        add_action("woocommerce_after_checkout_billing_form", [$this, "registerCustomField"], 10);
        add_action("woocommerce_after_checkout_billing_form", [$this, "displayInpostButton"], 20);
        add_action('woocommerce_checkout_create_order', [$this, 'saveCustomFieldOrderMeta'], 22, 2);
        add_action('woocommerce_admin_order_data_after_shipping_address', [$this, "displayPaczkomatFieldInOrder"], 10, 1);
        add_action('woocommerce_shipping_init', [$this, 'customShippingMethodInit'], 10);
        add_filter('woocommerce_shipping_methods', [$this, 'addCustomShippingMethod'], 20);
      }
    }
  }

  public function displayInpostButton()
  {
    include(WSIM_INPOST_MAP_PLUGIN_DIR_PATH . "templates/inpostButton.php");
  }

  public function displayMap()
  {
    include(WSIM_INPOST_MAP_PLUGIN_DIR_PATH . "templates/geoMap.php");
  }

  public function registerCustomField($checkout)
  {
    \woocommerce_form_field('billing__paczkomat_id', array(
      'type'          => 'text',
      'class'         => array('form-row form-row-wide'),
      'label'         => __('Paczkomat number', 'ws-inpost-map'),
      'placeholder'   => __('Paczkomat number', 'ws-inpost-map'),
    ), $checkout->get_value('billing__paczkomat_id'));
  }

  public function saveCustomFieldOrderMeta($order, $data)
  {
    if (isset($_POST['woocommerce-process-checkout-nonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['woocommerce-process-checkout-nonce'])), 'woocommerce-process_checkout')) {
      if (isset($_POST['billing__paczkomat_id']) && !empty($_POST['billing__paczkomat_id'])) {
        $paczkomatId = sanitize_text_field($_POST['billing__paczkomat_id']);
        $order->update_meta_data('paczkomat_id', $paczkomatId);
      }
    }
  }

  public function displayPaczkomatFieldInOrder($order)
  {

    $paczkomatId = $order->get_meta('paczkomat_id');
    if (!empty($paczkomatId)) {
      $html = '<p><strong>' . __('Locker ID', 'ws-inpost-map') . ': </strong> ' . esc_html($paczkomatId) . '</p>';

      echo wp_kses_post($html);
    }
  }

  public function customShippingMethodInit()
  {
    include(WSIM_INPOST_MAP_PLUGIN_DIR_PATH . "App/WsInpostShippingMethod.php");
  }

  public function addCustomShippingMethod($methods)
  {
    $methods['wsim_inpost_shipping_method'] = 'WSIM_InpostShippingMethod';
    return $methods;
  }
}
