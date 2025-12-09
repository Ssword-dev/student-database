<?php

require dirname(__DIR__, 4) . "/vendor/autoload.php";


use EdgeFramework\Build\Pipeline\TokenizationStep;

describe("Tokenization Step", function () {
    $step = new TokenizationStep();

    describe("TokenizationStep", function () use ($step) {
        it("should be able to parse old english correctly.", function () use ($step) {
            $source = 'Hark ye Traveler! For it is I, the Captaineth of this Shipeth!';
            $tokens = $step->process($source);

            expect($tokens)->toHaveLength(26);
        });

        it('should be able to parse shakespearean works.', function () use ($step) {
            $source = 'Gregory, on my word we\'ll not carry coals.';
            $tokens = $step->process($source);

            expect($tokens)->toHaveLength(19);
        });

        it('should be able to only parse programming english.', function () use ($step) {
            $source = 'Gregory, on my word wi’ll not carry coals.';
            $tokens = $step->process($source);

            expect($tokens)->toHaveLength(21);
        });

        
        it('should parse numbers correctly', function () use ($step) {
            $source = 'The value is 3.14159 and not 42.';
            $tokens = $step->process($source);

            // word / whitespace / word / whitespace / word / whitespace /
            // number (3.14159) / whitespace / word / whitespace/ word / space /
            // number (42)
            expect($tokens)->toHaveLength(13);
        });

        it('should handle multiple punctuation and whitespace', function () use ($step) {
            $source = 'Hello...   world!!!';
            $tokens = $step->process($source);

            expect($tokens)->toHaveLength(9);
        });

        it('should parse empty string as zero tokens', function () use ($step) {
            $source = '';
            $tokens = $step->process($source);

            expect($tokens)->toHaveLength(0);
        });

        it('should parse single symbols correctly', function () use ($step) {
            $source = '{}[](),;+-*/=';
            $tokens = $step->process($source);

            // 13 single-char tokens
            expect($tokens)->toHaveLength(13);
        });
    });
});