<?php
namespace montefuscolo;

class SingletonMediator  {
    private static $instance = null;
    private $mediator = null;

    private function __construct() {
        $this->mediator = new BaseMediator();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function add_filter($name, $callback, $priority=10) {
        $this->mediator->add_filter($name, $callback, $priority);
    }

    public function add_action($name, $callback, $priority=10) {
        $this->mediator->add_action($name, $callback, $priority);
    }

    public function remove_filter($name, $func=null) {
        return $this->mediator->remove_filter($name, $func);
    }

    public function remove_action($name, $func=null) {
        return $this->mediator->remove_action($name, $func);
    }

    public function run_filters($name, $subject=null) {
        return $this->mediator->run_filters($name, $subject);
    }

    public function run_actions($name, $subject=null) {
        return $this->mediator->run_actions($name, $subject);
    }
}
