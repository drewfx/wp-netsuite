<?php

class Gc_Netsuite_i18n
{
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            GC_NETSUITE_PLUGIN_NAME,
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/');
    }
}
