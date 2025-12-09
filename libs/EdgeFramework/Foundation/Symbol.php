<?php
namespace EdgeFramework\Foundation;

/**
 * Constructs a unique symbol.
 */
class Symbol {
    public $name;
    public function __construct($name) {
        $this->name = $name;
    }

    public function __toString() {
        return "Symbol($this->name)";
    }

    private static array $symbolRegistry = [];

    /**
     * Returns a normal symbol.
     * @param string $name The name of the symbol.
     * @return Symbol
     */
    public static function for(string $name): Symbol{
        if (!isset(self::$symbolRegistry[$name])) {
            self::$symbolRegistry[$name] = new Symbol($name);
        }

        return self::$symbolRegistry[$name];
    }
}