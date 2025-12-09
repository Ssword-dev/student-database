<?php
namespace EdgeFramework\Routing\ModuleSystem;

abstract class Module {
    private $_hooks = array(); // associative array of functions.

    public function hook(string $hookName, callable $hookFn){
        $this->_hooks[$hookName] = $hookFn;
    }

    public function triggerHook(string $hookName, ...$args) {
        if (isset($this->_hooks[$hookName])){
            $hookFn = $this->_hooks[$hookName];
            $hookFn(...$args);
        }
    } 
    abstract public function register(): void;
}
