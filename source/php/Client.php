<?php

namespace SAMLSSO;

use OneLogin\Saml2\Auth;
use SAMLSSO\Settings;

class Client
{
    public $saml;

    public $attributesMapping = [
        'username' => 'http://schemas.microsoft.com/ws/2008/06/identity/claims/windowsaccountname',
        'first-name' => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname',
        'last-name' => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname',
        'email' => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress',
        'ad-groups' => 'http://schemas.xmlsoap.org/claims/Group',
    ];

    public $groupRoleMapping = [
        'Domain Users' => 'subscriber',
        'Domain Admins' => 'administrator'
    ];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $settings = new Settings();

        if ($settings->isSettingsSatisfied) {
            // Make the mappings overwritable with constants.
            if (defined('SAML_ATTRIBUTES_MAPPING')) {
                $this->attributesMapping = SAML_ATTRIBUTES_MAPPING;
            }

            if (defined('SAML_GROUP_ROLE_MAPPING')) {
                $this->groupRoleMapping = SAML_GROUP_ROLE_MAPPING;
            }

            $this->saml = new Auth($settings->getSettings());
        }
    }

    /**
     *  Authenticates the user using SAML
     *
     *  @return void
     */
    public function authenticate($relayState)
    {
        $this->saml->login($relayState);
    }


    /**
     * Authenticates the user and updates role with WordPress using wp signon.
     *
     * @param string $username The user to log in as.
     * @return void
     */
    public function simulateSignon($username)
    {
        $this->updateRole($username);
        $user = get_user_by('login', $username);

        wp_set_auth_cookie($user->ID);
        do_action('wp_login', $username, $user);

        if (array_key_exists('redirect_to', $_GET)) {
            wp_redirect($_GET['redirect_to']);
        } else {
            wp_redirect(network_home_url('/'));
        }
        
        die();
    }

    /**
     * Updates a user's role if their current one doesn't match the attributes provided by the IdP
     *
     * @return void
     */
    public function updateRole($username)
    {
        $attributes = $this->saml->getAttributes();
        $role = false;
        foreach ($attributes[$this->attributesMapping['ad-groups']] as $group) {
            if (isset($groupRoleMapping[$group])) {
                $role = $groupRoleMapping[$group];
            }
        }

        $user = get_user_by('login', $username);
        if ($user) {
            $user->set_role($role);
        }
    }
}
