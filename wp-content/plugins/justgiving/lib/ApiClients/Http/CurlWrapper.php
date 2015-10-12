<?php
class CurlWrapper
{
    protected $ch = null;
    protected $cookieFile = '';
    protected $cookies = array();
    protected $headers = array();
    protected $options = array();
    protected static $predefinedUserAgents = array(
        // IE 9.0
        'ie'       => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
        // Firefox 6
        'firefox'  => 'Mozilla/5.0 (Windows NT 6.1; rv:6.0) Gecko/20110814 Firefox/6.0',
        // Opera 12
        'opera'    => 'Opera/9.80 (Windows NT 6.1; U; en-US) Presto/2.9.181 Version/12.00',
        // Chrome 15
        'chrome'   => 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.872.0 Safari/535.2',
        // Google Bot
        'bot'      => 'Googlebot/2.1 (+http://www.google.com/bot.html)',
    );
    /**
     * @var array GET/POST params to send
     */
    protected $requestParams = array();
    /**
     * @var string cURL response data
     */
    protected $response = '';
    /**
     * @var array cURL transfer info
     */
    protected $transferInfo = array();    
    public function __construct()
    {
        if (!extension_loaded('curl')) {
            throw new CurlWrapperException('cURL extension is not loaded.');
        }
        $this->ch = curl_init();
        if (!$this->ch) {
            throw new CurlWrapperException($this->ch);
        }    
    }
    
    public function __destruct()
    {
        if (is_resource($this->ch)) {
            curl_close($this->ch);
        }
        $this->ch = null;
    }  

    public function addCookie($name, $value = null)
    {
        if (is_array($name)) {
            $this->cookies = $name + $this->cookies;
        } else {
            $this->cookies[$name] = $value;
        }
    }

    /**
     * Adds a header for a cURL transfer
     *
     * Examples:
     * $curl->addHeader('Accept-Charset', 'windows-1251,utf-8;q=0.7,*;q=0.7');
     * $curl->addHeader('Pragma', '');
     * $curl->addHeader(array('Accept-Charset'=>'windows-1251,utf-8;q=0.7,*;q=0.7', 'Pragma'=>''));
     *
     * @param string|array $header Header or array of headers (header=>value)
     * @param string $value Value of header
     */
    public function addHeader($header, $value = null)
    {
        if (is_array($header)) {
            $this->headers = $header + $this->headers;
        } else {
            $this->headers[$header] = $value;
        }
    }

    /**
     * Adds an option for a cURL transfer (@see http://php.net/manual/en/function.curl-setopt.php)
     * @param integer|array $option CURLOPT_XXX predefined constant or array of constants (constant=>value)
     * @param mixed $value Value of option
     */
    public function addOption($option, $value = null)
    {
        if (is_array($option)) {
            $this->options = $option + $this->options;
        } else {
            $this->options[$option] = $value;
        }
    }

    public function addRequestParam($name, $value = null)
    {
        if (is_array($name)) {
            $this->requestParams = $name + $this->requestParams;
        } elseif (is_string($name) && $value === null) {
            parse_str($name, $params);
            if (!empty($params)) {
                $this->requestParams = $params + $this->requestParams;
            }
        } else {
            $this->requestParams[$name] = $value;
        }
    }

    /**
     * Clears the cookies file
     */
    public function clearCookieFile()
    {
        if (trim($this->cookieFile) !== ''){
            if (!is_writable($this->cookieFile)) {
                throw new CurlWrapperException('Cookie file "'.($this->cookieFile).'" is not writable or does\'n exists!');
            }
            file_put_contents($this->cookieFile, '', LOCK_EX);
        }
    }

    /**
     * Clears the cookies
     */
    public function clearCookies()
    {
        $this->cookies = array();
    }

    /**
     * Clears the headers
     */
    public function clearHeaders()
    {
        $this->headers = array();
    }

    /**
     * Clears the options
     */
    public function clearOptions()
    {
        $this->options = array();
    }

    /**
     * Clears the request parameters
     */
    public function clearRequestParams()
    {
        $this->requestParams = array();
    }

    /**
     * Makes the 'DELETE' request to the $url with an optional $requestParams
     * @param string $url
     * @param array $requestParams
     * @return string
     */
    public function delete($url, $requestParams = null)
    {
        return $this->request($url, 'DELETE', $requestParams);
    }

    /**
     * Makes the 'GET' request to the $url with an optional $requestParams
     * @param string $url
     * @param array $requestParams
     * @return string
     */
    public function get($url, $requestParams = null)
    {
        return $this->request($url, 'GET', $requestParams);
    }   

    public function getResponse()
    {
        return $this->response;
    }
    
    /**
     * Gets the information about the last transfer
     * @param string $key @see http://php.net/manual/en/function.curl-getinfo.php
     * keys are:
     * -- 'url'                      - Last effective URL
     * -- 'content_type'             - Content-Type: of downloaded object, NULL indicates server did not send valid Content-Type: header
     * -- 'http_code'                - Last received HTTP code
     * -- 'header_size'              - Total size of all headers received
     * -- 'request_size'             - Total size of issued requests, currently only for HTTP requests
     * -- 'filetime'                 - Remote time of the retrieved document, if -1 is returned the time of the document is unknown
     * -- 'ssl_verify_result'        - Result of SSL certification verification requested by setting CURLOPT_SSL_VERIFYPEER
     * -- 'redirect_count'           - Number of redirects it went through if CURLOPT_FOLLOWLOCATION was set
     * -- 'total_time'               - Total transaction time in seconds for last transfer
     * -- 'namelookup_time'          - Time in seconds until name resolving was complete
     * -- 'connect_time'             - Time in seconds it took to establish the connection
     * -- 'pretransfer_time'         - Time in seconds from start until just before file transfer begins
     * -- 'size_upload'              - Total number of bytes uploaded
     * -- 'size_download'            - Total number of bytes downloaded
     * -- 'speed_download'           - Average download speed
     * -- 'speed_upload'             - Average upload speed
     * -- 'download_content_length'  - content-length of download, read from Content-Length:  field
     * -- 'upload_content_length'    - Specified size of upload
     * -- 'starttransfer_time'       - Time in seconds until the first byte is about to be transferred
     * -- 'redirect_time'            - Time in seconds of all redirection steps before final transaction was started
     * @return array|string
     */
    public function getTransferInfo($key = null)
    {
        if (empty($this->transferInfo)) {
            throw new CurlWrapperException('There is no transfer info. Did you do the request?');
        }
        if ($key === null) {
            return $this->transferInfo;
        }
        if (isset($this->transferInfo[$key])) {
            return $this->transferInfo[$key];
        }
        throw new CurlWrapperException('There is no such key: '.$key);
    }

    /**
     * Makes the 'HEAD' request to the $url with an optional $requestParams
     * @param string $url
     * @param array $requestParams
     * @return string
     */
    public function head($url, $requestParams = null)
    {
        return $this->request($url, 'HEAD', $requestParams);
    }

    /**
     * Makes the 'POST' request to the $url with an optional $requestParams
     * @param string $url
     * @param array $requestParams
     * @return string
     */
    public function post($url, $requestParams = null)
    {
        return $this->request($url, 'POST', $requestParams);
    }

    /**
     * Makes the 'PUT' request to the $url with an optional $requestParams
     * @param string $url
     * @param array $requestParams
     * @return string
     */
    public function put($url, $requestParams = null)
    {
        return $this->request($url, 'PUT', $requestParams);
    }

    /**
     * Removes the cookie for next cURL transfer
     * @param string $name Name of cookie
     */
    public function removeCookie($name)
    {
        if (isset($this->cookies[$name])) {
            unset($this->cookies[$name]);
        }
    }

    /**
     * Removes the header for next cURL transfer
     * @param string $header
     */
    public function removeHeader($header)
    {
        if (isset($this->headers[$header])) {
            unset($this->headers[$header]);
        }
    }

    /**
     * Removes the option for next cURL transfer
     * @param integer $option CURLOPT_XXX predefined constant
     */
    public function removeOption($option)
    {
        if (isset($this->options[$option])) {
            unset($this->options[$option]);
        }
    }

    /**
     * Removes the request parameter for next cURL transfer
     * @param string $name
     */
    public function removeRequestParam($name)
    {
        if (isset($this->requestParams[$name])) {
            unset($this->requestParams[$name]);
        }
    }

    /**
     * Makes the request of the specified $method to the $url with an optional $requestParams
     * @param string $url
     * @param string $method
     * @param array $requestParams
     * @return string
     */
    public function request($url, $method = 'GET', $requestParams = null)
    {
        $this->setURL($url);
        $this->setRequestMethod($method);
        
        if (!empty($requestParams)) {
            $this->addRequestParam($requestParams);
        }
        //print_R($url);
        $this->initOptions();
        //if ($method =='PUT'){error_log(print_R($this->ch,true));}
        $this->response = curl_exec($this->ch);
        //if ($method =='PUT'){print_R($url);print_R($this->response);exit;}
        //error_log($url); error_log(print_R($this->response,true));//exit;
        if ($this->response === false) {
            throw new CurlWrapperException($this->ch);
        }
        $this->transferInfo = curl_getinfo($this->ch);
        return $this->response;
    }

    /**
     * Reinitiates the cURL handle
     * headers, options, request parameters, cookies and cookies file remain untouchable!
     */
    public function reset()
    {
        $this->__destruct();
        $this->transferInfo = array();
        $this->__construct();
    }

    /**
     * Reinitiates the cURL handle and resets all data,
     * inlcuding headers, options, request parameters, cookies and cookies file
     */
    public function resetAll()
    {
        $this->clearHeaders();
        $this->clearOptions();
        $this->clearRequestParams();
        $this->clearCookies();
        $this->clearCookieFile();
        $this->reset();
    }

    /**
     * Sets the number of seconds to wait while trying to connect, use 0 to wait indefinitely
     * @param integer $seconds
     */
    public function setConnectTimeOut($seconds)
    {
        $this->addOption(CURLOPT_CONNECTTIMEOUT, $seconds);
    }

    /**
     * Sets the filename to store cookies
     * @param string $filename
     */
    public function setCookieFile($filename)
    {
        if (!is_writable($filename)) {
            throw new CurlWrapperException('Cookie file "'.$filename.'" is not writable or does\'n exists!');
        }
        $this->cookieFile = $filename;
    }

    /**
     * Sets the default headers
     */
    public function setDefaultHeaders()
    {
        $this->headers = array(
            'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,/;q=0.8',
            'Accept-Charset'  => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'Accept-Language' => 'en-us,en;q=0.5',
            'Accept-Encoding' => 'gzip,deflate',
            'Keep-Alive'      => '300',
            'Connection'      => 'keep-alive',
            'Cache-Control'   => 'max-age=0',
            'Pragma'          => ''
        );
    }

    /**
     * Sets the default options
     */
    public function setDefaultOptions()
    {
        $this->options = array(
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_HEADER          => false,
            CURLOPT_ENCODING        => 'gzip,deflate',
            CURLOPT_AUTOREFERER     => true,
            CURLOPT_CONNECTTIMEOUT  => 15,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_SSL_VERIFYPEER  => false,
/*            
            CURLOPT_SSLVERSION      => 3,
            CURLOPT_HTTP_VERSION    => 'CURL_HTTP_VERSION_1_0',
            CURLOPT_SSL_CIPHER_LIST => 'TLSv1',
*/
            CURLOPT_VERBOSE         => 0
        );
    }

    /**
     * Sets default headers and options and user agent if $userAgent is given
     * @param string $userAgent Some predefined user agent name (ie, firefox, opera, etc.) or anything string you want
     */
    public function setDefaults($userAgent = null)
    {
        $this->setDefaultHeaders();
        $this->setDefaultOptions();
        if (!empty($userAgent)) {
            $this->setUserAgent($userAgent);
        }
    }

    /**
     * Sets the contents of the "Referer: " header to be used in a HTTP request
     * @param string $referer
     */
    public function setReferer($referer)
    {
        $this->addOption(CURLOPT_REFERER, $referer);
    }

    /**
     * Sets the maximum number of seconds to allow cURL functions to execute
     * @param integer $seconds
     */
    public function setTimeout($seconds)
    {
        $this->addOption(CURLOPT_TIMEOUT, $seconds);
    }

    /**
     * Sets the contents of the "User-Agent: " header to be used in a HTTP request
     * You can use 'magic' words: 'ie', 'firefox', 'opera' and 'chrome'
     * to set one of predefined CurlWrapper's user agents
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        if (isset(self::$predefinedUserAgents[$userAgent])) {
            $this->addOption(CURLOPT_USERAGENT, self::$predefinedUserAgents[$userAgent]);
        } else {
            $this->addOption(CURLOPT_USERAGENT, $userAgent);
        }
    }

    /**
     * Sets the value of cookieFile to empty string
     */
    public function unsetCookieFile()
    {
        $this->cookieFile = '';
    }

    /**
     * Builds url from associative array produced by parse_str() function
     * @param array $parsedUrl
     * @return string
     */
    protected function buildUrl($parsedUrl)
    {
        return (isset($parsedUrl['scheme'])   ?     $parsedUrl["scheme"].'://' : '').
               (isset($parsedUrl['user'])     ?     $parsedUrl["user"].':'     : '').
               (isset($parsedUrl['pass'])     ?     $parsedUrl["pass"].'@'     : '').
               (isset($parsedUrl['host'])     ?     $parsedUrl["host"]         : '').
               (isset($parsedUrl['port'])     ? ':'.$parsedUrl["port"]         : '').
               (isset($parsedUrl['path'])     ?     $parsedUrl["path"]         : '').
               (isset($parsedUrl['query'])    ? '?'.$parsedUrl["query"]        : '').
               (isset($parsedUrl['fragment']) ? '#'.$parsedUrl["fragment"]     : '');
    }

    /**
     * Sets the final options and initiates
     */
    protected function initOptions()
    {
        if (!empty($this->requestParams)) {
            if (isset($this->options[CURLOPT_HTTPGET])) {
                $this->prepareGetParams();
            } else {
                $this->addOption(CURLOPT_POSTFIELDS, $this->requestParams);
            }
        }
        if (!empty($this->headers)) {
            $this->addOption(CURLOPT_HTTPHEADER, $this->prepareHeaders());
        }
        if (!empty($this->cookieFile)) {
            $this->addOption(CURLOPT_COOKIEFILE, $this->cookieFile);
            $this->addOption(CURLOPT_COOKIEJAR, $this->cookieFile);
        }
        if (!empty($this->cookies)) {
            $this->addOption(CURLOPT_COOKIE, $this->prepareCookies());
        }
        if (!curl_setopt_array($this->ch, $this->options)) {
            throw new CurlWrapperException($this->ch);
        }
    }

    /**
     * Converts the cookies array to the correct string format
     * @return string
     */
    protected function prepareCookies()
    {
        $cookiesString = '';
        foreach ($this->cookies as $cookie => $value) {
            $cookiesString .= $cookie.'='.$value.'; ';
        }
        return $cookiesString;
    }

    /**
     * Converts request parameters to the query string and adds it to the request url
     */
    protected function prepareGetParams()
    {
        $parsedUrl = parse_url($this->options[CURLOPT_URL]);
        $query = http_build_query($this->requestParams, '', '&');
        if (isset($parsedUrl['query'])) {
            $parsedUrl['query'] .= '&'.$query;
        } else {
            $parsedUrl['query'] = $query;
        }
        $this->setUrl($this->buildUrl($parsedUrl));
    }

    /**
     * Converts the headers array to the cURL's option format array
     * @return array
     */
    protected function prepareHeaders()
    {
        $headers = array();
        foreach ($this->headers as $header => $value) {
            $headers[] = $header.': '.$value;
        }
        return $headers;
    }

    /**
     * Sets the HTTP request method
     * @param string $method
     */
    protected function setRequestMethod($method)
    {
        // Preventing request methods collision
        $this->removeOption(CURLOPT_NOBODY);
        $this->removeOption(CURLOPT_HTTPGET);
        $this->removeOption(CURLOPT_POST);
        $this->removeOption(CURLOPT_CUSTOMREQUEST);
        switch (strtoupper($method)) {
            case 'HEAD':
                $this->addOption(CURLOPT_NOBODY, true);
                break;
            case 'GET':
                $this->addOption(CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                $this->addOption(CURLOPT_POST, true);
                break;
            default:
                $this->addOption(CURLOPT_CUSTOMREQUEST, $method);
        }
    }

    /**
     * Sets the url
     * @param string $url
     */
    protected function setUrl($url)
    {
        $this->addOption(CURLOPT_URL, $url);
    }
    
}

class CurlWrapperException extends Exception
{
    /**
     * @param string|resource $messageOrCurlHandler
     */
    public function __construct($messageOrCurlHandler)
    {
        if (is_string($messageOrCurlHandler)) {
            $this->message = $messageOrCurlHandler;
        } else {
            $this->message = curl_error($messageOrCurlHandler);
            $this->code = curl_errno($messageOrCurlHandler);
        }
    }
}