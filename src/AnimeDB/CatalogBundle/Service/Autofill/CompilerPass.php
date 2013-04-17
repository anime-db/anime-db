<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\CatalogBundle\Service\Autofill;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Autofill compiler pass
 *
 * @package AnimeDB\CatalogBundle\Service\Autofill
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class CompilerPass implements CompilerPassInterface
{
    /**
     * Process container builder
     *
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('anime_db_catalog.autofill.chain')) {
            return;
        }

        $definition = $container->getDefinition('anime_db_catalog.autofill.chain');
        $taggedServices = $container->findTaggedServiceIds('anime_db_catalog.autofill');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addFiller',
                    array(new Reference($id), $attributes['alias'])
                );
            }
        }
    }
}