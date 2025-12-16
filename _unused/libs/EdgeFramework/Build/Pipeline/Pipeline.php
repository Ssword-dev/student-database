<?php namespace EdgeFramework\Build\Pipeline;


/**
 * @template I The input of the pipeline
 * @template O The output of the final step in the pipeline
 */
class Pipeline {
    /**
     * @var Step<mixed, mixed>[]
     */
    private array $steps;

    public function __construct(array $steps) { $this->steps = $steps; }

    /**
     * @param I $input The input of the pipeline
     * @return O The output of the pipeline
     */
    public function process($input){
        $result = $input;

        foreach($this->steps as $step){
            $result = $step->process($result);
        }

        return $result;
    }
}
