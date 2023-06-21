<?php
/**
 * GVTRYON_PluginSettings.
 *
 * @package GVTRYON_PluginSettings
 */

defined('ABSPATH') || exit;

class GVTRYON_PluginSettings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        //create new top-level menu
        add_menu_page(
            __('G Virtual Tryon', 'gvtryon'),
            __('G Virtual Tryon', 'gvtryon'),
            'manage_options',
            'gvtryon',
            array($this, 'create_admin_page'),
            // 'dashicons-schedule',
            // plugins_url('/images/icon.png', __FILE__),
            50
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // $this->options = get_option('gvtryon_setting');
        ?>
        <div class="gvtryon-wrapper">
            <h1 class="gvtryon-header">
                <?php echo __('G Virtual Tryon', 'gvtryon'); ?>
            </h1>
            <div class="gvtryon-content">
                <form method="post" action="options.php">
                    <?php
                    settings_fields('gvtryon_plugin_setting_group');
                    do_settings_sections('gvtryon_plugin_setting');
                    // submit_button("");
                    ?>
                    <h4>information and guide how to use the plugin</h4>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'gvtryon_plugin_setting_group',
            'gvtryon_plugin_setting',
            array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
            'gvtryon_settings',
            '',
            // __('G Virtual Tryon Settings', 'gvtryon'),
            array($this, 'print_section_info'),
            'gvtryon_plugin_setting'
        );

        // add_settings_field(
        //     'gvtryon_activation_key',    // ID
        //     __('Activation Key', 'gvtryon'), // Title 
        //     array($this, 'gvtryon_key_callback'),    // Callback
        //     'gvtryon_plugin_setting',    // Page
        //     'gvtryon_settings' // Section           
        // );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input)
    {
        $new_input = $input;
        // if (isset($input['gvtryon_activation_key']))
        //     $new_input['gvtryon_activation_key'] = sanitize_text_field($input['gvtryon_activation_key']);

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        // print 'Enter G Virtual Tryon Settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function gvtryon_key_callback()
    {
        ?>
        <div class="fields-wrapper">
            <?php
            printf(
                '<input type="text" class="field" id="gvtryon_activation_key" name="gvtryon_plugin_setting[gvtryon_activation_key]" value="%s" />',
                isset($this->options['gvtryon_activation_key']) ? esc_attr($this->options['gvtryon_activation_key']) : ''
            );
            ?>
        </div>
        <?php
    }
}

if (is_admin())
    $gvtryon_plugin_setting_page = new GVTRYON_PluginSettings();