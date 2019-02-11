<?php

namespace App\ServiceProviders;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use App\Index\IndexStore;

class IndexStoreProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        // Setup database connection
        $container['db'] = function ($container) {

            // Check if database exists
            $exists = is_file($container['settings']['app.file.store']);

            // Create database connection
            $connection = DriverManager::getConnection(
                [
                    'driver' => 'pdo_sqlite',
                    'path' => $container['settings']['app.file.store']
                ],
                new Configuration
            );

            // If database does not exists, create structure
            if (!$exists) {
                $sql = file_get_contents($container['settings']['app.file.store_sql']);
                $connection->prepare($sql)->execute();
            }

            return $connection;
        };

        // Setup index store
        $container['index_store'] = function($container) {
            return new IndexStore($container['db']);
        };
    }
}
