<?php
namespace EdgeFramework\Foundation;

use ArrayAccess;
use ClosedGeneratorException;
use Exception;
use Generator;
use Iterator;

/**
 * # IndexableGenerator
 * 
 * ---
 * 
 * ## Abstract
 * 
 * An indexable generator is a generator that can be freely indexed and
 * have values from previous iteration be read.
 * 
 * Specifically, An IndexableGenerator follows the normal generator model
 * and extends it to allow access of the nth item in the generator.
 * 
 * ## Why was this made?
 * 
 * Well, have you tried to build a scanner and a lexer?
 * odds are, you want to stream their **results** instead of
 * having one big array of tokens. but there is a problem. What if
 * we had to look to a previous token to classify the next token?
 * 
 * @template TValue The value that the generator contains.
 * 
 * @implements Iterator<TValue>
 * @implements ArrayAccess<int, TValue>
 */
final class IndexableGenerator implements Iterator, ArrayAccess
{
    /**
     * @var Generator<int, TValue, void, void>
     */
    private Generator $generator;
    private int $currentIndex = 0;
    private int $lastIndex = 0;
    private array $indexedData = [];

    /**
     * Constructs a new indexable generator.
     * @param Generator<int, TValue, void, void>  $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
        $this->currentIndex = 0;
        $this->lastIndex = 0;
        $this->indexedData = [];
        $this->init();
    }

    public function next(): void
    {
        // move the current pointer.
        $this->currentIndex++;

        // this is a the new last item.
        if ($this->currentIndex > $this->lastIndex) {
            $this->lastIndex = $this->currentIndex;

            // move next and compute the next value.
            $this->generator->next();
            $this->provideValue(fn() => $this->computeCurrentValue());
        }
    }

    /**
     * Gets the current value in the generator.
     * 
     * if the current value was not previously computed, then we must
     * be on the last index and the value
     * gets computed, if not, this method throws an error. 
     * 
     * @throws \OutOfBoundsException
     * @return TValue
     */
    public function current(): mixed
    {
        return $this->getCachedOrProvideValue(fn() => $this->generator->current());
    }

    /**
     * Gets the current key for the current value in the generator.
     * @return int
     */
    public function key(): int
    {
        return $this->currentIndex;
    }


    /**
     * Rewinds the generator.
     * @return void
     */
    public function rewind(): void
    {
        $this->currentIndex = 0;
    }

    /**
     * Returns whether the current index is less than or equal
     * the last valid index OR the underlying generator still has values to
     * yield.
     * @return bool
     */
    public function valid(): bool
    {
        // cached value exists
        if (array_key_exists($this->currentIndex, $this->indexedData)) {
            return true;
        }

        // otherwise, generator must still be able to yield
        return $this->generator->valid();
    }

    public function offsetGet($offset): mixed
    {
        // if the offset is less than the last reached index,
        // then the result is already indexed.
        if ($offset <= $this->lastIndex) {
            return $this->indexedData[$offset];
        }

        // advance generator until we reach the offset
        while ($this->currentIndex < $offset) {
            if (!$this->generator->valid()) {
                throw new ClosedGeneratorException("Generator is exhausted.");
            }

            // move next.
            $this->next();
        }

        return $this->current();
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this[$offset] !== null;
    }

    public function offsetSet($offset, $value): void
    {
        throw new Exception("Cannot set item of IndexableGenerator, indexed generators are read-only.");
    }

    public function offsetUnset($offset): void
    {
        throw new Exception("Cannot unset item of IndexableGenerator, indexed generators are read-only.");
    }

    /**
     * Initializes the generator.
     * @return void
     */
    private function init(): void
    {
        // call the first provide value.
        $this->provideValue(fn() => $this->computeCurrentValue());
    }

    private function computeCurrentValue()
    {
        return $this->generator->current();
    }

    private function provideValue(callable $fn)
    {
        $this->indexedData[$this->currentIndex] = $fn();
        return $this->indexedData[$this->currentIndex];
    }

    private function cachedValue()
    {
        return $this->indexedData[$this->currentIndex];
    }

    private function getCachedOrProvideValue(callable $fn)
    {
        return $this->cachedValue() ?? $this->provideValue($fn);
    }
}
