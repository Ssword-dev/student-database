<?php
use EdgeFramework\Build\Pipeline\Pipeline;
use EdgeFramework\Build\Pipeline\Step;

/**
 * @extends parent<int, int>
 */
class SimplePipelineStep implements Step
{
    public function process($input)
    {
        return $input * 2;
    }
}

describe("Pipeline", function () {
    it("should be able to produce input and output for simple pipeline steps.", function () {
        $pipeline = new Pipeline([
            new SimplePipelineStep(), // x * 2
            new SimplePipelineStep(), // x * 2
        ]);

        expect($pipeline->process(1))->toBe(4);
    });
});
