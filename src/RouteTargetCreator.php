<?php

namespace Tarikweiss\SlimAttributeRouter;

interface RouteTargetCreator
{
    /**
     * Create a route target callable or string for slim route registration.
     *
     * @param string|null $method The name of the method inside the class.
     *
     * @see \Slim\App::map() The used mapping method with the slim app.
     */
    public function createRouteTarget(RouteLevel $routeLevel, string $class, ?string $method): callable|string;
}