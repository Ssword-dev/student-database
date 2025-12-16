<?php
namespace EdgeFramework\Foundation;

abstract class OutputInstrument {
    /**
     * A method to write to the instrument.
     */
    abstract public function write(string $data): void;

    /**
     * Ends the outputs.
     */
    abstract public function end(): void;
}