<?php
namespace EdgeFramework\Output;

use EdgeFramework\Foundation\OutputInstrument;

const BUFFER_LENGTH_THRESHOLD = 500;

class StreamingOutputInstrument extends OutputInstrument {
    public bool $isFirstWrite = true;
    private $_buffer = [];
    private int $_bufferedOutputSize = 0;
    public function __construct() {
        ob_end_flush();
        ob_end_clean();
    }

    public function write(string $data): void {
        // this improves TTFB.
        if ($this->isFirstWrite) {
            echo $data;
            flush();
        }

        else {
            $this->streamData($data);
        }
    }

    public function end(): void {
        $this->flush();
    }

    private function streamData(string $data){
        $this->_buffer []= $data;
        $this->_bufferedOutputSize += strlen($data);

        if ($this->_bufferedOutputSize >= BUFFER_LENGTH_THRESHOLD){
            $this->flush();
        }
    }

    private function flush(): void {
        $bufferedOutput = implode('', $this->_buffer);
        $this->_buffer = [];
        $this->_bufferedOutputSize = 0;

        echo $bufferedOutput;
        flush();
    }
}