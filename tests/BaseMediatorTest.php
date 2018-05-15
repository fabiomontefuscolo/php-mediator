<?php

use PHPUnit\Framework\TestCase;
use montefuscolo\BaseMediator;


class BaseMediatorTest extends TestCase {
    
    public function testUniqueInstance() {
        $instance1 = BaseMediator::getInstance();
        $instance2 = BaseMediator::getInstance();

        $this->assertSame($instance1, $instance2);
        $this->assertEquals($instance1, $instance2);
    }

    public function testAddAction() {
        $self = $this;

        $hooks = BaseMediator::getInstance();
        $hooks->add_action('test_add_action', function() use ($self) {
            $self->assertTrue(true);
        });

        $hooks->run_actions('test_add_action');
    }

    public function testAddActionWithParameters() {
        $self = $this;

        $hooks = BaseMediator::getInstance();
        $hooks->add_action('test_addaction_with_parameters', function($subject) use ($self) {
            $self->assertEquals('the-parameter', $subject);
        });

        $hooks->run_actions('test_addaction_with_parameters', 'the-parameter');
    }


    public function testAddActionPriority() {
        $hooks = BaseMediator::getInstance();
        $state = (object) array('name' => '');

        $hooks->add_action('test_add_action_priority', function($subject) {
            $subject->name .= '1';
        }, 20);

        $hooks->add_action('test_add_action_priority', function($subject) {
            $subject->name .= '2';
        }, 10);

        $hooks->add_action('test_add_action_priority', function($subject) {
            $subject->name .= '3';
        }, 30);

        $hooks->run_actions('test_add_action_priority', $state);
        $this->assertEquals('213', $state->name);
    }

    public function testRemoveAction() {
        $hooks = BaseMediator::getInstance();

        $callback1 = function($subject) { $subject->name .= 'callback1'; };
        $hooks->add_action('test_remove_action', $callback1, 10);

        $callback2 = function($subject) { $subject->name .= 'callback2'; };
        $hooks->add_action('test_remove_action', $callback2, 20);

        $state = (object) array('name' => '');
        $hooks->run_actions('test_remove_action', $state);
        $this->assertEquals('callback1callback2', $state->name);

        $state = (object) array('name' => '');
        $hooks->remove_action('test_remove_action', $callback1);
        $hooks->run_actions('test_remove_action', $state);
        $this->assertEquals('callback2', $state->name);
    }

    public function testEmptyAction() {
        $hooks = BaseMediator::getInstance();

        $callback1 = function($subject) { $subject->name .= 'callback1'; };
        $hooks->add_action('test_empty_action', $callback1, 10);

        $callback2 = function($subject) { $subject->name .= 'callback2'; };
        $hooks->add_action('test_empty_action', $callback2, 20);

        $state = (object) array('name' => 'untouched');
        $hooks->remove_action('test_empty_action');
        $hooks->run_actions('test_empty_action', $state);
        $this->assertEquals('untouched', $state->name);
    }

    public function testAddFilter() {
        $hooks = BaseMediator::getInstance();

        $hooks->add_filter('test_add_filter', function($subject){
            return $subject * 2;
        });
        $hooks->add_filter('test_add_filter', function($subject){
            return $subject * 3;
        });
        $hooks->add_filter('test_add_filter', function($subject){
            return $subject - 6;
        });

        $result = $hooks->run_filters('test_add_filter', 1);
        $this->assertEquals(0, $result);
    }
}
