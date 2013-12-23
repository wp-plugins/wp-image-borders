<?php

//
// supportdash.php (version 2.0)
//
// compiled with ejs, so < % =  % > are replaced
//

if (class_exists('SupportDash') == False) {
    class SupportDash {

        /* plugin name */
        private $name;

        /* plugin version */
        private $version;

        private $id;

        private $url = 'https://app.supportdash.com/plugin';

        private $pluginVersion;

        /*
         * constructor
         */
        function __construct($pname, $pversion, $pId) {
            $this->name = $pname;
            $this->version = $pversion;
            $this->id = $pId;

            // set the api version that this file requires
            $this->pluginVersion = "v3";

            $this->url = $this->url . '/' . $this->pluginVersion;

            add_action('admin_init', array($this, 'init'));
        }

        function init () {
            /* try to get current user id */
            $token = get_option('sdToken');

            // if they don't have a support dash customer toke, create one
            if (!$token) {
                $userdata = wp_get_current_user();
                $user_email = md5($userdata->user_email);
                update_option('sdToken', md5(date('m/d/Y h:i:s a') . $user_email));
            }

            $this->constructPlugin();
        }

        /*
         * getName
         **/
        function getName() {
            return $this->name;
        }

        /*
         * getVersion
         **/
        function getVersion() {
            return $this->version;
        }

        function escapeInput ($input) {
            return htmlspecialchars($input, ENT_QUOTES);
        }

        function constructPlugin() {
            $userdata = wp_get_current_user();
            $data = array(
                'name' => $this->escapeInput($userdata->display_name),
                'hashedEmail' => md5($userdata->user_email),
                'productVersion' => $this->version,
                'wordpressVersion' => get_bloginfo('version'),
                'phpVersion' => phpversion(),
                'token' => get_option('sdToken'), /* will be false if they were never given one */
                'productId' => $this->id,
                'url' => get_site_url(),
                'pluginVersion' => $this->pluginVersion,
                'sdUrl' => 'https://app.supportdash.com'
            );

            echo '<script>function supportdashWordpressData () { return ' . json_encode($data) . ' ; }</script>';
            echo '<script src="' . $this->url . '/app.min.js"></script>';
            echo '<link rel="stylesheet" type="text/css" href="' . $this->url . '/app.css" media="all"/>';
        }

    }
}