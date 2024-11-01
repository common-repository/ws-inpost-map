<?php

namespace WsInpostMapOnCheckout\App;

if (!defined('ABSPATH')) exit;

use WsInpostMapOnCheckout\App\WsInpostActions;
use WsInpostMapOnCheckout\App\WSInpostSettings;

class WsInpostMap
{
    public function run()
    {
        if (class_exists("woocommerce")) {
            new WsInpostActions();
        }
        new Assets();
        new WSInpostSettings();
    }
}
