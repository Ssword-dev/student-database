<?php
namespace EdgeFramework\Routing;

class PartialContent {
    public string $timeStamp;
    public string $content;

    public function __construct(string $content) {
        $this->timeStamp = floor(microtime(true) * 1000);
        $this->content = $content;
    }
}