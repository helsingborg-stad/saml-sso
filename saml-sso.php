<?php

/**
 * Plugin Name:       SAML SSO
 * Plugin URI:        saml-sso
 * Description:       SAML SSO solution for Wordpress
 * Version:           1.0.1
 * Author:            Joel Bernerman <joel.bernerman@helsingborg.se>
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 */

 // Protect agains direct file access
if (! defined('WPINC')) {
    die;
}

define('SAML_SSO_PATH', plugin_dir_path(__FILE__));
define('SAML_SSO_URL', plugins_url('', __FILE__));

// Autoload from ABSPATH
if (file_exists(SAML_SSO_PATH . '/vendor/autoload.php')) {
    require_once SAML_SSO_PATH . '/vendor/autoload.php';
}

require_once SAML_SSO_PATH . 'source/php/Vendor/Psr4ClassLoader.php';
require_once SAML_SSO_PATH . 'Public.php';

// Instantiate and register the autoloader
$loader = new SAMLSSO\Vendor\Psr4ClassLoader();
$loader->addPrefix('SAMLSSO', SAML_SSO_PATH);
$loader->addPrefix('SAMLSSO', SAML_SSO_PATH . 'source/php/');
$loader->register();

// Start application
new SAMLSSO\App();
