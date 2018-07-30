<?php

class Gc_Netsuite
{
    const CONTACT_FORM_7 = 'cf7';
    const GRAVITY_FORM = 'gform';

    const CONFIG_NAME = 'gc_netsuite_config';
    const PPC_SESSION_NAME = 'gc_netsuite_ppc_keyword';
    /** @var $config */
    protected static $config;
    /** @var \Gc_Netsuite_Loader $loader */
    protected $loader;
    /** @var string $version */
    protected $version;
    /** @var string $plugin_name */
    protected $plugin_name;
    /** @var string $plugin_display_name */
    protected $plugin_display_name;

    /**
     * Gc_Netsuite constructor.
     */
    public function __construct()
    {
        $this->version = GC_NETSUITE_VERSION;
        $this->plugin_name = GC_NETSUITE_PLUGIN_NAME;
        $this->plugin_display_name = GC_NETSUITE_PLUGIN_DISPLAY_NAME;
        $this->wpse_session_start();
        $this->load_dependencies();
        $this->define_admin_methods();
        $this->define_public_methods();
    }

    protected function wpse_session_start()
    {
        if (!session_id()) {
            session_start();
        }
    }

    private function load_dependencies()
    {
        require GC_NETSUITE_DIR . '/includes/gc-netsuite-loader.php';
        require GC_NETSUITE_DIR . '/public/gc-netsuite-public.php';
        require GC_NETSUITE_DIR . '/admin/gc-netsuite-admin.php';

        $this->loader = new Gc_Netsuite_Loader();
    }

    public function define_admin_methods()
    {
        $plugin_admin = new Gc_Netsuite_Admin();

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_init', $plugin_admin, 'init_options_page');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_options_page');
    }

    private function define_public_methods()
    {
        $plugin_public = new Gc_Netsuite_Public();

        // register user info
        $this->loader->add_action('wp_head', $plugin_public, 'init');

        $integration = boolval(Gc_Netsuite::get_config('enabled_integration'));

        if ($integration === self::CONTACT_FORM_7) {
            $this->loader->add_filter('wpcf7_posted_data', $plugin_public, 'add_user_info_to_posted_data', 10, 1);
            $this->loader->add_filter('wpcf7_before_send_mail', $plugin_public, 'submit_posted_data', 10);
        }

        if ($integration === self::GRAVITY_FORM) {
            $this->loader->add_action('gform_pre_submission_1', $plugin_public, 'public_submit', 10);
        }
    }

    public static function get_config($field = null)
    {
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
                    if (isset($value[$level])) {
                        $value = $value[$level];
                    } else {
                        return null;
                    }
                }
            } else {
                $value = isset(self::$config[$field]) ? self::$config[$field] : null;
            }

            return $value;
        }
        return self::$config;
    }

    public function run()
    {
        $this->loader->run();
    }

    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    public function get_plugin_display_name()
    {
        return $this->plugin_display_name;
    }

    public function get_loader()
    {
        return $this->loader;
    }

    public function get_version()
    {
        return $this->version;
    }
}
