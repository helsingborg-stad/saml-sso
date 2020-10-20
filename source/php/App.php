<?php

namespace SAMLSSO;

use SAMLSSO\Settings;

class App
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // Check if settings is fully there before adding any endpoints.
        $settings = new Settings();
        if ($settings->isSettingsSatisfied) {
            new Endpoints();
        }
    }
}
