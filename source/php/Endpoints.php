<?php

namespace SAMLSSO;

use OneLogin\Saml2\Auth;

class Endpoints
{
    // Enpoint and response class name array.
    public $endpoints = [
        'ACS' => 'acs',
        'Metadata' => 'metadata',
        'Logout' => 'logout'
    ];

    // Base endpoint.
    public $baseEndpoint = 'saml';

    /**
     * Constructor. Add neccessary actions and filters for our endpoints to work.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('init', [$this, 'addRewriteRules']);
        add_filter('query_vars', [$this, 'queryVars']);
        add_action('template_include', [$this, 'templateInclude']);
    }

    /**
     * Add the WP rewrite rules for the /saml/ endpoints.
     *
     * @return void
     */
    public function addRewriteRules()
    {
        add_rewrite_endpoint($this->baseEndpoint, EP_ROOT);
    }

    /**
     * Add our vars as allowed query vars for WP.
     *
     * @param array $queryVars array with current query vars from WP.
     *
     * @return array allowed query vars.
     */
    public function queryVars($queryVars)
    {
        return array_merge($queryVars, array_values($this->endpoints));
    }

    /**
     * Call our endpoint class function run() to get expected output or handling.
     *
     * @param string $template template location string from WP.
     *
     * @return string template to load if we dont hit our endpoints.
     */
    public function templateInclude($template)
    {
        global $wp_query;

        $samlEndpoint = get_query_var($this->baseEndpoint);
        foreach ($this->endpoints as $class => $endpoint) {
            if (strpos($samlEndpoint, $endpoint) === 0) {
                $className = '\\SAMLSSO\\Endpoints\\' . $class;
                $className::run();
                die();
            }
        }
        
        return $template;
    }

    /**
     * Flush the WP rewrite rules.
     *
     * @return void
     */
    public function flushRewiteRules()
    {
        flush_rewrite_rules();
    }

    /**
     * Check if current request uri is one of our endpoints.
     *
     * @return bool
     */
    public static function isSAMLSSOEndpoint()
    {
        $isSAMLSSOEndpoint = false;

        $endpoints = new Endpoints();
        // Subfolder mutisites got different path structure than single installs and subdomain multisite.
        if (is_multisite() && !is_subdomain_install()) {
            $baseEndpoint = get_blog_details()->path . $endpoints->baseEndpoint;
        } else {
            $baseEndpoint = $endpoints->baseEndpoint;
        }
        
        foreach ($endpoints->endpoints as $endpoint) {
            $fullEndpoint = $baseEndpoint . '/' . $endpoint;
            if (substr($_SERVER['REQUEST_URI'], 0, strlen($fullEndpoint)) === $fullEndpoint) {
                $isSAMLSSOEndpoint = true;
                break;
            }
        }
        return $isSAMLSSOEndpoint;
    }
}
