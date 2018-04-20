<?php

class Gc_Netsuite{

  const CONFIG_NAME = 'gc_netsuite_settings';

  protected $loader;
  protected $version;
  protected $plugin_name;
  protected $plugin_display_name;

  protected static $config;

  public function __construct(){
    $this->version = $this->return_or_define('GC_NETSUITE_VERSION', '1.0.0');
    $this->plugin_name = $this->return_or_define('GC_NETSUITE_PLUGIN_NAME', 'gc_netsuite');
    $this->$plugin_display_name = $this->return_or_define('GC_NETSUITE_PLUGIN_DISPLAY_NAME', 'GC Netsuite');
    $this->load_dependencies();
    $this->define_public_methods();
  }

  private function load_dependencies(){
    require GC_NETSUITE_DIR . '/includes/gc-netsuite-loader.php';
    require GC_NETSUITE_DIR . '/public/gc-netsuite-public.php';

    $this->loader = new Gc_Netsuite_Loader();
  }

  private function define_public_methods(){
    $plugin_public = new Gc_Netsuite_Public($this->plugin_name, $this->version);
    $this->loader->add_filter( 'wpcf7_before_send_mail', $plugin_public, 'submit_posted_data', 10 , 1);
  }

  private function return_or_define($check_if_defined = null, $default = null){
    if(!defined($check_if_defined)){
      define($check_if_defined, $default);
    }
    return $check_if_defined;
  }

  public function run(){
    $this->loader->run();
  }

  public function get_plugin_name(){
    return $this->plugin_name;
  }

  public function get_plugin_display_name(){
    return $this->$plugin_display_name;
  }

  public function get_loader(){
    return $this->loader;
  }

  public function get_version() {
    return $this->version;
  }

}
 ?>
