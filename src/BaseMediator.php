<?php
namespace montefuscolo;

class BaseMediator {
    private $filters = null;
    private $actions = null;

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

        $result = null;
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

        return $result;
    }

    public function __construct() {
        $this->filters = array();
        $this->actions = array();
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
        return $this->run($this->filters, $name, $subject);
    }

    public function run_actions($name, $subject=null) {
        return $this->run($this->actions, $name, $subject);
    }
}
