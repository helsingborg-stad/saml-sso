<?php

namespace SAMLSSO\Endpoints;

use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Error;
use SAMLSSO\Client;

class ACS
{
    /**
     * Handles the response in /saml/acs endpoint.
     *
     * @return void
     */
    public static function run()
    {
        $client = new Client();
        $client->saml->processResponse();
        
        $errors = $client->saml->getErrors();
        
        if (!empty($errors)) {
            echo '<p>' . implode(', ', $errors) . '</p>';
            exit();
        }
        
        if (!$client->saml->isAuthenticated()) {
            echo '<p>Not authenticated</p>';
            exit();
        }

        $attributes = $client->saml->getAttributes();
        $username = $attributes[$client->attributesMapping['username']][0];
        $client->simulateSignon($username);
        // TODO Make sure the redirect works.
        if (isset($_POST['RelayState']) && Utils::getSelfURL() != $_POST['RelayState']) {
            $client->saml->redirectTo($_POST['RelayState']);
        }
    }
}
