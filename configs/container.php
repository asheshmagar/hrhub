<?php
/**
 * Container config.
 *
 * @return \DI\Container
 */

use DI\ContainerBuilder;

$container_builder = new ContainerBuilder();
$container_builder->addDefinitions( __DIR__ . '/container-bindings.php' );

return $container_builder->build();
