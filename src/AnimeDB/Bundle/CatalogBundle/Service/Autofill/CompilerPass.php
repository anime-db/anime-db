<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Service\Autofill;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Autofill compiler pass
 *
 * @package AnimeDB\Bundle\CatalogBundle\Service\Autofill
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
        if (!$container->hasDefinition('anime_db.autofill')) {
            return;
        }

        $definition = $container->getDefinition('anime_db.autofill');
        $taggedServices = $container->findTaggedServiceIds('autofill.filler');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addFiller',
                    [new Reference($id), $attributes['alias']]
                );
            }
        }
    }
}