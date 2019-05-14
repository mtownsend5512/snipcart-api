<?php

namespace Mtownsend\SnipcartApi;

use Exception;
use GuzzleHttp\Client as Guzzle;

/**
 * A class to simplify api calls to Snipcart
 *
 * @package Mtownsend\SnipcartApi
 * @author Mark Townsend <mtownsend5512@gmail.com
 */
class SnipcartApi
{
    /**
     * The Snipcart api key
     *
     * @var string
     */
    public $apiKey;

    /**
     * The base url for the request
     * May or may not include the version number
     *
     * @var string
     */
    public $baseUrl;

    /**
     * The endpoint to be attached to the base url for the request
     *
     * @var string
     */
    public $endpoint = '';

    /**
     * The payload/body of the request
     *
     * @var array
     */
    public $payload = [];

    /**
     * The options that should be passed to the Guzzle instance
     * May include headers, authentication, and more
     *
     * @var array
     */
    public $requestOptions;

    /**
     * The http request method. E.g. GET, POST, PUT, PATCH, DELETE
     *
     * @var string
     */
    public $httpMethod = 'GET';

    /**
     * The response http code
     *
     * @var integer
     */
    protected $responseHttpCode = 0;

    /**
     * The type of post encoding the request will be
     * 'body' or 'form_params'
     *
     * @var string
     */
    public $postEncoding = 'body';

    /**
     * The returned server response
     *
     * @var string
     */
    public $response = '';

    /**
     * Set up the Snipcart Api class to begin making api calls
     *
     * @param string $apiKey Snipcart api key
     * @param array $options Guzzle related options
     */
    public function __construct(string $apiKey = '', array $options = [])
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = 'https://app.snipcart.com/api';

        $this->request = new Guzzle();
        $this->requestOptions = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => "Mtownsend/SnipcartApi (github.com/mtownsend5512/snipcart-api)"
            ],
            'auth' => [
                $this->apiKey,
                null
            ],
            'http_errors' => false,
            'decode_content' => false
        ];

        $this->requestOptions($options);
    }

    /**
     * Add a key value header
     *
     * @param string $key The header array key
     * @param mixed $value The header value
     */
    public function addHeader(string $key, $value)
    {
        if ($key && $value) {
            $this->requestOptions['headers'][$key] = $value;
        }

        return $this;
    }

    /**
     * Add multiple key/value headers
     *
     * @param array $array
     */
    public function addHeaders(array $array)
    {
        foreach ($array as $key => $value) {
            $this->addHeader($key, $value);
        }

        return $this;
    }

    /**
     * Set the Snipcart api key
     *
     * @return string Snipcart api key
     */
    public function apiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->requestOptions([
            'auth' => [
                $this->apiKey,
                null
            ]
        ]);

        return $this;
    }

    /**
     * Set the http method to GET
     *
     * @return \Mtownsend\SnipcartApi\SnipcartApi
     */
    public function get()
    {
        $this->httpMethod = 'GET';

        return $this;
    }

    /**
     * Set the http method to POST
     *
     * @return \Mtownsend\SnipcartApi\SnipcartApi
     */
    public function post()
    {
        $this->httpMethod = 'POST';

        return $this;
    }

    /**
     * Set the http method to PUT
     *
     * @return \Mtownsend\SnipcartApi\SnipcartApi
     */
    public function put()
    {
        $this->httpMethod = 'PUT';

        return $this;
    }

    /**
     * Set the http method to PATCH
     *
     * @return \Mtownsend\SnipcartApi\SnipcartApi
     */
    public function patch()
    {
        $this->httpMethod = 'PATCH';

        return $this;
    }

    /**
     * Set the http method to DELETE
     *
     * @return \Mtownsend\SnipcartApi\SnipcartApi
     */
    public function delete()
    {
        $this->httpMethod = 'DELETE';

        return $this;
    }

    /**
     * The url endpoint to be added onto the base url
     *
     * @return \Mtownsend\SnipcartApi\SnipcartApi
     */
    public function to($endpoint = '')
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * A proxy for the 'to' method
     *
     * @return \Mtownsend\SnipcartApi\SnipcartApi
     */
    public function from($endpoint = '')
    {
        $this->to($endpoint);

        return $this;
    }

    /**
     * Set the api call's post encoding format
     *
     * @param  string $encoding body|form_params
     * @return \Mtownsend\SnipcartApi\SnipcartApi
     */
    public function postEncoding(string $encoding = 'body')
    {
        $this->postEncoding = $encoding;

        return $this;
    }

    /**
     * The payload for the request
     *
     * @param  mixed string|array $payload The data to be passed along in the body or query string of the request
     * @param  mixed string|array $value When the payload is passed as a string, it expects the payload to act as the key
     * @return \Mtownsend\SnipcartApi\SnipcartApi
     */
    public function payload($payload = null, $value = null)
    {
        if ($value && is_string($payload) && $this->httpMethod == 'GET') {
            $payload = [$payload => $value];
        }

        if ($payload) {
            $this->payload = $this->argForcedToArray($payload);
        }

        return $this;
    }

    /**
     * A raw payload that does not need to undergo any parsing or transformations
     *
     * @param string $payload The raw query string or json payload
     * @return \Mtownsend\SnipcartApi\SnipcartApi
     */
    public function rawPayload(string $payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * Set Guzzle's request options
     *
     * @param  array  $options
     * @return \Mtownsend\SnipcartApi\SnipcartApi
     */
    public function requestOptions(array $options = [])
    {
        $this->requestOptions = array_merge($this->requestOptions, $options);

        return $this;
    }

    /**
     * Return the fully qualified url for the request
     *
     * @return string
     */
    public function getFullRequestUrl(): string
    {
        if ($this->httpMethod == 'GET') {
            $this->endpoint = str_replace('/?', '?', $this->strStart(rtrim($this->endpoint, '/'), '/') . $this->payload);
        }

        if (empty($this->endpoint)) {
            return rtrim($this->baseUrl, '/');
        }

        return rtrim($this->baseUrl, '/') . '/' . ltrim($this->endpoint, '/');
    }

    /**
     * Send the actual API request/call and retrieve the response
     *
     * @return string
     */
    public function send()
    {
        // Format the payload unless the payload is raw
        if (!is_string($this->payload)) {
            $this->formatPayload();
        }

        // Send the request
        $request = new Guzzle(['base_uri' => $this->baseUrl]);
        $response = $request->request($this->httpMethod, $this->getFullRequestUrl(), array_merge($this->requestOptions, [$this->postEncoding => $this->httpMethod == 'GET' ? '' : $this->payload]));

        // Get the response http code
        $this->responseHttpCode = $response->getStatusCode();

        // Parse response
        $this->response = json_decode((string) $response->getBody());

        return $this->response;
    }

    /**
     * Get the api call's http response code
     *
     * @return int
     */
    public function responseCode(): int
    {
        return (int) $this->responseHttpCode;
    }

    /**
     * Return the status of the api call
     *
     * @return bool
     */
    public function successful(): bool
    {
        $responseCode = (string) $this->responseCode();
        return ($responseCode[0] === '2') ? true : false;
    }

    /**
     * Format the payload by determining the HTTP verb and/or the specified format (query string/json)
     *
     * @return void
     */
    protected function formatPayload(): void
    {
        if ($this->httpMethod == 'GET') {
            $this->payload = $this->transformArrayToQueryString($this->payload);
            return;
        } else {
            $this->payload = $this->transformArrayToJson($this->payload);
            return;
        }
    }

    /**
     * Transform an array into a json encoded string
     *
     * @param array $array The payload for a POST, PUT, or PATCH request
     * @return string
     */
    protected function transformArrayToJson(array $array): string
    {
        return json_encode($array);
    }

    /**
     * Transform an array into a query string
     *
     * @param array $array The payload for a GET request
     * @return string
     */
    protected function transformArrayToQueryString(array $array): string
    {
        $payload = $this->arrayToQueryString($array);

        return $payload;
    }

    /**
     * Clean an endpoint of prepended and trailing slashes
     *
     * @return string
     */
    protected function cleanEndpoint($endpoint): string
    {
        return ltrim(rtrim($endpoint, '/'), '/');
    }

    /**
     * Force an argument to be cast as an array
     *
     * @param  mixed $arg
     * @return array
     */
    protected function argForcedToArray($arg): array
    {
        if (is_array($arg)) {
            return $arg;
        }

        if (is_object($arg) && method_exists($arg, 'toArray')) {
            return $arg->toArray();
        }

        return [$arg];
    }

    /**
     * Convert an array to a valid query string
     *
     * @param  array $array
     * @return string
     */
    protected function arrayToQueryString(array $array): string
    {
        $queryString = http_build_query($array);

        if (empty($queryString)) {
            return '';
        }

        return '?' . $queryString;
    }

    /**
     * Determine if an array is associative
     *
     * @param  array @array
     * @return boolean
     */
    protected function isAssocArray(array $array): bool
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    /**
     * Begin a string with a single instance of a given value
     *
     * @param  string $value
     * @param  string $prefix
     * @return string
     */
    protected function strStart($value, $prefix): string
    {
        $quoted = preg_quote($prefix, '/');

        return $prefix . preg_replace('/^(?:' . $quoted . ')+/u', '', $value);
    }
}
