<?php namespace EdgeFramework\Build\Pipeline;

/**
 * @template I Step input.
 * @template O Step Output.
 */
interface Step {
    /**
     * Processes the previous step's output or the initial input.
     * @param I $input
     * @return O
     */
    public function process($input);
}
