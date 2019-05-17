<?php

namespace Mroldan\Geocoder;

class Geocoder {

    private $cache = [];
    private $proxyList = null;//set in downloadProxyList 

    public $useProxy = false;
    public $debug = false;
    public $proxyListURL = 'https://proxy.rudnkh.me/txt';

    public function serviceURL($location){
        return sprintf("http://geocoder.ca?json=1&locate=%s", $location);
    }

    public function cacheGet($key){
        $value = null;

        if(array_key_exists($key, $this->cache)){
            $value = $this->cache[$key];
        }

        return $value;
    }

    public function cacheSet($key,$value){
        $this->cache[$key] = $value;
        return $this->cacheGet($key);
    }

    /**
     * Get location data
     * @param string $location
     * @return Object|null
     */
    public function locate($location){
        $results = null;
        $contents = $this->cacheGet($location);
        if(!$contents){
            $url = $this->serviceURL($location);

            try{
                $contents = $this->fileGetContents($url);
                if($contents){
                    $this->log($contents);
                    $this->cacheSet($location,$contents);
                    $results = json_decode($contents);
                }
            }catch(\Exception $e){
                $results = null;
            }
        }else{
            $results = json_decode($contents);
        }
    
        return $results;
    }

    public function downloadProxyList(){
        $proxyList = [];
        $url = $this->proxyListURL;

        $contents = file_get_contents($url);

        if($contents){
            $proxyList = explode("\n",$contents);
            $proxyList = array_filter($proxyList, function($item){
                return !empty($item);
            });
        }

        return $proxyList;
    }

    public function nextProxy(){
        if(is_null($this->proxyList)){
            $this->proxyList = $this->downloadProxyList();
        }
        return array_shift($this->proxyList);
    }


    public function fileGetContents($url,$proxy=false){
        
        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        if($proxy){
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }

        // $output contains the output string
        $contents = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch); 

        if($this->useProxy && ($throttled = strpos($contents,'Throttled') !== false || $denied = strpos($contents,'ERR_ACCESS_DENIED') !== false)){
            $this->log($throttled?'Throttled':'ACCESS_DENIED');
            $nextProxy = $this->nextProxy();
            
            if($nextProxy){
                $this->log("Trying with proxy... $nextProxy");
                $contents = $this->fileGetContents($url, $nextProxy);
            }else{
                $this->log('No more proxies to try');
                $contents = null;
            }
        }

        return $contents;
    }

    private function log($msg){
        if($this->debug){
            error_log($msg);
        }
    }
}

?>
