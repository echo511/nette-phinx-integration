<?php

namespace Echo511\NettePhinxIntegration\Config;

use Nette\Database\Connection;
use Nette\DI\Container;
use Phinx\Config\Config;

class ConfigFactory
{
    public static function createConfigFromNetteDatabase(Container $container, $defaults = [])
    {
        $config = $defaults;
        $config['environments']['default_database'] = 'default';
        foreach ($container->findByType(Connection::class) as $connectionServiceName) {
            $parts = explode(".", $connectionServiceName);
            $environment = $parts[1];

            /** @var Connection $connection */
            $connection = $container->getService($connectionServiceName);
            $dbname = self::getDsnValue('dbname', $connection->getDsn());
            $config['environments'][$environment] = [
                'name' => $dbname,
                'connection' => $connection->getPdo()
            ];
        }
        return new Config($config);
    }


    public static function getDsnValue($dsnParameter, $dsn)
    {
        $pattern = sprintf('~%s=([^;]*)(?:;|$)~', preg_quote($dsnParameter, '~'));

        $result = preg_match($pattern, $dsn, $matches);
        if ($result === false) {
            throw new RuntimeException('Regular expression matching failed unexpectedly.');
        }

        return $result ? $matches[1] : false;
    }
}