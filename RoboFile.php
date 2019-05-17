<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    
    function test(){
        $this->taskWatch()
                ->monitor(["src","tests"], function(){
                        $this->taskPHPUnit()
                                ->bootstrap("src/Geocoder.php")
                                ->file("tests/Some.test.php")
                                ->run();
                })->run();
        }
}