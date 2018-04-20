<?php

class Gc_Netsuite_Public
{
  private $plugin_name;
  private $version;
  private $post_data;

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
      http_build_query($posted_data);
      submit();
  }

  public function submit(){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://forms.na1.netsuite.com/app/site/crm/externalleadpage.nl?compid=1046918&formid=5&h=AACffht_-OLEtAl4YYo3343iksp5jSibbAY');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

    $result = curl_exec($ch);
    var_dump($reult)
  }
}
