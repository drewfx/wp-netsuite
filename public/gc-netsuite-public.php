<?php

class Gc_Netsuite_Public
{
    /**
     * These values are constant in Netsuite.
     */
    const WEB_LEAD = 11;
    const DIAL = 17;
    const SOCIAL = 205;
    const ORGANIC = 27597591;
    const WEB_DIRECT = 28438140;
    const PPC = 27353440;
    const COMPANY_TYPE = 65;
    const PRODUCT_LINE = 46;
    const SUPPORT = '7131';
    const CUSTOMER_CARE = '7317';

    private $plugin_name;
    private $admin = 'druppel@goldencomm.com';
    private $version;
    private $post_data;
    private $endpoint = '';
    private $success = 0;
    private $error = 0;
    private $response = '';

    /**
     * Accepted industry types
     * @var array
     */
    private $industries = [
        'Advertising/Direct Response Agency' => 79,
        'Franchise' => 83,
        'Wellness & Fitness' => 86,
        'Legal Services' => 49,
        'Educational Services' => 40,
        'Mortgage Industry' => 52,
        'Insurance' => 47,
        'Other' => 53,
    ];

    /**
     * Accepted social site designations.
     * @var array
     */
    private $social = [
        'facebook', 'linkedin',
        'twitter', 'instagram',
        'reddit', 'blogger',
        'stackexchange', 'yelp',
        'netvibes', 'youtube'
    ];

    public function __construct()
    {
        $this->plugin_name = GC_NETSUITE_PLUGIN_NAME;
        $this->version = GC_NETSUITE_VERSION;
        $this->endpoint = GC_Netsuite::get_config('netsuite_endpoint');
    }

    public function init()
    {
        $this->save_ppc_value_in_session();
        $this->save_referrer_in_session();
        $this->save_uri_to_session();
    }

    /**
     * Saves the PPC (pay per click) value to the session.
     */
    public function save_ppc_value_in_session()
    {
        $ppc_keyword = Gc_Netsuite::get_config('ppc_keyword');
        if ($ppc_keyword && isset($_GET[$ppc_keyword]) && !isset($_SESSION[Gc_Netsuite::PPC_SESSION_NAME])) {
            $_SESSION[Gc_Netsuite::PPC_SESSION_NAME] = $_GET[$ppc_keyword];
        }
    }

    /**
     * Saves the referrer to the local domain to the session.
     */
    public function save_referrer_in_session()
    {
        if (!isset($_SESSION['off_site_referrer'])) {
            $_SESSION['off_site_referrer'] = $_SERVER['HTTP_REFERER'];
        }
    }

    /**
     * Concatenates the 'visited path' to the session for submission.
     */
    public function save_uri_to_session()
    {
        global $wp_the_query;
        if (!isset($_SESSION['VISITED_PATH']))
            $_SESSION['VISITED_PATH'] = '';
        if (!$wp_the_query->is_404)
            $_SESSION['VISITED_PATH'] .= "\n$_SERVER[REQUEST_URI]";
    }

    /**
     * Format our posted data, clean and then submit.
     * Refer to the web lead form in Netsuite: Setup -> Sales & Marketing Automation -> Online Customer Forms
     * before adding any additional fields. An associated ID will be required to pass the fields or it will error out.
     */
    public function submit_posted_data()
    {
        $enabled = boolval(Gc_Netsuite::get_config('enable_netsuite'));

        if ($enabled && !$this->support()) {
            $this->post_data = array(
                'compid' => 1046918,                                     // web form fields
                'formid' => 5,                                           // web form fields
                'h' => 'AACffht_-OLEtAl4YYo3343iksp5jSibbAY',       // web form fields
                'firstname' => $this->get_firstname(),
                'lastname' => $this->get_lastname(),
                'companyname' => $this->get_company(),
                'phone' => $this->get_phone(),
                'email' => $this->get_email(),
                'custentity_esc_industry' => $this->get_industry(),
                'comments' => $this->get_comments(),
                'custentity124' => $this->get_submission_url(),                 // submission url
                'leadsource' => $this->get_ppc(),                            // lead source
                'custentity73' => self::WEB_LEAD,                              // campaign category
                'custentity33' => self::DIAL,                                  // campaign subcategory
                'custentity1' => self::COMPANY_TYPE,                          // company type
                'custentity125' => $this->get_keyword(),                        // search keyword
                'custentity129' => $this->checkbox($_POST['dataintegration']),
                'custentity128' => $this->checkbox($_POST['calltracking']),
                'custentity127' => $this->checkbox($_POST['callrouting']),
                'custentity131' => $this->checkbox($_POST['leadsmarketplace']),
                'custentitybulknumbers' => $this->checkbox($_POST['bulknumbers']),
                'custentity32' => $this->get_newsletter($_POST['newslttr']),
                'custentity132' => $this->checkbox($_POST['vanitynumbers']),
                'custentityreferrer' => $this->get_referrer(),
                'custentitygclid' => $this->get_gclid(),
                'custentity19' => $this->get_date(),
                'custentity12' => self::PRODUCT_LINE
            );

            $this->clean();
            $this->submit();
        }
    }

    private function support()
    {
        return ($_POST['_wpcf7'] == self::SUPPORT || $_POST['_wpcf7'] == self::CUSTOMER_CARE);
    }

    /**
     * @return string
     */
    private function get_firstname()
    {
        return $_POST['txt_first_name'];
    }

    /**
     * @return string
     */
    private function get_lastname()
    {
        return $_POST['txt_last_name'];
    }

    /**
     * @return string
     */
    private function get_company()
    {
        return $_POST['txt_company'];
    }

    /**
     * @return string
     */
    private function get_phone()
    {
        return $_POST['txt_phone'];
    }

    /**
     * @return string
     */
    private function get_email()
    {
        return $_POST['txt_email_address'];
    }

    /**
     * Search the array for a value matching our submitted value.
     *
     * @default string 'Other'
     * @return false|int|string
     */
    private function get_industry()
    {
        $industry = array_search($_POST['industry'],
            array_reverse(
                $this->industries
            )
        );

        if ($industry === false) {
            $industry = $this->industries['Other'];
        }
        return $industry;
    }

    /**
     * @return string
     */
    private function get_comments()
    {
        return (isset($_POST['notes'])) ? $_POST['notes'] : '';
    }

    /**
     * @return string
     */
    private function get_submission_url()
    {
        return explode('?', $_SERVER['HTTP_REFERER'])[0];
    }

    /**
     * @return string
     */
    private function get_ppc()
    {
        $source = self::WEB_DIRECT;
        $ppc = $this->_get_ppc();
        $ref_host = $this->get_referrer_host();

        if (!empty($ppc)) {
            $source = self::PPC;
        } else if (!empty($ref_host)) {
            $source = self::ORGANIC;

            foreach ($this->social as $k => $v) {
                if (stripos($ref_host, $v) !== false) {
                    $source = self::SOCIAL;
                    break;
                }
            }
        }

        return $source;
    }

    private function _get_ppc()
    {
        return isset($_SESSION['ppc']) ? ucwords($_SESSION['ppc']) : null;
    }

    /**
     * @return string
     */
    private function get_referrer_host()
    {
        $referrer = $this->get_referrer();

        if ($referrer) {
            return parse_url($referrer, PHP_URL_HOST);
        }

        return '';
    }

    /**
     * @return string
     */
    private function get_referrer()
    {
        return isset($_SESSION['off_site_referrer']) ? $_SESSION['off_site_referrer'] : '';
    }

    /**
     * @return string
     */
    private function get_keyword()
    {
        return isset($_SESSION['search_keyword']) ? $_SESSION['search_keyword'] : '';
    }

    /**
     * @return string
     */
    private function checkbox($value)
    {
        return (!empty($value)) ? 'T' : '';
    }

    /**
     * @param $value
     * @return string
     */
    private function get_newsletter($value)
    {
        return (!empty($value)) ? 'Yes' : 'No';
    }

    /**
     * @return string
     */
    private function get_gclid()
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

        return isset($_COOKIE['gclid']) ? $_COOKIE['gclid'] : '';
    }

    /**
     * @return string mm/dd/yyyy
     */
    private function get_date()
    {
        return date('m/d/Y');
    }

    /***       ~~~~~~~~~~~~~~~~~       ***/
    /***       User Info Methods       ***/
    /***       ~~~~~~~~~~~~~~~~~       ***/

    /**
     * Clean empty values from the post data.
     */
    public function clean()
    {
        foreach ($this->post_data as $k => $v) {
            if (empty(trim($v))) {
                unset($this->post_data[$k]);
            }
        }
    }

    /**
     * Submits the data to our Netsuite endpoint.
     * Logs success/error to `{prefix?}gc_netsuite_posts`
     */
    public function submit()
    {
        $query = http_build_query($this->post_data);

        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->endpoint . $query);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_SSLVERSION, 6);

            $this->response = curl_exec($ch);

            if ($this->response === false) {
                $this->error = 1;
                $this->log_in_db(sprintf(
                        'Curl failed with error #%d: %s',
                        curl_error($ch), curl_errno($ch))
                );
            } else {
                $this->success = 1;
                $this->log_in_db("Success");
            }

            curl_close($ch);

        } catch (\Exception $e) {
            $this->log_in_db(sprintf(
                    'Curl failed with error #%d: %s',
                    $e->getCode(), $e->getMessage())
            );
        }
    }

    /**
     * Log information in the database.
     * @param $message
     */
    private function log_in_db($message)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gc_netsuite_posts';

        try {
            $wpdb->insert($table_name, array(
                'submitted' => date('Y-m-d H:i:s'),
                'url' => $this->endpoint,
                'data' => http_build_query($this->post_data),
                'response' => $this->response,
                'success' => $this->success,
                'error' => $this->error,
                'message' => $message
            ));
        } catch (\Exception $e) {
            error_log($e->getMessage(), 1, $this->admin);
        }
    }
}
