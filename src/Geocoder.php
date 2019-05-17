<?php

class Geocoder {

    private $cache = [];

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
                $contents = file_get_contents($url);
                if($contents){
                    $this->cacheSet($location,$contents);
                    $results = json_decode($contents);
                }
            }catch(\Exception $e){
                $results = null;
            }
        }
    
        return $results;
    }
}

?>
