<?php

/**
 * Written by Netsense s.r.l 2016
 */

namespace OAuth2;

class OAuth2 {

    /**
     * Different AUTH method
     */
    const AUTH_TYPE_URI = 0;
    const AUTH_TYPE_AUTHORIZATION_BASIC = 1;
    const AUTH_TYPE_FORM = 2;

    /**
     * Different Access token type
     */
    const ACCESS_TOKEN_URI = 0;
    const ACCESS_TOKEN_BEARER = 1;
    const ACCESS_TOKEN_OAUTH = 2;
    const ACCESS_TOKEN_MAC = 3;

    /**
     * Different Grant types
     */
    const GRANT_TYPE_AUTH_CODE = 'authorization_code';
    const GRANT_TYPE_PASSWORD = 'password';
    const GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';
    const GRANT_TYPE_REFRESH_TOKEN = 'refresh_token';

    /**
     * HTTP Methods
     */
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_DELETE = 'DELETE';
    const HTTP_METHOD_HEAD = 'HEAD';
    const HTTP_METHOD_PATCH = 'PATCH';

    /**
     * HTTP Form content types
     */
    const HTTP_FORM_CONTENT_TYPE_APPLICATION = 0;
    const HTTP_FORM_CONTENT_TYPE_MULTIPART = 1;

    /**
     * Client ID
     *
     * @var string
     */
    protected $client_id = null;

    /**
     * Client Secret
     *
     * @var string
     */
    protected $client_secret = null;

    /**
     * Client Authentication method
     *
     * @var int
     */
    protected $client_auth = self::AUTH_TYPE_URI;

    /**
     * Access Token
     *
     * @var string
     */
    protected $access_token = null;

    /**
     * Access Token Type
     *
     * @var int
     */
    protected $access_token_type = self::ACCESS_TOKEN_URI;

    /**
     * Access Token Secret
     *
     * @var string
     */
    protected $access_token_secret = null;

    /**
     * Access Token crypt algorithm
     *
     * @var string
     */
    protected $access_token_algorithm = null;

    /**
     * Access Token Parameter name
     *
     * @var string
     */
    protected $access_token_param_name = 'access_token';

    /**
     * The path to the certificate file to use for https connections
     *
     * @var string  Defaults to .
     */
    protected $certificate_file = null;

    /**
     * cURL options
     *
     * @var array
     */
    protected $curl_options = array();
    private static $instance;

    /**
     * Construct
     *
     * @param string $client_id Client ID
     * @param string $client_secret Client Secret
     * @param int    $client_auth (AUTH_TYPE_URI, AUTH_TYPE_AUTHORIZATION_BASIC, AUTH_TYPE_FORM)
     * @param string $certificate_file Indicates if we want to use a certificate file to trust the server. Optional, defaults to null.
     * @return void
     */
    function __construct($client_id, $client_secret, $client_auth = self::AUTH_TYPE_AUTHORIZATION_BASIC, $certificate_file = null) {
        if (!extension_loaded('curl')) {
            throw new Exception('The PHP exention curl must be installed to use this library.', Exception::CURL_NOT_FOUND);
        }

        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->client_auth = $client_auth;
        $this->certificate_file = $certificate_file;
        if (!empty($this->certificate_file) && !is_file($this->certificate_file)) {
            throw new InvalidArgumentException('The certificate file was not found', InvalidArgumentException::CERTIFICATE_NOT_FOUND);
        }
    }

    static function getApiOauth2Accessor() {
        return OAuth2::$instance;
    }

    static function initApiOauth2Accessor($client_id, $client_secret, $client_auth = self::AUTH_TYPE_AUTHORIZATION_BASIC, $certificate_file = null) {

        OAuth2::$instance = new OAuth2($client_id, $client_secret, $client_auth, $certificate_file);
    }

    /**
     * Get the client Id
     *
     * @return string Client ID
     */
    public function getClientId() {
        return $this->client_id;
    }

    /**
     * Get the client Secret
     *
     * @return string Client Secret
     */
    public function getClientSecret() {
        return $this->client_secret;
    }

    /**
     * getAuthenticationUrl
     *
     * @param string $auth_endpoint Url of the authentication endpoint
     * @param string $redirect_uri  Redirection URI
     * @param array  $extra_parameters  Array of extra parameters like scope or state (Ex: array('scope' => null, 'state' => ''))
     * @return string URL used for authentication
     */
    public function getAuthenticationUrl($auth_endpoint, $redirect_uri, array $extra_parameters = array()) {
        $parameters = array_merge(array(
            'response_type' => 'code',
            'client_id' => $this->client_id,
            'redirect_uri' => $redirect_uri
                ), $extra_parameters);
        return $auth_endpoint . '?' . http_build_query($parameters, null, '&');
    }

    /**
     * getAccessToken
     *
     * @param string $token_endpoint    Url of the token endpoint
     * @param int    $grant_type        Grant Type ('authorization_code', 'password', 'client_credentials', 'refresh_token', or a custom code (@see GrantType Classes)
     * @param array  $parameters        Array sent to the server (depend on which grant type you're using)
     * @param array  $extra_headers     Array of extra headers
     * @return array Array of parameters required by the grant_type (CF SPEC)
     */
    function getAccessToken($token_endpoint, $grant_type, array $parameters = array(), array $extra_headers = array()) {

        if (!$grant_type) {
            throw new InvalidArgumentException('The grant_type is mandatory.', InvalidArgumentException::INVALID_GRANT_TYPE);
        }
        $grantTypeClassName = $this->convertToCamelCase($grant_type);
        $grantTypeClass = __NAMESPACE__ . '\\GrantType\\' . $grantTypeClassName;
        if (!class_exists($grantTypeClass)) {
            throw new InvalidArgumentException('Unknown grant type \'' . $grant_type . '\'', InvalidArgumentException::INVALID_GRANT_TYPE);
        }

        $grantTypeObject = new $grantTypeClass();

        $grantTypeObject->validateParameters($parameters);

        if (!defined($grantTypeClass . '::GRANT_TYPE')) {
            throw new Exception('Unknown constant GRANT_TYPE for class ' . $grantTypeClassName, Exception::GRANT_TYPE_ERROR);
        }
        $parameters['grant_type'] = $grantTypeClass::GRANT_TYPE;
        $http_headers = $extra_headers;
        switch ($this->client_auth) {
            case self::AUTH_TYPE_URI:
            case self::AUTH_TYPE_FORM:

                $parameters['client_id'] = $this->client_id;
                $parameters['client_secret'] = $this->client_secret;
//                $http_headers['Authorization'] = 'Basic ' . base64_encode($this->client_id .  ':' . $this->client_secret);
//                $parameters['username'] = $username;
//                $parameters['password'] = $password;
                break;
            case self::AUTH_TYPE_AUTHORIZATION_BASIC:
                $parameters['client_id'] = $this->client_id;
                $http_headers['Authorization'] = 'Basic ' . base64_encode($this->client_id . ':' . $this->client_secret);
                break;
            default:
                throw new Exception('Unknown client auth type.', Exception::INVALID_CLIENT_AUTHENTICATION_TYPE);
                break;
        }

        $http_headers['Accept'] = 'application/json';
        $http_headers['Content-Type'] = 'application/x-www-form-urlencoded';

        return $this->executeRequest($token_endpoint, $parameters, self::HTTP_METHOD_POST, $http_headers, self::HTTP_FORM_CONTENT_TYPE_APPLICATION, $parameters['grant_type']);
    }

    /**
     * setToken
     *
     * @param string $token Set the access token
     * @return void
     */
    public function setAccessToken($token) {
        $this->access_token = $token;
    }

    /**
     * Check if there is an access token present
     *
     * @return bool Whether the access token is present
     */
    public function hasAccessToken() {
        return !!$this->access_token;
    }

    /**
     * Set the client authentication type
     *
     * @param string $client_auth (AUTH_TYPE_URI, AUTH_TYPE_AUTHORIZATION_BASIC, AUTH_TYPE_FORM)
     * @return void
     */
    public function setClientAuthType($client_auth) {
        $this->client_auth = $client_auth;
    }

    /**
     * Set an option for the curl transfer
     *
     * @param int   $option The CURLOPT_XXX option to set
     * @param mixed $value  The value to be set on option
     * @return void
     */
    public function setCurlOption($option, $value) {
        $this->curl_options[$option] = $value;
    }

    /**
     * Set multiple options for a cURL transfer
     *
     * @param array $options An array specifying which options to set and their values
     * @return void
     */
    public function setCurlOptions($options) {
        $this->curl_options = array_merge($this->curl_options, $options);
    }

    /**
     * Set the access token type
     *
     * @param int $type Access token type (ACCESS_TOKEN_BEARER, ACCESS_TOKEN_MAC, ACCESS_TOKEN_URI)
     * @param string $secret The secret key used to encrypt the MAC header
     * @param string $algorithm Algorithm used to encrypt the signature
     * @return void
     */
    public function setAccessTokenType($type, $secret = null, $algorithm = null) {
        $this->access_token_type = $type;
        $this->access_token_secret = $secret;
        $this->access_token_algorithm = $algorithm;
    }

    /**
     * Fetch a protected ressource
     *
     * @param string $protected_ressource_url Protected resource URL
     * @param array  $parameters Array of parameters
     * @param string $http_method HTTP Method to use (POST, PUT, GET, HEAD, DELETE)
     * @param array  $http_headers HTTP headers
     * @param int    $form_content_type HTTP form content type to use
     * @return array
     */
    public function fetch($protected_resource_url, $parameters = array(), $http_method = self::HTTP_METHOD_GET, array $http_headers = array(), $form_content_type = self::HTTP_FORM_CONTENT_TYPE_MULTIPART) {
        if ($this->access_token) {
            switch ($this->access_token_type) {
                case self::ACCESS_TOKEN_URI:
                    if (is_array($parameters)) {
                        $parameters[$this->access_token_param_name] = $this->access_token;
                    } else {
                        throw new InvalidArgumentException(
                        'You need to give parameters as array if you want to give the token within the URI.', InvalidArgumentException::REQUIRE_PARAMS_AS_ARRAY
                        );
                    }
                    break;
                case self::ACCESS_TOKEN_BEARER:
                    $http_headers['Authorization'] = 'Bearer ' . $this->access_token;
                    break;
                case self::ACCESS_TOKEN_OAUTH:
                    $http_headers['Authorization'] = 'OAuth ' . $this->access_token;
                    break;
                case self::ACCESS_TOKEN_MAC:
                    $http_headers['Authorization'] = 'MAC ' . $this->generateMACSignature($protected_resource_url, $parameters, $http_method);
                    break;
                default:
                    throw new Exception('Unknown access token type.', Exception::INVALID_ACCESS_TOKEN_TYPE);
                    break;
            }
        }
        return $this->executeRequest($protected_resource_url, $parameters, $http_method, $http_headers, $form_content_type);
    }

    /**
     * Generate the MAC signature
     *
     * @param string $url Called URL
     * @param array  $parameters Parameters
     * @param string $http_method Http Method
     * @return string
     */
    private function generateMACSignature($url, $parameters, $http_method) {
        $timestamp = time();
        $nonce = uniqid();
        $parsed_url = parse_url($url);
        if (!isset($parsed_url['port'])) {
            $parsed_url['port'] = ($parsed_url['scheme'] == 'https') ? 443 : 80;
        }
        if ($http_method == self::HTTP_METHOD_GET) {
            if (is_array($parameters)) {
                $parsed_url['path'] .= '?' . http_build_query($parameters, null, '&');
            } elseif ($parameters) {
                $parsed_url['path'] .= '?' . $parameters;
            }
        }

        $signature = base64_encode(hash_hmac($this->access_token_algorithm, $timestamp . "\n"
                        . $nonce . "\n"
                        . $http_method . "\n"
                        . $parsed_url['path'] . "\n"
                        . $parsed_url['host'] . "\n"
                        . $parsed_url['port'] . "\n\n"
                        , $this->access_token_secret, true));

        return 'id="' . $this->access_token . '", ts="' . $timestamp . '", nonce="' . $nonce . '", mac="' . $signature . '"';
    }

    /**
     * Execute a request (with curl)
     *
     * @param string $url URL
     * @param mixed  $parameters Array of parameters
     * @param string $http_method HTTP Method
     * @param array  $http_headers HTTP Headers
     * @param int    $form_content_type HTTP form content type to use
     * @return array
     */
    private function executeRequest($url, $parameters = array(), $http_method = self::HTTP_METHOD_GET, array $http_headers = null, $form_content_type = self::HTTP_FORM_CONTENT_TYPE_MULTIPART, $grant_type) {
        $type = $grant_type;
        $curl_options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CUSTOMREQUEST => $http_method
        );

        switch ($http_method) {
            case self::HTTP_METHOD_POST:
                $curl_options[CURLOPT_POST] = true;
            /* No break */
            case self::HTTP_METHOD_PUT:
            case self::HTTP_METHOD_PATCH:

                /**
                 * Passing an array to CURLOPT_POSTFIELDS will encode the data as multipart/form-data,
                 * while passing a URL-encoded string will encode the data as application/x-www-form-urlencoded.
                 * http://php.net/manual/en/function.curl-setopt.php
                 */
                if (is_array($parameters) && self::HTTP_FORM_CONTENT_TYPE_APPLICATION === $form_content_type) {
                    $parameters = http_build_query($parameters, null, '&');
                }
                $curl_options[CURLOPT_POSTFIELDS] = $parameters;
                break;
            case self::HTTP_METHOD_HEAD:
                $curl_options[CURLOPT_NOBODY] = true;
            /* No break */
            case self::HTTP_METHOD_DELETE:
            case self::HTTP_METHOD_GET:
                if (is_array($parameters) && count($parameters) > 0) {
                    $url .= '?' . http_build_query($parameters, null, '&');
                } elseif ($parameters) {
                    $url .= '?' . $parameters;
                }
                break;
            default:
                break;
        }

        $curl_options[CURLOPT_URL] = $url;

        if (is_array($http_headers)) {
            $header = array();
            foreach ($http_headers as $key => $parsed_urlvalue) {
                $header[] = "$key: $parsed_urlvalue";
            }
            $curl_options[CURLOPT_HTTPHEADER] = $header;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $curl_options);
        // https handling
        if (!empty($this->certificate_file)) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_CAINFO, $this->certificate_file);
        } else {
            // bypass ssl verification
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }
        if (!empty($this->curl_options)) {
            curl_setopt_array($ch, $this->curl_options);
        }
        $result = curl_exec($ch);
//        var_dump($result);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        if ($curl_error = curl_error($ch)) {
            throw new Exception($curl_error, Exception::CURL_ERROR);
            curl_close($ch);
        } else {
            $json_decode = json_decode($result, true);
        }
        curl_close($ch);

        if ($type == "password" || $type == "refresh_token") {
            if (isset($json_decode['access_token'])) {
                update_option('wp_access_token', $json_decode['access_token']);
            }
            if (isset($json_decode['refresh_token'])) {
                update_option('wp_refresh_token', $json_decode['refresh_token']);
            }
        }

        if ($type == "client_credentials") {
            if (isset($json_decode['access_token'])) {
                update_option('wp_public_access_token', $json_decode['access_token']);
            }
        }

        $json_decode = $this->resultGetAccessToken($json_decode);
//        update_option('wp_access_token', $json_decode['access_token']);
//        update_option('wp_refresh_token', $json_decode['refresh_token']);
//          var_dump("Ci SONOOOOOOOO", get_option('wp_access_token'));
        return $json_decode['access_token'];
    }

    public function resultGetAccessToken($json_decode) {
        
        if (isset($json_decode['error'])) {

            if ($json_decode['error'] == "invalid_grant" && $json_decode['error_description'] == "Bad credentials") {


                update_option('wp_registration', 'FALSE');
                update_option('wp_userwimtv', 'username');
                update_option('wp_passwimtv', 'password');
                add_submenu_page($registrationHidden, __('REGISTER_menuLink', "wimtvpro"), __('REGISTER_menuLink', "wimtvpro"), 'edit_others_posts', __('REGISTER_urlLink', "wimtvpro"), 'wimtvpro_registration');
                return;
            } else
            if ($json_decode['error'] == "invalid_grant") {

                $apiPublic = getApiOPublic();
                $parameters = array();
                $parameters['username'] = get_option('wp_userwimtv');
                $parameters['password'] = get_option('wp_passwimtv');

                return $json_decode = $apiPublic->getToken($parameters, "password");
            } else if ($json_decode['error'] == "invalid_token") {

                $apiPublic = getApiOPublic();
                $parameters = array();

                $parameters['refresh_token'] = get_option('wp_refresh_token');
                $access_token = $apiPublic->getToken($parameters, "refresh_token");
//                update_option('wp_access_token', $access_token);


                return true;
            } else if ($json_decode['error'] == "access_denied") {
                $apiPublic = getApiOPublic();
                $parameters = array();


                $parameters['username'] = get_option('wp_userwimtv');
                $parameters['password'] = get_option('wp_passwimtv');
//            var_dump($apiPublic->getToken($parameters, "password"));exit;
                return $json_decode = $apiPublic->getToken($parameters, "password");
            }
        }
    }

    /**
     * Set the name of the parameter that carry the access token
     *
     * @param string $name Token parameter name
     * @return void
     */
    public function setAccessTokenParamName($name) {
        $this->access_token_param_name = $name;
    }

    /**
     * Converts the class name to camel case
     *
     * @param  mixed  $grant_type  the grant type
     * @return string
     */
    private function convertToCamelCase($grant_type) {
        $parts = explode('_', $grant_type);
        array_walk($parts, function(&$item) {
            $item = ucfirst($item);
        });
        return implode('', $parts);
    }

}

class Exception extends \Exception {

    const CURL_NOT_FOUND = 0x01;
    const CURL_ERROR = 0x02;
    const GRANT_TYPE_ERROR = 0x03;
    const INVALID_CLIENT_AUTHENTICATION_TYPE = 0x04;
    const INVALID_ACCESS_TOKEN_TYPE = 0x05;

}

class InvalidArgumentException extends \InvalidArgumentException {

    const INVALID_GRANT_TYPE = 0x01;
    const CERTIFICATE_NOT_FOUND = 0x02;
    const REQUIRE_PARAMS_AS_ARRAY = 0x03;
    const MISSING_PARAMETER = 0x04;

}
