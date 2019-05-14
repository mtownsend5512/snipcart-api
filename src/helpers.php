<?php

if (!function_exists('snipcart')) {
    /**
     * Create a new instance of the Mtownsend\SnipcartApi\SnipcartApi class
     *
     * @return Mtownsend\SnipcartApi\SnipcartApi
     */
    function snipcart(string $apiKey = '', array $options = [])
    {
        if (empty($apiKey) && function_exists('config')) {
            $apiKey = config('snipcart.api_key');
        }
        return new Mtownsend\SnipcartApi\SnipcartApi($apiKey, $options);
    }
}
