<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

if ( class_exists( 'MasjidiApp_Iqama_Plugin' ) ) {
    require_once( dirname(__FILE__) . '/src/MasjidiApp_Iqama_Plugin.php' );
    MasjidiApp_Iqama_Plugin::uninstall();
}