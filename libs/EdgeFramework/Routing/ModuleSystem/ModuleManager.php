<?php
namespace EdgeFramework\Routing\ModuleSystem;

final class ModuleManager {
    /**
     * @var array<int, Module>
     */
    private $_modules = [];

    public function addModule(Module $module): void {
        $this->_modules[] = $module;
        $module->register();
    }
    
    public function triggerHook($hookName, ...$args) {
        foreach($this->_modules as $module){
            $module->triggerHook($hookName, ...$args);
        }
    }
}