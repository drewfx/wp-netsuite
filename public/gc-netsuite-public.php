<?php

class Gc_Netsuite_Public
{
    private $plugin_name;
    private $version;
    private $post_data;
    private $endpoint = 'https://forms.na1.netsuite.com/app/site/crm/externalleadpage.nl?compid=1046918&formid=5&h=AACffht_-OLEtAl4YYo3343iksp5jSibbAY';
    private $success = 0;
    private $error = 0;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function submit_posted_data() {
        $this->post_data = [
            'firstname' => $_POST['first-name'],
            'lastname' => $_POST['last-name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone-number'],
            'companyname' => $_POST['company'],
            'comments' => $_POST['comments']
        ];

        $this->submit();
    }

  /**
   * log it
   */
    public function submit() {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->post_data);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);

            curl_exec($ch);

            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpcode == '302') {
                $this->success = 1;
                $this->record_in_db("Success");
            } else {
                $this->success = 1;
                $this->record_in_db("Success");
            }
        } catch (Exception $e) {
            $this->error = 1;
            $this->record_in_db($e->getMessage());
        }
    }

    public function record_in_db($message){
        global $wpdb;
        $table_name = $wpdb->prefix . 'gc_netsuite_posts';

        $wpdb->insert($table_name, array(
            'url' => $this->endpoint,
            'data' => http_build_query($this->post_data),
            'success' => $this->success,
            'error' => $this->error,
            'message' => $message
        ));
    }
}
