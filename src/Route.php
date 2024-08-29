<?php

namespace Tarikweiss\SlimAttributeRouter;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Route
{
    /**
     * @param array<string> $methods The methods the path should be registered for (for example GET, POST, PATCH, PUT, ...).
     * @param string        $path    The path for the action. Needs to be a path accepted by {@link \Slim\App::map()}.
     */
    public function __construct(
        public array  $methods,
        public string $path
    )
    {
    }
}