<?php
namespace EdgeFramework\Output;

use EdgeFramework\Foundation\OutputInstrument;

class BufferedOutputInstrument extends OutputInstrument
{
    private $_buffer = [];
    public function __construct()
    {

    }

    public function write(string $data): void
    {
        $this->_buffer[] = $data;

        $numberOfBufferedChunks = count($this->_buffer);

        if ($numberOfBufferedChunks > 10) {
            $this->flush();
        }
    }

    public function end(): void {
        $this->flush();
    }

    public function flush(): void
    {
        $bufferedData = implode("", $this->_buffer);
        $this->_buffer = [];

        echo $bufferedData;
    }
}