<?php
namespace Faramoj\AdvancedSearch;

class Container {
    private $services = [];
    private $factories = [];
    private $instances = [];

    public function set($id, callable $factory) {
        $this->factories[$id] = $factory;
    }

    public function get($id) {
        if (!isset($this->instances[$id])) {
            if (!isset($this->factories[$id])) {
                throw new \Exception("Service not found: {$id}");
            }
            $this->instances[$id] = $this->factories[$id]($this);
        }
        return $this->instances[$id];
    }
}
