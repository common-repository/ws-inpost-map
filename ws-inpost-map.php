<?php

/*
 * Plugin Name:       WS Inpost Map
 * Text Domain:       ws-inpost-map
 * Description:       Plugin designed to enhance the shipping options in WooCommerce. It provides an additional shipping method and enables customers to conveniently select a parcel locker (Paczkomat) using an integrated map feature.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Author:            Web Systems
 * Author URI:        https://www.k4.pl/
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Tested up to:      6.4
 */

if (!defined('WPINC')) {
    die;
}

if (!defined('WSIM_INPOST_MAP_PLUGIN_DIR_PATH')) {
    define('WSIM_INPOST_MAP_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
}

if (!defined('WSIM_INPOST_MAP_PLUGIN_DIR_URL')) {
    define('WSIM_INPOST_MAP_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
}

require __DIR__ . '/vendor/autoload.php';

load_plugin_textdomain('ws-inpost-map', false, dirname(plugin_basename(__FILE__)) . '/languages');

class WSIM_InpostMapPlugin
{
    public function __construct()
    {
        $this->runWsInpostMapManager();
    }

    public static function activate()
    {
        $wsInpostSettings = new WsInpostMapOnCheckout\App\WSInpostSettings();
        $wsInpostSettings->saveDefaultData();
    }

    public static function deactivate()
    {
    }

    public function runWsInpostMapManager()
    {
        $pluginManager = new WsInpostMapOnCheckout\App\WsInpostMap();
        $pluginManager->run();
    }
}

register_activation_hook(__FILE__, [WSIM_InpostMapPlugin::class, 'activate']);
register_deactivation_hook(__FILE__, [WSIM_InpostMapPlugin::class, 'deactivate']);

add_action('plugins_loaded', function () {
    new WSIM_InpostMapPlugin();
});
