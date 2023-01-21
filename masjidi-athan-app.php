<?php
/**
 * Plugin Name
 *         
 * @package           PluginPackage
 * @author            Omar Abdel Bari
 * @copyright         2023 Omar Abdel Bari
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       MasjidiApp Athan/Iqama Integration
 * Plugin URI:        https://github.com/ProgrammingPony/masjidiapp-iqama-wordpress
 * Description:       Athan and Iqama widgets which integrate with the Masjidi App.
 * Version:           0.1.1
 */

if ( ! class_exists( 'MasjidiApp_Iqama_Plugin' ) ) {
    require_once( dirname(__FILE__) . '/src/MasjidiApp_Iqama_Plugin.php' );
    
    MasjidiApp_Iqama_Plugin::init();
}