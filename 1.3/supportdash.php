<?php

if (isset($sdDashboard) == False) {
  $sdDashboard = false;
}

if (class_exists('SupportDash') == False) {
  class SupportDash {

    /* plugin name */
    private $name;

    /* plugin version */
    private $version;

    private $id;

    private $url = 'https://app.supportdash.com/plugin';

    /*
     * constructor
     */
    function __construct ($pname, $pversion, $pId) {
      $this->name = $pname;
      $this->version = $pversion;
      $this->id = $pId;

      /* try to get current user id */
      $token = get_option('sdToken');

      if (!$token) {
        $userdata = wp_get_current_user();
        $user_email = md5($userdata->user_email);
        update_option('sdToken', md5(date('m/d/Y h:i:s a') . $user_email));
      }


      /* register hooks */
      add_action('admin_menu',array($this, 'tryAddingDashboard'));
      add_action('admin_menu',array($this, 'addPlugin'));
    }

    /*
     * getName
     **/
    function getName () {
      return $this->name;
    }

    /*
     * getVersion
     **/
    function getVersion () {
      return $this->version;
    }

    /*
     * tryAddingDashboard
     */
    function tryAddingDashboard () {
      /* check if another support dash plugin already added the dashboard */
      global $sdDashboard;
      if ($sdDashboard == True) {
        return;
      }
      $sdDashboard = True;

      /* add the support dashboard menu page */
      add_menu_page('Support', 'Support', 'manage_options', 'SupportDash', array($this, 'addDashboard'),
        'https://i.imgur.com/rPZQ4tY.png', 81);
      //add_options_page('Inbox', 'Inbox', 'manage_options', 'SupportDash', function(){});
    }

    /*
     * addDashboard
     */
    function addDashboard () {
      echo '<script src="' . $this->url . '/app.js"></script>';
      echo '<link rel="stylesheet" type="text/css" href="' . $this->url . '/app.css" media="all"/>';

      $userdata = wp_get_current_user();
      $data = array(
        'name' => $userdata->display_name,
        'hashedEmail' => md5($userdata->user_email),
        'productVersion' => $this->version,
        'token' => get_option('sdToken'), /* will be false if they were never given one */
        'productId' => $this->id,
        'url' => get_site_url(),
        'sdUrl' => 'https://app.supportdash.com'
      );

      echo '<div ng-app="supportdash-dashboard">';
      echo "<p ng-init='data = " . json_encode($data) . ";'></p>";
      echo '<ng-view></ng-view></div>';
    }

    /*
     * addPlugin
     */
    function addPlugin () {
      $slug = (string)(strtolower(str_replace(' ', '', $this->name)));
      add_submenu_page('SupportDash', $this->name, $this->name, 'manage_options', $this->id . '-sd', array($this, 'constructPlugin'));
    }

    function constructPlugin () {
      echo '<script src="' . $this->url . '/app.js"></script>';
      echo '<link rel="stylesheet" type="text/css" href="' . $this->url . '/app.css" media="all"/>';

      $userdata = wp_get_current_user();
      $data = array(
        'name' => $userdata->display_name,
        'hashedEmail' => md5($userdata->user_email),
        'productVersion' => $this->version,
        'token' => get_option('sdToken'), /* will be false if they were never given one */
        'productId' => $this->id,
        'url' => get_site_url(),
        'sdUrl' => 'https://app.supportdash.com'
      );

      echo '<div ng-app="supportdash-plugin">';
      echo "<p ng-init='data = " . json_encode($data) . ";'></p>";
      echo '<ng-view></ng-view></div>';
    }

  }
}