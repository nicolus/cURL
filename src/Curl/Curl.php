<?php
namespace PVT\HTTP;

use \Exception;

class Curl
{

    const UA_CHROME  = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36';
    const UA_FIREFOX = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0';

    var $ch;
    var $url = "";
    var $returnTransfer = true;
    var $followLocation = true;
    var $header = false;
    var $userName = "";
    var $password = "";
    var $post = false;
    var $postFields = [];
    var $sslVerifier = false;
    var $HttpAuth = false;
    var $timeout = -1;
    var $headers = [];
    var $proxy = null;
    var $proxyUser = null;
    var $proxyPassword = null;
    var $userAgent = self::UA_CHROME;

    var $info;


    public function __construct() {
        $this->ch = curl_init();
    }

    public function __destruct() {
        curl_close($this->ch);
    }


    /**
     * Get info (as returned by curl_info()) for the last request.
     * @return mixed
     */
    public function getInfo() {
        return $this->info;
    }

    /**
     * Set the URL to request
     * @param string $url
     * @return self
     */
    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    /**
     * Return the web page (true by default)
     * @param boolean $returnTransfer
     * @return self
     */
    public function setReturnTransfer($returnTransfer) {
        $this->returnTransfer = $returnTransfer;
        return $this;
    }

    /**
     * Follow redirects (true by default)
     * @param boolean $followLocation
     * @return self
     */
    public function setFollowLocation($followLocation) {
        $this->followLocation = $followLocation;
        return $this;
    }

    /**
     * Return the headers
     * @param boolean $header
     * @return self
     */
    public function setHeader($header) {
        $this->header = $header;
        return $this;
    }

    /**
     * Set all headers as an array of strings
     * @param array $headers
     * @return self
     */
    public function setHeaders($headers) {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Add one or more headers as a string or an array of strings
     * @param array|string $headers
     * @return self
     */
    public function addHeaders($headers) {
        foreach ((array)$headers as $header) {
            $this->headers[] = $header;
        }
        return $this;
    }

    /**
     * Set the username and password if page requires authentication
     * @param string $userName
     * @param string $password
     * @return self
     */
    public function setAuth($userName, $password) {
        $this->userName = $userName;
        $this->password = $password;
        $this->HttpAuth = true;
        return $this;
    }

    /**
     * Set the proxy to use for requests
     * @param string $host host with port number (eg. "uk.proxymesh.com:31280")
     * @param string $user
     * @param string $password
     * @return self
     */
    public function setProxy($host, $user, $password) {
        $this->proxy         = $host;
        $this->proxyUser     = $user;
        $this->proxyPassword = $password;
        return $this;
    }

    /**
     * Set method to POST and send $postfields
     * @param array $postFields
     * @return self
     */
    public function setPost($postFields) {
        $this->post       = true;
        $this->postFields = $postFields;
        return $this;
    }

    /**
     *
     * @param boolean $sslVerifier
     * @return self
     */
    public function setSslVerifier($sslVerifier) {
        $this->sslVerifier = $sslVerifier;
        return $this;
    }


    /**
     * Set the timeout (-1 no timeout by default)
     * @param int $timeout
     * @return self
     */
    public function setTimeout($timeout) {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @param string $userAgent
     * @return self
     */
    public function setUserAgent($userAgent) {
        $this->userAgent = $userAgent;
        return $this;
    }


    /**
     * @return mixed
     * @throws Exception
     */
    public function request() {

        curl_setopt_array($this->ch, [
            CURLOPT_URL            => $this->url,
            CURLOPT_RETURNTRANSFER => $this->returnTransfer,
            CURLOPT_HEADER         => $this->header,
            CURLOPT_USERAGENT      => $this->userAgent,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_FOLLOWLOCATION => $this->followLocation,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST           => $this->post,
            CURLOPT_POSTFIELDS     => $this->postFields,
            CURLOPT_HTTPGET        => !$this->post,
            CURLOPT_HTTPAUTH       => $this->HttpAuth,
            CURLOPT_HTTPHEADER     => $this->headers,
        ]);

        if ($this->proxy) {
            curl_setopt_array($this->ch, [
                CURLOPT_PROXY        => $this->proxy,
                CURLOPT_PROXYUSERPWD => "$this->proxyUser:$this->proxyPassword"
            ]);
        }

        $result     = curl_exec($this->ch);
        $this->info = curl_getinfo($this->ch);
        $error      = curl_error($this->ch);

        if ($error !== "") {
            throw new Exception($error, curl_errno($this->ch));
        }

        return $result;
    }


    public static function get($url) {
        return (new self)
            ->setUrl($url)
            ->request();
    }

    public static function post($url, $postfields = null) {
        return (new self)
            ->setUrl($url)
            ->setPost($postfields)
            ->request();
    }

}