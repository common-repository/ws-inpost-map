<?php

if (!defined('ABSPATH')) exit;
class WSIM_InpostShippingMethod extends WC_Shipping_Method
{
  public $cost;

  public function __construct($instance_id = 0)
  {
    $this->id = 'wsim_inpost_shipping_method';
    $this->instance_id = absint($instance_id);
    $this->method_title = __('Paczkomat Inpost', 'ws-inpost-map');
    $this->method_description = __('The method allows you to choose a parcel locker where the shipment is to be delivered.', 'ws-inpost-map');
    $this->supports = array(
      'shipping-zones',
      'instance-settings',
      'instance-settings-modal',
    );

    $this->enabled = "no";
    $this->title = __('Paczkomat Inpost', 'ws-inpost-map');

    $this->init();
  }

  public function init()
  {
    $this->init_form_fields();
    $this->init_settings();

    $this->title = ($this->get_option('title') !== '') ? $this->get_option('title') : $this->title;
    $this->enabled = ($this->get_option('enabled') === 'yes') ? $this->get_option('enabled') : $this->enabled;
    $this->method_title = ($this->get_option('title') !== '') ? $this->get_option('title') : $this->method_title;
    $this->method_description = ($this->get_option('description') !== '') ? $this->get_option('description') : $this->method_description;
    $this->cost = $this->get_option('cost');

    add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
  }

  public function init_form_fields()
  {
    $form_fields = array(
      'title' => array(
        'title'       => esc_html__('Method Title', 'ws-inpost-map'),
        'type'        => 'text',
        'description' => esc_html__('Enter the method title', 'ws-inpost-map'),
        'default'     => esc_html__('', 'ws-inpost-map'),
        'desc_tip'    => true,
      ),
      'description' => array(
        'title'       => esc_html__('Description', 'ws-inpost-map'),
        'type'        => 'textarea',
        'description' => esc_html__('Enter the Description', 'ws-inpost-map'),
        'default'     => esc_html__('', 'ws-inpost-map'),
        'desc_tip'    => true
      ),
      'cost' => array(
        'title'       => esc_html__('Cost', 'ws-inpost-map'),
        'type'        => 'text',
        'description' => esc_html__('Add the shipping cost', 'ws-inpost-map'),
        'default'     => esc_html__('', 'ws-inpost-map'),
        'desc_tip'    => true
      )
    );

    $this->instance_form_fields = $form_fields;
  }

  public function calculate_shipping($package = array())
  {
    $rate = array(
      'id'       => $this->id,
      'label'    => ($this->get_option('title') !== '') ? $this->get_option('title') : $this->method_title,
      'cost'     => $this->cost,
      'package' => $package,
    );
    $this->add_rate($rate);
  }
}
