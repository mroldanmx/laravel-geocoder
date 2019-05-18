<?php

namespace Mroldan\Geocoder;

class Geocoder
{

    private $cache = [];
    private $proxyList = null; //set in downloadProxyList 
    private $currentProxy = null;
    public $timeout = 1; //seconds

    public $useProxy = false;
    public $debug = false;
    public $proxyListURL = 'https://proxy.rudnkh.me/txt';

    public $sourceURL = "http://geocoder.ca";
    public $options = [
        'json' => 1,
    ]; //can be customized

    public $errorTypes = [
        'Throttled',
        'ERR_ACCESS_DENIED',
        '400 Bad Request',
        'Authentication Required',
    ];


    public function serviceURL($location)
    {
        $this->options['locate'] = $location;
        $queryParams = \http_build_query($this->options);
        return sprintf("%s?%s", $this->sourceURL, $queryParams);
    }

    public function cacheGet($key)
    {
        $value = null;

        if (array_key_exists($key, $this->cache)) {
            $value = $this->cache[$key];
        }

        return $value;
    }

    public function cacheSet($key, $value)
    {
        $this->cache[$key] = $value;
        return $this->cacheGet($key);
    }

    /**
     * Get location data
     * @param string $location
     * @return Object|null
     */
    public function locate($location)
    {
        $results = null;
        $contents = $this->cacheGet($location);
        if (!$contents) {
            $url = $this->serviceURL($location);

            try {
                $contents = $this->fileGetContents($url);
                if ($contents) {
                    $this->log($contents);
                    $this->cacheSet($location, $contents);
                    $results = json_decode($contents);
                }
            } catch (\Exception $e) {
                $results = null;
            }
        } else {
            $results = json_decode($contents);
        }

        return $results;
    }

    public function downloadProxyList()
    {
        $proxyList = [];
        $url = $this->proxyListURL;

        $contents = file_get_contents($url);

        if ($contents) {
            $proxyList = explode("\n", $contents);
            $proxyList = array_filter($proxyList, function ($item) {
                return !empty($item);
            });
        }

        return $proxyList;
    }

    public function nextProxy()
    {
        if (is_null($this->proxyList)) {
            $this->proxyList = $this->downloadProxyList();
        }
        $this->currentProxy = array_shift($this->proxyList);
    }


    public function fileGetContents($url)
    {

        $ch = curl_init();
        // set url

        $timeStart = microtime(true);
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        if ($this->currentProxy) {
            $this->log("using proxy $this->currentProxy");
            curl_setopt($ch, CURLOPT_PROXY, $this->currentProxy);
        }

        // $output contains the output string
        $contents = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);
        $timeEnd = microtime(true);
        $lasted = $timeEnd - $timeStart;

        $error = $this->parseErrors($contents);

        $this->log("Took $lasted");
        if (!$contents || (($error && $lasted <= $this->timeout) && $this->useProxy)) {

            $this->log($error);
            $this->nextProxy();

            if ($this->currentProxy) {
                $this->log("Trying with proxy... $this->currentProxy");
                $contents = $this->fileGetContents($url);
            } else {
                $this->log('No more proxies to try');
                throw new NoMoreProxiesException('No more proxies to try');
            }
        }

        return $contents;
    }

    private function parseErrors($string)
    {
        foreach ($this->errorTypes as $errorType) {
            if (strpos($string, $errorType) !== false) {
                return $errorType;
            }
        }

        return false;
    }

    private function log($msg)
    {
        if ($this->debug) {
            error_log($msg);
        }
    }
}
