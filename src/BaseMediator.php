<?php
namespace montefuscolo;

class BaseMediator {
    private static $instance = null;
    private $filters = null;
    private $actions = null;
    private $prefix = 'root:';

    private function __construct() {
        $this->filters = array();
        $this->actions = array();
    }

    private function add(&$chain, $name, $callback, $priority) {
        if (empty($chain[ $name ])) {
            $chain[ $name ] = array();
        }
        $chain[ $name ][ ] = array($callback, $priority);
    }

    private function remove(&$chain, $name, $func) {
        if (empty($chain[ $name ])) {
            return;
        }
        if (empty($func)) {
            unset($chain[ $name ]);
            return;
        }
        foreach ($chain[ $name ] as $key => $function) {
            list($callback, $priority) = $function;
            if ( $callback === $func ) {
                unset($chain[ $name ][ $key ]);
            }
        }
    }

    private function run(&$chain, $name, $subject=null) {
        if (empty($chain[ $name ])) {
            return $subject;
        }

        $filtering = $this->filters === $chain;
        $subject = array_slice(func_get_args(), 2);

        usort($chain[ $name ], function($a, $b) {
            return $a[1] === $b[1] ? 0 : ( $a[1] < $b[1] ? -1 : 1 ); 
        });

        foreach ($chain[ $name ] as $function) {
            list($callback, $priority) = $function;
            $result = call_user_func_array($callback, $subject);

            if ($filtering) {
                $subject = array($result);
            }
        }

        return $subject;
    }

    private function get_hook_name($name) {
        return $this->prefix . $name;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function add_filter($name, $callback, $priority=10) {
        $this->add($this->filters, $name, $callback, $priority);
    }

    public function add_action($name, $callback, $priority=10) {
        $this->add($this->actions, $name, $callback, $priority);
    }

    public function remove_filter($name, $func=null) {
        return $this->remove($this->filters, $name, $func);
    }

    public function remove_action($name, $func=null) {
        return $this->remove($this->actions, $name, $func);
    }

    public function run_filters($name, $subject=null) {
        if (empty($this->filters[ $name ])) {
            return $subject;
        }

        usort($this->filters[ $name ], function($a, $b) {
            return $a[1] === $b[1] ? 0 : ( $a[1] < $b[1] ? -1 : 1 ); 
        });

        foreach ($this->filters[ $name ] as $function) {
            list($callback, $priority) = $function;
            $subject = call_user_func($callback, $subject);
        }
        return $subject;
    }

    public function run_actions($name, $subject=null) {
        if (empty($this->actions[ $name ])) {
            return $subject;
        }

        usort($this->actions[ $name ], function($a, $b) {
            return $a[1] === $b[1] ? 0 : ( $a[1] < $b[1] ? -1 : 1 ); 
        });

        foreach ($this->actions[ $name ] as $function) {
            list($callback, $priority) = $function;
            call_user_func($callback, $subject);
        }
    }
}
