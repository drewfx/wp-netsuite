<?php

class Gc_Netsuite_Public
{
  private $plugin_name;
  private $version;
  private $post_data;

  public function __construct() {
      $this->plugin_name    = GC_NETSUITE_PLUGIN_NAME;
      $this->version        = GC_NETSUITE_VERSION;
      $this->post_data      = array();
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

  public function submit() {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://forms.na1.netsuite.com/app/site/crm/externalleadpage.nl?compid=1046918&formid=5&h=AACffht_-OLEtAl4YYo3343iksp5jSibbAY');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->post_data);

    $result = curl_exec($ch);
    var_dump($result);
  }
}
