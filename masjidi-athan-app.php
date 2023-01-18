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
 * Version:           0.0.1
 */

if ( ! class_exists( 'MasjidiApp_Iqama_Plugin' ) ) {

    class MasjidiApp_Iqama_Plugin {
        
        public static string $plugin_name = 'masjidiapp_iqama';
        public static string $option_group = 'masjidiapp_iqama_settings';
        public static string $menu_slug = 'masjidiapp_iqama-menu';
        public static string $settings_page_slug = 'masjidiapp_iqama-settings_page';
        public static string $settings_section_id = 'masjidiapp_iqama-default_settings';
        public static string $settings_id = 'masjidiapp_iqama-settings';

        public static string $api_key_id = 'masjidiapp_iqama_api-key';
        public static string $masjid_id_id = 'masjidiapp_iqama_masjid-id';

        public static function init() {
            add_action( 'admin_init', 'MasjidiApp_Iqama_Plugin::settings_init' );
            add_action( 'admin_menu', 'MasjidiApp_Iqama_Plugin::options_page' );
        }

        public static function settings_init() {
            register_setting( MasjidiApp_Iqama_Plugin::$plugin_name, MasjidiApp_Iqama_Plugin::$settings_id );

            add_settings_section(
                MasjidiApp_Iqama_Plugin::$settings_section_id,
                'Default Settings',
                'MasjidiApp_Iqama_Plugin::render_settings_section_header',
                MasjidiApp_Iqama_Plugin::$menu_slug);
            
            add_settings_field(
                MasjidiApp_Iqama_Plugin::$api_key_id,
                'Api Key',
                function () {
                    return MasjidiApp_Iqama_Plugin::render_settings_field( MasjidiApp_Iqama_Plugin::$api_key_id );
                },
                MasjidiApp_Iqama_Plugin::$menu_slug,
                MasjidiApp_Iqama_Plugin::$settings_section_id);
            
            add_settings_field(
                MasjidiApp_Iqama_Plugin::$masjid_id_id,
                'Masjid ID',
                function () {
                    return MasjidiApp_Iqama_Plugin::render_settings_field( MasjidiApp_Iqama_Plugin::$masjid_id_id, true );
                },
                MasjidiApp_Iqama_Plugin::$menu_slug,
                MasjidiApp_Iqama_Plugin::$settings_section_id);

        }

        public static function render_settings_field( $name, $required = false, $default_value = '' ) {
            $options = get_option( MasjidiApp_Iqama_Plugin::$settings_id );
            ?>
                <input type="text" 
                    name="<?php echo esc_attr( MasjidiApp_Iqama_Plugin::$settings_id . '[' . $name . ']' ); ?>" 
                    value="<?php echo isset( $options[ $name ] ) ? $options[ $name ] : esc_attr( $default_value ); ?>"
                    <?php if ($required) echo 'required' ?> />
            <?php
        }

        public static function render_settings_section_header( $args ) {
?>
            <span><?php echo esc_attr( $args['id'] ); ?></span>
<?php
        }

        public static function uninstall() {

            unregister_setting( MasjidiApp_Iqama_Plugin::$option_group, 'api_key' );
            unregister_setting( MasjidiApp_Iqama_Plugin::$option_group, 'masjid_id' );

            remove_action('admin_menu', 'MasjidiApp_Iqama_Plugin::options_page');
            remove_menu_page(MasjidiApp_Iqama_Plugin::$menu_slug);
        }

        public static function options_page() {
            add_options_page(
                'MasjidiApp Iqama',
			    'MasjidiApp Iqama',
                'manage_options',
                MasjidiApp_Iqama_Plugin::$menu_slug,
                'MasjidiApp_Iqama_Plugin::options_page_html'
            );
        }

        public static function options_page_html() {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            ?>
            <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

                <p>The API Key may become mandatory in a future version. To avoid interuption on plugin update, please <a href="https://stream.masjidiapp.com/masjidiapp/2021/02/16/masjidi-api/" target="_blank">acquire one</a> as soon as possible.</p>

                <form action="options.php" method="post">
                <?php
                    // output security fields for the registered setting "wporg_options"
                    settings_fields( MasjidiApp_Iqama_Plugin::$plugin_name );
                    // output setting sections and their fields
                    // (sections are registered for "wporg", each field is registered to a specific section)
                    do_settings_sections( MasjidiApp_Iqama_Plugin::$menu_slug );
                    // output save settings button
                    submit_button('Save Settings');
                ?>
              </form>
            </div>
            <?php
        }
    }

    MasjidiApp_Iqama_Plugin::init();
}