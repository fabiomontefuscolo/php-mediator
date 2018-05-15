<?php

use PHPUnit\Framework\TestCase;
use montefuscolo\SingletonMediator;


class SingletonMediatorTest extends BaseMediatorTest {
    public function getInstance() {
        return SingletonMediator::getInstance();
    }

    public function testUniqueInstance() {
        $instance1 = $this->getInstance();
        $instance2 = $this->getInstance();

        $instance1->add_filter('test_unique_instance', function($value) {
            return "> $value <";
        });
        $result = $instance2->run_filters('test_unique_instance', 'foo');

        $this->assertSame($instance1, $instance2);
        $this->assertEquals("> foo <", $result);
    }
}
