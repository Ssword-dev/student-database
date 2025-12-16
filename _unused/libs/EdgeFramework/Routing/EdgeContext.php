<?php
namespace EdgeFramework\Routing;

class EdgeContext {
    public array $scripts = [];
    public array $styles = [];
    public array $meta = [];
    public string $title = 'Document';

    public function script(string $scriptURL) {
        $this->scripts[] = $scriptURL;
    }

    public function style(string $style) {
        $this->styles[] = $style;
    }

    public function metadata(array $metadata) {
        $this->meta = array_merge($this->meta, $metadata);
    }

    public function title(string $title) {
        $this->title = $title;
    }
}