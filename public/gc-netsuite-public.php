<?php

class Gc_Netsuite_Public
{
    private $plugin_name;
    private $version;
    private $post_data;
    private $endpoint = 'https://forms.na1.netsuite.com/app/site/crm/externalleadpage.nl?compid=1046918&formid=5&h=AACffht_-OLEtAl4YYo3343iksp5jSibbAY';
    private $success = 0;
    private $error = 0;

    public function __construct()
    {
        $this->plugin_name  = GC_NETSUITE_PLUGIN_NAME;
        $this->version      = GC_NETSUITE_VERSION;
    }

    public function submit_posted_data()
    {
        $this->post_data = array(
            'firstname'         => $_POST['txt_first_name'],
            'lastname'          => $_POST['txt_last_name'],
            'companyname'       => $_POST['txt_company'],
            'phone'             => $_POST['txt_phone'],
            'email'             => $_POST['txt_email_address'],
            //'custentity_esc_industry' => $_POST['industry'],
            //'comments'          => $_POST['comments'],
            //'url'               => '',                            //(web address)
            //'custentity124'     => $_POST['lead_source_details'], //(web form source)
            'campaigncategory'  => '12',
            //'custentity33'      => $this->get_ppc(),            // (campaign subcategory)
            //'custentity125'     => $this->get_keyword(),        // (keyword search)
            //'custentity129'     => $_POST['dataintegration'],   // (data integration checkbox)
            //'custentity128'     => $_POST['calltracking'],      // (call tracking checkbox)
            //'custentity127'     => $_POST['callrouting'],       // (call routing checkbox)
            //'custentity132'     => $_POST['vanitynumbers'],     // (800 numbers)
            'custentity131'     => ($_POST['leadsmarketplace']) ? "T" : "",  // (leads marketplace)
            //'custentitybulknumbers' => $_POST['bulknumbers'],
            'custentity32'      => ($_POST['newslttr']) ? "T" : "",        // (newsletter)
            //'custentityreferrer'=> $this->get_referrer(),
            //'custentitygclid'   => $this->get_gclid(),
            'custentity131_send' => '',
            'custentity32_send' => '',
        );

        #$this->clean();
        $this->submit();
    }


    public function clean()
    {
        foreach ($this->post_data as $k => $v) {
            if (empty(trim($v))) {
                unset($this->post_data[$k]);
            }
        }
    }


    /**
   * log it
   */
    public function submit()
    {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->post_data);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);

            $result = curl_exec($ch);

            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            var_dump([$this->post_data, $result, $httpcode]);

            if ($httpcode == '302') {
                $this->success = 1;
                $this->log_in_db("Success");
            } else {
                $this->error = 1;
                $this->log_in_db("Failure");
            }
        } catch (Exception $e) {
            $this->error = 1;
            $this->log_in_db($e->getMessage());
        }
    }

    public function log_in_db($message)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gc_netsuite_posts';

        try {
            $wpdb->insert($table_name, array(
                'url'     => $this->endpoint,
                'data'    => http_build_query($this->post_data),
                'success' => $this->success,
                'error'   => $this->error,
                'message' => $message
            ));
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function get_referrer()
    {
        return isset($_SESSION['off_site_referrer']) ? $_SESSION['off_site_referrer'] : null;
    }

    public function get_keyword()
    {
        return isset($_SESSION['search_keyword']) ? $_SESSION['search_keyword'] : null;
    }

    public function get_gclid()
    {
        if (!isset($_COOKIE['gclid']) || (isset($_GET['gclid']) && !isset($_SESSION['gclid']))) {
            return null;
        }

        $gclid = !empty($_GET['gclid']) ? urldecode($_GET['gclid']) : '';
        if (!empty($gclid)) {
            $expire = time() + 60 * 60 * 24;
            $domain = str_replace(array('http://', 'http://', '/'), '', get_site_url());
            setcookie('gclid', $gclid, ($expire * 30), '/', $domain);
        }

        return isset($_COOKIE['gclid']) ? $_COOKIE['gclid'] : null ;
    }

    public function get_ppc() 
    {
        $ppc = $this->_get_ppc();
        $source = 'Direct';
        $url = $_SESSION['off_site_referrer'];

        if (!empty($ppc)) {
            switch($ppc) {
                case 'google': $source = 'Google AdWords'; break;
                case 'bing'  : $source = 'Bing Paid'; break;
                case 'yahoo' : $source = 'Yahoo Paid'; break;
                default      : $source = ucwords($ppc); break;
            }
        } else if (!empty($url)) {
            $source = '';
            $common_sites = [
                'google'        => 'Google Organic',
                'bing'          => 'Bing Organic',
                'yahoo'         => 'Yahoo Organic',
                'facebook'      => 'Facebook Social',
                'linkedin'      => 'LinkedIn Social',
                'twitter'       => 'Twitter Social',
                'instagram'     => 'Instagram Social',
                'reddit'        => 'Reddit Social',
                'blogger'       => 'Blogger Social',
                'stackexchange' => 'Stack Exchange Social',
                'yelp'          => 'Yelp Social',
                'netvibes'      => 'Netvibes Social',
                'youtube'       => 'YouTube Social',
            ];
            $source = 'Organic';
            foreach ($common_sites as $id => $label) {
                if (__str_contains($url, $id)) {
                    $source = $label;
                    break;
                }
            }
        }

        return 'W2L: ' . $source;
    }

    protected function _get_ppc()
    {
        return isset($_SESSION['ppc']) ? ucwords($_SESSION['ppc']) : null;
    }
}
