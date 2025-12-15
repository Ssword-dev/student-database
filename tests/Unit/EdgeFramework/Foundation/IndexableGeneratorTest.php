<?php

use EdgeFramework\Foundation\IndexableGenerator;

class InternalIndexableGeneratorStructure extends IndexableGenerator
{
    public \Generator $generator;
    public int $currentIndex = 0;
    public array $indexedData = [];
    public int $lastIndex = 0;

    public function getGenerator(): \Generator
    {
        return $this->generator;
    }

    public function getCurrentIndex(): int
    {
        return $this->currentIndex;
    }

    public function getLastIndex(): int
    {
        return $this->lastIndex;
    }

    public function getIndexedData(): array
    {
        return $this->indexedData;
    }
}

describe("IndexableGenerator", function () {
    $createBaseGenerator = function (...$items) {
        yield from $items;
        return null;
    };

    it("should act like a generator", function () use ($createBaseGenerator) {
        $iteration = 0;
        $baseGenerator = $createBaseGenerator(1, 2, 3);

        $indexableGenerator = new IndexableGenerator($baseGenerator);

        foreach ($indexableGenerator as $key => $value) {
            $iteration++;
        }

        expect($iteration)->toBe(3);
    });

    it("should be indexable", function () use ($createBaseGenerator) {
        $baseGenerator = $createBaseGenerator(...range(1, 10));

        $indexableGenerator = new IndexableGenerator($baseGenerator);

        expect($indexableGenerator[9])->toBe(10);

        foreach (range(0, 8) as $key) {
            expect($indexableGenerator[$key])->toBe($key + 1);
        }
    });
});
