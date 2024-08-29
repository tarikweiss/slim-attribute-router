<?php

namespace Tarikweiss\SlimAttributeRouter;

use Tarikweiss\SlimAttributeRouter\RouteTargetCreator;

class PublicMethodRouteCreator implements RouteTargetCreator
{
    public function __construct(
        public string $classLevelMethodName = 'run'
    )
    {
    }


    public function createRouteTarget(RouteLevel $routeLevel, string $class, ?string $method): callable|string
    {
        return match ($routeLevel) {
            RouteLevel::LEVEL_CLASS  => $class . ':' . $this->classLevelMethodName,
            RouteLevel::LEVEL_METHOD => $class . ':' . $method,
        };
    }
}