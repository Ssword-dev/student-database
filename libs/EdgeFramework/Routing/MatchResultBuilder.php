<?php
namespace EdgeFramework\Routing;

class MatchResultBuilder {
    private MatchResult $_matchResult;

    public function __construct()
    {
        $this->_matchResult = new MatchResult();
    }

    public function withMatchState(bool $matchState){
        $this->_matchResult->setMatch($matchState);
        return $this;
    }

    public function withParams(array $params){
        $this->_matchResult->setMatchParams($params);
        return $this;
    }

    public function withQuery(array $query){
        $this->_matchResult->setQuery($query);
        return $this;
    }

    public function build(){
        return $this->_matchResult;
    }
}