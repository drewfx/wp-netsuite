<?php

/**
 * Based off the boilerplate provided by:
 *  https://github.com/DevinVinson/WordPress-Plugin-Boilerplate
 * Link provided by Drew Ruppel.
 *
 * Plugin Name: GC Netsuite
 * Description: ???
 * Version: 1.0.0
 * Author: Matthew Belanic
 * Author URI: https://goldencomm.com
 */

 if(! defined('ABSPATH')){
   exit;
 }

 define('GC_NETSUITE_PLUGIN_NAME' , 'gc_netsuite');

 define('GC_NETSUITE_PLUGIN_DISPLAY_NAME' , 'GC Netsuite');

 define("GC_NETSUITE_VERSION" , '1.0.0');

 define("GC_NETSUITE_DIR", __DIR__);

 define('GC_NETSUITE_INCLUDES_DIR' , GC_NETSUITE_DIR . '/includes');

 function activate_gc_netsuite(){
   require_once GC_NETSUITE_INCLUDES_DIR . '/gc-netsuite-activator.php';
   Gc_Netsuite_Activator::activate();
 }

 function deactivate_gc_netsuite(){
   require_once GC_NETSUITE_INCLUDES_DIR . '/gc-netsuite-deactivator.php';
   Gc_Netsuite_Deactivator::deactivate();
 }

 register_activation_hook( __FILE__, 'activate_gc_netsuite');
 register_deactivation_hook( __FILE__, 'deactivate_gc_netsuite');

 require GC_NETSUITE_INCLUDES_DIR . '/gc-netsuite.php';

 function run_gc_netsuite() {
     $plugin = new Gc_Netsuite();
     $plugin->run();
 }

 run_gc_netsuite();

 ?>
