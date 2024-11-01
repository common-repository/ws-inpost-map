<?php

namespace WsInpostMapOnCheckout\App;

if (!defined('ABSPATH')) exit;

class Assets
{
  public function __construct()
  {
    \add_action('wp_enqueue_scripts', [$this, "addStylesAndScripts"], 10);
  }
  public function addStylesAndScripts()
  {
    /* Styles */
    \wp_enqueue_style('ws-inpost-styles', WSIM_INPOST_MAP_PLUGIN_DIR_URL . 'assets/css/frontend/style.css');
    \wp_enqueue_style('geowidget-style',  WSIM_INPOST_MAP_PLUGIN_DIR_URL . 'assets/css/frontend/easypack.css');

    /* Scripts */
    \wp_enqueue_script('ws-geomap-scripts', WSIM_INPOST_MAP_PLUGIN_DIR_URL . 'assets/js/frontend/geomap.js', [], false, true);
    \wp_enqueue_script('ws-checkout-scripts', WSIM_INPOST_MAP_PLUGIN_DIR_URL . 'assets/js/frontend/checkout.js', [], false, true);
    \wp_enqueue_script('geowidget-script', WSIM_INPOST_MAP_PLUGIN_DIR_URL . 'assets/js/frontend/geowidget.js', [], false, true);
  }
}
