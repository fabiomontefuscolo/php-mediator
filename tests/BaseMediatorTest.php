<?php

use PHPUnit\Framework\TestCase;
use montefuscolo\BaseMediator;


class BaseMediatorTest extends TestCase {
    public function getInstance() {
        return new BaseMediator();
    }

    public function testAddAction() {
        $self = $this;

        $mediator = $this->getInstance();
        $mediator->add_action('test_add_action', function() use ($self) {
            $self->assertTrue(true);
        });

        $mediator->run_actions('test_add_action');
    }

    public function testAddActionWithParameters() {
        $self = $this;

        $mediator = $this->getInstance();
        $mediator->add_action('test_addaction_with_parameters', function($subject) use ($self) {
            $self->assertEquals('the-parameter', $subject);
        });

        $mediator->run_actions('test_addaction_with_parameters', 'the-parameter');
    }

    public function testAddActionPriority() {
        $mediator = $this->getInstance();
        $state = (object) array('name' => '');

        $mediator->add_action('test_add_action_priority', function($subject) {
            $subject->name .= '1';
        }, 20);

        $mediator->add_action('test_add_action_priority', function($subject) {
            $subject->name .= '2';
        }, 10);

        $mediator->add_action('test_add_action_priority', function($subject) {
            $subject->name .= '3';
        }, 30);

        $mediator->run_actions('test_add_action_priority', $state);
        $this->assertEquals('213', $state->name);
    }

    public function testRemoveAction() {
        $mediator = $this->getInstance();

        $callback1 = function($subject) { $subject->name .= 'callback1'; };
        $mediator->add_action('test_remove_action', $callback1, 10);

        $callback2 = function($subject) { $subject->name .= 'callback2'; };
        $mediator->add_action('test_remove_action', $callback2, 20);

        $state = (object) array('name' => '');
        $mediator->run_actions('test_remove_action', $state);
        $this->assertEquals('callback1callback2', $state->name);

        $state = (object) array('name' => '');
        $mediator->remove_action('test_remove_action', $callback1);
        $mediator->run_actions('test_remove_action', $state);
        $this->assertEquals('callback2', $state->name);
    }

    public function testEmptyAction() {
        $mediator = $this->getInstance();

        $callback1 = function($subject) { $subject->name .= 'callback1'; };
        $mediator->add_action('test_empty_action', $callback1, 10);

        $callback2 = function($subject) { $subject->name .= 'callback2'; };
        $mediator->add_action('test_empty_action', $callback2, 20);

        $state = (object) array('name' => 'untouched');
        $mediator->remove_action('test_empty_action');
        $mediator->run_actions('test_empty_action', $state);
        $this->assertEquals('untouched', $state->name);
    }

    public function testAddFilter() {
        $mediator = $this->getInstance();

        $mediator->add_filter('test_add_filter', function($subject){
            return $subject * 2;
        });
        $mediator->add_filter('test_add_filter', function($subject){
            return $subject * 3;
        });
        $mediator->add_filter('test_add_filter', function($subject){
            return $subject - 6;
        });

        $result = $mediator->run_filters('test_add_filter', 1);
        $this->assertEquals(0, $result);
    }

    public function testRunEmptyFilter() {
        $mediator = $this->getInstance();
        $result = $mediator->run_filters('test_run_empty_filter', 'untouched');
        $this->assertEquals('untouched', $result);
    }
}
