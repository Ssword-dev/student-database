<?php

namespace EdgeFramework\Build\Pipeline;

/**
 * @template I Step input.
 * @template O Step Output.
 */
abstract class Step {
    /**
     * Processes the previous step's output or the initial input.
     * @param I $input
     * @return O
     */
    abstract public function process(mixed $input);
}