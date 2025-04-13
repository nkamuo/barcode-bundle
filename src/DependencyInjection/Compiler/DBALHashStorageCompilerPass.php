<?php

declare(strict_types=1);

namespace Nkamuo\Barcode\DependencyInjection\Compiler;

use Nkamuo\Barcode\Storage\DBALHashStorage;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class DBALHashStorageCompilerPass implements CompilerPassInterface
{

    public const HASH_STORAGE_SERVICE_ID = 'barcode.storage.hash.default';

    public const DOCTRINE_DBAL_CONNECTION_SERVICE_ID = 'doctrine.dbal.default_connection';
    public function process(ContainerBuilder $container): void
    {
        // in this method you can manipulate the service container:
        // for example, changing some container service:
        /** @var Definition $definition */
        $definition = null;
        if($container->hasDefinition(self::HASH_STORAGE_SERVICE_ID)) {
            $definition = $container->getDefinition(self::HASH_STORAGE_SERVICE_ID);
        }else{
            $definition = $container->setDefinition(self::HASH_STORAGE_SERVICE_ID, new Definition());
        }

        if($container->hasDefinition(self::DOCTRINE_DBAL_CONNECTION_SERVICE_ID)){
            $ref = new Reference(self::DOCTRINE_DBAL_CONNECTION_SERVICE_ID);
            $definition
                ->setClass(DBALHashStorage::class)
                ->setArgument('$connection', $ref)
                ->setArgument('$tableName', 'nkamuo_barcode_hash_table')
                ->setArgument('$autoCreateTable', true)
            ;
        }
    }
}
