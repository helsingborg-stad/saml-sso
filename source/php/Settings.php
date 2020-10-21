<?php

namespace SAMLSSO;

use OneLogin\Saml2\Auth;

/**
 * Class only handles settings that is directly used by the saml-php library.
 * Same format as used in the actual lib described in here https://github.com/onelogin/php-saml.
 */
class Settings
{
    private $settings = [];

    private $requiredConstants = [
        'SAML_SP_ENITITY_ID',
        'SAML_SP_ACS_URL',
        'SAML_SP_CERTIFICATE',
        'SAML_SP_CERTIFICATE_KEY',
        'SAML_IDP_ENTITY_ID',
        'SAML_IDP_SSO_URL',
        'SAML_IDP_SLS_URL',
    ];
    
    private $defaultConstants = [
        'SAML_STRICT' => true,
        'SAML_DEBUG' => false,
        'SAML_SP_ACS_BINDING' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
        'SAML_SP_NAME_ID_FORMAT' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
        'SAML_IDP_SSO_BINDING' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        'SAML_IDP_SLS_BINDING' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        'SAML_IDP_CERTIFICATE' => null,
        'SAML_SECURITY_REQUESTED_AUTHN_CONTEXT' => false,
        'SAML_SECURITY_SIGNATURE_ALGORITHM' => 'http://www.w3.org/2001/04/xmlenc#sha256',
        'SAML_SECURITY_DIGEST_ALGORITHM' => 'http://www.w3.org/2001/04/xmlenc#sha256',
        'SAML_SECURITY_LOWERCASE_URL_ENCODING' => true,
    ];

    public $missingSettingsMessage = '';
    public $isSettingsSatisfied = true;

    /**
     * Constructor. Overrides default settings and check if required settings is missing.
     */
    public function __construct()
    {
        $this->overrideDefaults();
        $missingConstants = $this->getMissingRequiredConstants();
        
        if (count($missingConstants) > 0) {
            $this->missingSettingsMessage = '<b>SAML SSO Plugin</b> is missing required settings! <br>' .
                                            'Required constants needs to be present in you wp-config.php:<br>' .
                                            implode(',<br>', $missingConstants);

            add_action('admin_notices', array($this, 'settingsNotice'));
            $this->isSettingsSatisfied = false;
            error_log(strip_tags($this->missingSettingsMessage));
        }
    }

    /**
     * Check if any constants is missing.
     *
     * @return array All the missing constants.
     */
    public function getMissingRequiredConstants()
    {
        $missingConstants = [];
        foreach ($this->requiredConstants as $requiredConstant) {
            if (!defined($requiredConstant)) {
                $missingConstants[] = $requiredConstant;
            }
        }
        return $missingConstants;
    }

    /**
     * Override any default settings if the constant is set in config.
     *
     * @return void
     */
    public function overrideDefaults()
    {
        foreach ($this->defaultConstants as $defaultConstant => $defaultValue) {
            if (defined($defaultConstant)) {
                $this->defaultConstants[$defaultConstant] = constant($defaultConstant);
            }
        }
    }

    /**
     * Get all settings.
     *
     * @return array Settings structure as SAML-PHP lib wants it with added mapping for groups and attributes.
     */
    public function getSettings()
    {
        $settings = [
            'strict' => $this->defaultConstants['SAML_STRICT'],
            'debug' => $this->defaultConstants['SAML_DEBUG'],
            'sp' => [
                'entityId' => SAML_SP_ENITITY_ID,
                'assertionConsumerService' => [
                    'url' => SAML_SP_ACS_URL,
                    'binding' => $this->defaultConstants['SAML_SP_ACS_BINDING'],
                ],
                'NameIDFormat' => $this->defaultConstants['SAML_SP_NAME_ID_FORMAT'],
                'x509cert' => SAML_SP_CERTIFICATE,
                'privateKey' => SAML_SP_CERTIFICATE_KEY,
            ],
            'idp' => [
                'entityId' => SAML_IDP_ENTITY_ID,
                'singleSignOnService' => [
                    'url' => SAML_IDP_SSO_URL,
                    'binding' => $this->defaultConstants['SAML_IDP_SSO_BINDING'],
                ],
                'singleLogoutService' => [
                    'url' => SAML_IDP_SLS_URL,
                    'binding' => $this->defaultConstants['SAML_IDP_SLS_BINDING'],
                ],
                'x509cert' => $this->defaultConstants['SAML_IDP_CERTIFICATE'],
            ],
            'security' => [
                'requestedAuthnContext' => $this->defaultConstants['SAML_SECURITY_REQUESTED_AUTHN_CONTEXT'],
                'signatureAlgorithm' => $this->defaultConstants['SAML_SECURITY_SIGNATURE_ALGORITHM'],
                'digestAlgorithm' => $this->defaultConstants['SAML_SECURITY_DIGEST_ALGORITHM'],
                'lowercaseUrlencoding' => $this->defaultConstants['SAML_SECURITY_LOWERCASE_URL_ENCODING'],
            ]
        ];

        return apply_filters('saml-sso-settings', $settings);
    }

    /**
     * Prints a notice if missing settings constants.
     *
     * @return void
     */
    public function settingsNotice()
    {
        print '<div class="notice notice-error"><p>' . $this->missingSettingsMessage . '</p></div>';
    }
}
