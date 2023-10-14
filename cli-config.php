<?php

// cli-config.php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use DI\Container;

/** @var Container $container */
$container = require_once __DIR__ . '/config/container.php';

return ConsoleRunner::createHelperSet($container->get(EntityManager::class));
