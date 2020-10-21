<?php

namespace SAMLSSO\Endpoints;

use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Error;
use SAMLSSO\Client;

class Metadata
{
    /**
     * Handles the output in /saml/metadata endpoint.
     *
     * @return void
     */
    public static function run()
    {
        try {
            $client = new Client();

            $settings = $client->saml->getSettings();
            $metadata = $settings->getSPMetadata();
            $errors = $settings->validateMetadata($metadata);
            if (empty($errors)) {
                header('Content-Type: text/xml');
                echo $metadata;
            } else {
                throw new Error(
                    'Invalid SP metadata: ' . implode(', ', $errors),
                    Error::METADATA_SP_INVALID
                );
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
