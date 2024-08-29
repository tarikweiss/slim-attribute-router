<?php

namespace Tarikweiss\SlimAttributeRouter;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Slim\App;

class Router
{
    /**
     * @param array<string> $paths
     */
    public function __construct(
        protected array              $paths,
        protected RouteTargetCreator $routeTargetCreator
    )
    {
    }


    public function registerRoutes(App $app): void
    {
        /** @var array<\ReflectionClass<object>> $reflectedClasses */
        $reflectedClasses = [];
        try {
            $reflectedClasses = $this->getAllClassesInPaths();
        } catch (\ReflectionException) {
        }

        foreach ($reflectedClasses as $reflectedClass) {
            $classRouteAttributes = $reflectedClass->getAttributes(Route::class);
            foreach ($classRouteAttributes as $attribute) {
                $route    = $attribute->newInstance();
                $callable = $this->routeTargetCreator->createRouteTarget(RouteLevel::LEVEL_CLASS, $reflectedClass->getName(), null);
                $app->map($route->methods, $route->path, $callable);
            }

            foreach ($reflectedClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $methodRouteAttributes = $method->getAttributes(Route::class);
                foreach ($methodRouteAttributes as $attribute) {
                    $route    = $attribute->newInstance();
                    $callable = $this->routeTargetCreator->createRouteTarget(RouteLevel::LEVEL_METHOD, $reflectedClass->getName(), $method->getName());
                    $app->map($route->methods, $route->path, $callable);
                }
            }
        }
    }


    /**
     * @return array<\ReflectionClass<object>>
     * @throws \ReflectionException
     */
    protected function getAllClassesInPaths(): array
    {
        $includedClassFilePaths = [];

        foreach ($this->paths as $path) {
            $classFilePathIterator = new RegexIterator(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator(
                        $path,
                        FilesystemIterator::SKIP_DOTS
                    ),
                    RecursiveIteratorIterator::LEAVES_ONLY
                ),
                '/^.+\.php$/i',
                RegexIterator::GET_MATCH
            );

            /** @var string[] $classFilePaths */
            foreach ($classFilePathIterator as $classFilePaths) {
                foreach ($classFilePaths as $classFilePath) {
                    require_once $classFilePath;

                    $includedClassFilePaths[] = $classFilePath;
                }

            }
        }

        $includedClassFilePaths = array_unique($includedClassFilePaths);

        $classes = [];

        foreach (get_declared_classes() as $declaredClass) {
            $reflected = new \ReflectionClass($declaredClass);

            if (false === $reflected->getFileName()) {
                continue;
            }

            if (false === in_array($reflected->getFileName(), $includedClassFilePaths)) {
                continue;
            }

            $classes[] = $reflected;
        }

        return $classes;
    }
}