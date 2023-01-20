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
 * Version:           0.0.2
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

        public static string $current_iqama_times_shortcode = 'masjidiapp_current_iqama_times';

        public static function init() {
            add_action( 'admin_init', 'MasjidiApp_Iqama_Plugin::settings_init' );
            add_action( 'admin_menu', 'MasjidiApp_Iqama_Plugin::options_page' );

            if ( shortcode_exists( MasjidiApp_Iqama_Plugin::$current_iqama_times_shortcode ) ) {
                remove_shortcode( MasjidiApp_Iqama_Plugin::$current_iqama_times_shortcode );
            }

            add_shortcode(MasjidiApp_Iqama_Plugin::$current_iqama_times_shortcode, 'MasjidiApp_Iqama_Plugin::current_prayer_times_shortcode');
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

        /**
         * Display the Iqama times for a masjid by default.
         * Athan time can be displayed with 'showAthan' attribute.
         * If no 'masjidId' field is provided, it tries to use the default set by an admin. If that also does not exist, a failure will occur.
         * Currently 'apiKey' field is optional, it will not be used until a future release (once testing with).
         *  Set it as soon as possible to avoid unexpected behaviour in future releases.
         * 
         * Attributes are not case-sensitive.
         * 
         * @param array  $atts    Shortcode attributes. Default empty.
         * @param string $content Shortcode content. Default null.
         * @param string $tag     Shortcode tag (name). Default empty.
         * @return string Shortcode output.
         */
        public static function current_prayer_times_shortcode( $atts = [], $content = null, $tag = '' ) {
            // normalize attribute keys, lowercase
	        $atts = array_change_key_case( (array) $atts, CASE_LOWER );

            // override default attributes with user attributes
            $parsed_atts = shortcode_atts(
                array(
                    'showathan' => true,
                    'masjidid' => null,
                    'apikey' => null,
                ), $atts, $tag
            );

            $options = get_option( MasjidiApp_Iqama_Plugin::$settings_id );

            $api_key = $parsed_atts['apikey']
                ?? $options[MasjidiApp_Iqama_Plugin::$api_key_id];
            
            $masjid_id = $parsed_atts['masjidid']
                ?? $options[MasjidiApp_Iqama_Plugin::$masjid_id_id]
                ?? throw new Exception('No masjid ID was specified in the shortcode and no default was set');

            $current_date = (new DateTime) -> format("Y-m-d"); // This is the time of the server, need to make it client
            $request_uri = "https://ummahsoft.org/api/masjidi/v1/masjids/$masjid_id/salahandiqamatimes/day/$current_date";
            $fp = fopen($request_uri, 'r');

            $masjidiapi_response = stream_get_contents($fp); // returns false if failed or string if valid. Can return null if no body.
            if ( !is_string( $masjidiapi_response ) || $masjidiapi_response == ''  ) {
                return '<p>Invalid masjid ID or failed to receive details from MasjidiApi</p>';
            }

            $masjidiapi_response_data = json_decode($masjidiapi_response, true);

            if ( is_null($masjidiapi_response_data) || !array_key_exists( 0, $masjidiapi_response_data ) ) {
                return "<p>Unexpected response data format received from MasjidiApi: '$masjidiapi_response'</p>";
            }

            $masjidiapi_response_data = $masjidiapi_response_data[0];

            fclose($fp);

            $fajr_start = $masjidiapi_response_data['fajr_start_time'];
            $fajr_iqama = $masjidiapi_response_data['fajr_iqama'];
            $dhuhr_start = $masjidiapi_response_data['zuhr_start_time'];
            $dhuhr_iqama = $masjidiapi_response_data['zuhr_iqama'];
            $asr_start = $masjidiapi_response_data['asr_start_time'];
            $asr_iqama = $masjidiapi_response_data['asr_iqama'];
            $maghrib_start = $masjidiapi_response_data['magrib_start_time'];
            $maghrib_iqama = $masjidiapi_response_data['maghrib_iqama'];
            $isha_start = $masjidiapi_response_data['isha_start_time'];
            $isha_iqama = $masjidiapi_response_data['isha_iqama'];

            $jumah_data = $masjidiapi_response_data['jumma'];
            $jumah_quantity = sizeof($jumah_data);

            if ($jumah_quantity > 0)
            {
                $jumah1_start = $jumah_data[0]['azan_time'];
                $jumah1_iqama = $jumah_data[0]['iqama_time'];
            }

            if ($jumah_quantity > 1)
            {
                $jumah2_start = $jumah_data[1]['azan_time'];
                $jumah2_iqama = $jumah_data[1]['iqama_time'];
            }

            ob_start();
            ?>

            <div style='margin-right: auto; margin-left: auto; display:flex; flex-wrap: wrap;'>
                <div style='min-width: 160px;'>
                    <span style='display:block; font-weight:bold;'>Fajr</span>
                    <span id='custom-fajr-start-span' style='display:block;'>Start: <?php echo $fajr_start?></span>
                    <span id='custom-fajr-iqama-span' style='display:block;'>Iqama: <?php echo $fajr_iqama?></span>
                </div>

                <div style='min-width: 160px;'>
                    <span style='display:block; font-weight:bold;'>Dhuhr</span>
                    <span id='custom-dhuhr-start-span' style='display:block;'>Start: <?php echo $dhuhr_start?></span>
                    <span id='custom-dhuhr-iqama-span' style='display:block;'>Iqama: <?php echo $dhuhr_iqama?></span>
                </div>

                <div style='min-width: 160px;'>
                    <span style='display:block; font-weight:bold;'>Asr</span>
                    <span id='custom-asr-start-span' style='display:block;'>Start: <?php echo $asr_start?></span>
                    <span id='custom-asr-iqama-span' style='display:block;'>Iqama: <?php echo $asr_iqama?></span>
                </div>

                <div style='min-width: 160px;'>
                    <span style='display:block; font-weight:bold;'>Maghrib</span>
                    <span id='custom-maghrib-start-span' style='display:block;'>Start: <?php echo $maghrib_start?></span>
                    <span id='custom-maghrib-iqama-span' style='display:block;'>Iqama: <?php echo $maghrib_iqama?></span>
                </div>

                <div style='min-width: 160px;'>
                    <span style='display:block; font-weight:bold;'>Isha</span>
                    <span id='custom-isha-start-span' style='display:block;'>Start: <?php echo $isha_start?></span>
                    <span id='custom-isha-iqama-span' style='display:block;'>Iqama: <?php echo $isha_iqama?></span>
                </div>

                <?php if (isset($jumah1_start) || isset($jumah1_iqama)) {?>
                <div style='min-width: 160px;'>
                    <span style='display:block; font-weight:bold;'>Jum'ah 1</span>
                    <span id='custom-jumah1-start-span' style='display:block;'>Khutbah: <?php if (isset($jumah1_start)) echo $jumah1_start?></span>
                    <span id='custom-jumah1-iqama-span' style='display:block;'>Iqama: <?php if (isset($jumah1_iqama)) echo $jumah1_iqama?></span>
                </div>

                <?php
                }

                if (isset($jumah2_start) || isset($jumah2_iqama)) {
                ?>

                <div style='min-width: 160px;'>
                    <span style='display:block; font-weight:bold;'>Jum'ah 2</span>
                    <span id='custom-jumah2-start-span' style='display:block;'>Khutbah: <?php if (isset($jumah2_start)) echo $jumah2_start ?></span>
                    <span id='custom-jumah2-iqama-span' style='display:block;'>Iqama: <?php if (isset($jumah2_iqama)) echo $jumah2_iqama?></span>
                </div>
                <?php } ?>
            </div>

            <?php
            return ob_get_clean();
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