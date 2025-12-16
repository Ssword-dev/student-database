<?php

namespace EdgeFramework\Routing;

class MatchResult {
    private bool $_matched;
    private ?array $_params;
    private ?array $_query;

    public function __construct()
    {
        $this->_matched = false;
        $this->_params = null;
        $this->_query = null;
    }

    public function matched(): bool {
        return $this->_matched;
    }

    public function params(): ?array {
        return $this->_params;
    }

    public function query(): ?array {
        return $this->_query;
    }

    public function setMatch(bool $matchState){
        $this->_matched = $matchState;
    }

    public function setMatchParams(array $params){
        $this->_params = $params;
    }

    public function setQuery(array $query){
        $this->_query = $query;
    }

    public static function createBuilder(){
        return new MatchResultBuilder();
    }
}