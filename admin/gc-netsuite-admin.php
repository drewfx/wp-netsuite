<?php

class Gc_Netsuite_Admin
{
    private $options;
    private $plugin_name;
    private $plugin_display_name;
    private $version;

    public function __construct()
    {
        $this->plugin_name = GC_NETSUITE_PLUGIN_NAME;
        $this->plugin_display_name = GC_NETSUITE_PLUGIN_DISPLAY_NAME;
        $this->version = GC_NETSUITE_VERSION;
        $this->options = array();
    }

    public function enqueue_styles()
    {
        //
    }

    public function enqueue_scripts()
    {
        //
    }

    public function add_options_page()
    {
        add_options_page('Netsuite Integration', 'Netsuite', 'manage_options', 'gc_netsuite', array($this, 'render_options_page'));
    }

    public function init_options_page()
    {
        register_setting('gc_netsuite', Gc_Netsuite::CONFIG_NAME);

        add_settings_section(
            'general',
            'General',
            array($this, 'render_general_description'),
            'gc_netsuite'
        );
        add_settings_field(
            'enable_netsuite',
            'Enable Netsuite Integration',
            array($this, 'render_enable_netsuite'),
            'gc_netsuite',
            'general'
        );
        add_settings_field(
            'netsuite_endpoint',
            'Netsuite Endpoint',
            array($this, 'render_netsuite_endpoint'),
            'gc_netsuite',
            'general'
        );
        add_settings_field(
            'ppc_keyword',
            'PPC keyword',
            array($this, 'render_ppc_keyword'),
            'gc_netsuite',
            'general'
        );
        add_settings_field(
            'enable_ppc_keyword',
            'Enable PPC keyword',
            array($this, 'render_enable_ppc_keyword'),
            'gc_netsuite',
            'general'
        );
    }

    public function render_options_page()
    {
        echo "<form action='options.php' method='post'><h2>Netsuite Integration</h2>";
        settings_fields('gc_netsuite');
        do_settings_sections('gc_netsuite');
        submit_button();
        echo "</form>";
    }

    public function render_general_description()
    {
        echo '<h4>Configure Netsuite settings.</h4>' .
            '<p>Enable the Netsuite Integration for leads to flow through to Netsuite.<br>' .
            '<strong>Netsuite Endpoint</strong>: where the leads will flow through.<br>';
    }

    public function render_enable_netsuite()
    {
        $enabled = boolval(Gc_Netsuite::get_config('enable_netsuite'));
        echo "<select name='" . Gc_Netsuite::CONFIG_NAME . "[enable_netsuite]'>
                <option value='1' " . ($enabled ? 'selected' : '') . ">Yes</option>
                <option value='0' " . ($enabled ? '' : 'selected') . ">No</option>
            </select>";
    }

    public function render_netsuite_endpoint()
    {
        echo $this->create_input('netsuite_endpoint');
    }

    public function render_ppc_keyword()
    {
        echo $this->create_input('ppc_keyword');
    }

    public function render_enable_ppc_keyword()
    {
        $enabled = boolval(Gc_Netsuite::get_config('enable_ppc_keyword'));
        echo "<select name='".Gc_Netsuite::CONFIG_NAME."[enable_ppc_keyword]'>
                <option value='1' ".($enabled ? 'selected' : '').">Yes</option>
                <option value='0' ".($enabled ? '' : 'selected').">No</option>
            </select>";
	}

    public function create_input($id, $type = 'text')
    {
        $name = Gc_Netsuite::CONFIG_NAME . "[" . $id . "]";
        $value = Gc_Netsuite::get_config($id);
        return "<input type='{$type}' id='{$id}' name='{$name}' value='{$value}'>";
    }
}
