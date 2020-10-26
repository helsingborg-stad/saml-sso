<!-- SHIELDS -->
[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![License][license-shield]][license-url]

<p>
  <a href="https://github.com/helsingborg-stad/dev-guide">
    <img src="images/logo.jpg" alt="Logo" width="300">
  </a>
</p>
<h3>SAML SSO</h3>
<p>
  SAML SSO plugin for Wordpress
  <br />
  <a href="https://github.com/helsingborg-stad/dev-guide/issues">Report Bug</a>
  ·
  <a href="https://github.com/helsingborg-stad/dev-guide/issues">Request Feature</a>
</p>



## Table of Contents
- [Table of Contents](#table-of-contents)
- [About SAML SSO](#about-saml-sso)
  - [Built With](#built-with)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Configuration](#configuration)
    - [Required PHP-SAML Constants](#required-php-saml-constants)
    - [Optional PHP-SAML Constants](#optional-php-saml-constants)
    - [Additional PHP-SAML settings.](#additional-php-saml-settings)
    - [Other constants](#other-constants)
- [Usage](#usage)
- [Roadmap](#roadmap)
- [Contributing](#contributing)
- [License](#license)
- [Acknowledgements](#acknowledgements)



## About SAML SSO
SAML SSO login plugin for wordpress.
This plugin will add 3 endpoints required for SAML SSO to operate.  
```
/saml/acs
/saml/logout
/saml/metadata
```

NOTE: Currently only invokable programatically se [usage](#usage) for instructions.



### Built With

* [Onelogin PHP SAML](https://github.com/onelogin/php-saml/)



## Getting Started

To get a local copy up and running follow these steps.



### Prerequisites

* Composer  
[Install instructions](https://getcomposer.org/download/) 



### Installation

1. Clone the repo
```sh
git clone https://github.com/helsingborg-stad/saml-sso.git
```
2. Install Composer packages
```sh
composer install
```



### Configuration

Configuration is done with constants in `wp-config.php`  
PHP-SAML constants is in direct relation to settings the [PHP-SAML](https://github.com/onelogin/php-saml/) require.  
Check documentation in PHP SAML for more information about these settings.  

Example configuration:  
```
// NOTE Never place certificates below the root web folder!
$idpCertificateFile = '/etc/certs/idp-cert.cer';
$spCertificateFile = '/etc/certs/sp-cert.cer';
$spCertificateKeyFile = '/etc/certs/sp-cert.key';

define('SAML_SP_ENITITY_ID', 'https://www.example.com/saml/metadata');
define('SAML_SP_ACS_URL', 'https://www.example.com/saml/acs');
define('SAML_IDP_ENTITY_ID', 'https://www.example.com/adfs/services/trust');
define('SAML_IDP_SSO_URL', 'https://www.example.com/adfs/ls/');
define('SAML_IDP_SLS_URL', 'https://www.example.com/adfs/ls/');

if (file_exists($idpCertificateFile)) {
    define('SAML_IDP_CERTIFICATE', file_get_contents($idpCertificateFile));
}

if (file_exists($spCertificateFile)) {
    define('SAML_SP_CERTIFICATE', file_get_contents($spCertificateFile));
}

if (file_exists($spCertificateKeyFile)) {
    define('SAML_SP_CERTIFICATE_KEY', file_get_contents($spCertificateKeyFile));
}

```



#### Required PHP-SAML Constants

The below constants is required to be set in your `wp-config.php` file.  
```
SAML_SP_ENITITY_ID
SAML_SP_ACS_URL
SAML_SP_CERTIFICATE
SAML_SP_CERTIFICATE_KEY
SAML_IDP_ENTITY_ID
SAML_IDP_SSO_URL
SAML_IDP_SLS_URL
```



#### Optional PHP-SAML Constants

Optional constants and their default value to be set in your `wp-config.php` file.  
```
SAML_STRICT => true
SAML_DEBUG => false
SAML_SP_ACS_BINDING => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
SAML_SP_NAME_ID_FORMAT => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
SAML_IDP_SSO_BINDING => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
SAML_IDP_SLS_BINDING => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
SAML_IDP_CERTIFICATE => null,
SAML_SECURITY_REQUESTED_AUTHN_CONTEXT => false,
SAML_SECURITY_SIGNATURE_ALGORITHM' => 'http://www.w3.org/2001/04/xmlenc#sha256',
SAML_SECURITY_DIGEST_ALGORITHM' => 'http://www.w3.org/2001/04/xmlenc#sha256',
SAML_SECURITY_LOWERCASE_URL_ENCODING => true,
```



#### Additional PHP-SAML settings.

Additional PHP-SAML settings can be added with the `saml-sso-settings` filter.



#### Other constants

Two constants is present for mapping configuration.  
Overwrite this with constants `SAML_ATTRIBUTES_MAPPING` and `SAML_GROUP_ROLE_MAPPING` in `wp-config-php`.

Default values:
```php
SAML_ATTRIBUTES_MAPPING = [
    'username' => 'http://schemas.microsoft.com/ws/2008/06/identity/claims/windowsaccountname',
    'first-name' => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname',
    'last-name' => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname',
    'email' => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress',
    'ad-groups' => 'http://schemas.xmlsoap.org/claims/Group',
];

SAML_MAP_TO_AD_GROUP = true;

SAML_AD_GROUP_ROLE_MAPPING = [
    'Domain Users' => 'subscriber',
    'Domain Admins' => 'administrator'
];
```



## Usage

When configured and plugin is activated, the code below can be used to trigger a SAML SSO login.  
```php
if (class_exists('\SAMLSSO\Client')) {
    $client = new \SAMLSSO\Client();
    // Supply where to redirect after login.
    $client->authenticate('https://www.example.com/redirect');
}
```



## Roadmap

See the [open issues][issues-url] for a list of proposed features (and known issues).



## Contributing

Contributions are what make the open source community such an amazing place to be learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request



## License

Distributed under the [MIT License][license-url].



## Acknowledgements

- [othneildrew Best README Template](https://github.com/othneildrew/Best-README-Template)



<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/helsingborg-stad/saml-sso.svg?style=flat-square
[contributors-url]: https://github.com/helsingborg-stad/saml-sso/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/helsingborg-stad/saml-sso.svg?style=flat-square
[forks-url]: https://github.com/helsingborg-stad/saml-sso/network/members
[stars-shield]: https://img.shields.io/github/stars/helsingborg-stad/saml-sso.svg?style=flat-square
[stars-url]: https://github.com/helsingborg-stad/saml-sso/stargazers
[issues-shield]: https://img.shields.io/github/issues/helsingborg-stad/saml-sso.svg?style=flat-square
[issues-url]: https://github.com/helsingborg-stad/saml-sso/issues
[license-shield]: https://img.shields.io/github/license/helsingborg-stad/saml-sso.svg?style=flat-square
[license-url]: https://raw.githubusercontent.com/helsingborg-stad/saml-sso/master/LICENSE
[product-screenshot]: images/screenshot.png
