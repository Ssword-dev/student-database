<?php

namespace EdgeFramework\Routing\Configurations;

use EdgeFramework\Routing\Enums\OutputKind;

class RouterConfiguration {
    public OutputKind $outputKind;
    public string $projectName;

    public function __construct(
        OutputKind $outputKind = OutputKind::Buffered,
        string $projectName = 'project'
    ) {
        $this->outputKind = $outputKind;
        $this->projectName = $projectName;
    }
}