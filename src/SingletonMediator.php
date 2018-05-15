<?php
namespace montefuscolo;

class SingletonMediator extends BaseMediator {
    private static $instance = null;

    private function __construct() {
        parent::__construct();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
