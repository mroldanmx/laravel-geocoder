<?php

use PHPUnit\Framework\TestCase;

class SomeTest extends TestCase{

        public function testSome() {
            $some = new SomeClass();
            $this->assertEquals( "some string", $some->mimic("some string"));
        } 
} 
?>
