<?php

class Gc_Netsuite
{

    const CONFIG_NAME = 'gc_netsuite_config';

    protected $loader;
    protected $version;
    protected $plugin_name;
    protected $plugin_display_name;

    protected static $config;

    public function __construct() {
        $this->version             = GC_NETSUITE_VERSION;
        $this->plugin_name         = GC_NETSUITE_PLUGIN_NAME;
        $this->plugin_display_name = GC_NETSUITE_PLUGIN_DISPLAY_NAME;
        $this->load_dependencies();
        $this->define_admin_methods();
        $this->define_public_methods();
    }

    private function load_dependencies() {
        require GC_NETSUITE_DIR . '/includes/gc-netsuite-loader.php';
        require GC_NETSUITE_DIR . '/public/gc-netsuite-public.php';
        require GC_NETSUITE_DIR . '/admin/gc-netsuite-admin.php';

        $this->loader = new Gc_Netsuite_Loader();
    }

    public function define_admin_methods() {
        $plugin_admin = new Gc_Netsuite_Admin();
        $this->loader->add_action('admin_init', $plugin_admin, 'init_options_page');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_options_page');
    }

    private function define_public_methods() {
        $plugin_public = new Gc_Netsuite_Public();
        $this->loader->add_filter('wpcf7_before_send_mail', $plugin_public, 'submit_posted_data', 10, 1);
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_plugin_display_name() {
        return $this->plugin_display_name;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }

    public static function get_config($field = null) {
        if (!isset(self::$config)) {
            self::$config = get_option(self::CONFIG_NAME);
            if (!self::$config) {
                return null;
            }
        }
        if ($field) {
            if (is_array($field)) {
                $value = self::$config;
                foreach ($field as $level) {
                    if (isset($value[ $level ])) {
                        $value = $value[ $level ];
                    } else {
                        return null;
                    }
                }
            } else {
                $value = isset(self::$config[ $field ]) ? self::$config[ $field ] : null;
            }

            return $value;
        }
        return self::$config;
    }
}
