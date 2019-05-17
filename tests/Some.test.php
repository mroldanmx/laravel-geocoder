<?php

require "vendor/autoload.php";
use PHPUnit\Framework\TestCase;

class SomeTest extends TestCase{

        public function testSome() {
            $geo = new Mroldan\Geocoder\Geocoder();
            $geo->debug = true;
            $geo->useProxy= true;

            $this->assertContains( "geocoder", $geo->serviceURL("V8L4S2"));

            $location = $geo->locate("V8L4S2");

            $this->assertTrue(is_object($location));
            $this->assertContains('Sidney',$location->standard->city);

            //this one should run from cache
            $this->assertNull($geo->locate("THIS SHOULD BE A VERY LARGE LOCATION"));
        } 
} 
?>
