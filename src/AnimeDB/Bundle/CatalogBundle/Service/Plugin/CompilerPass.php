<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Service\Plugin;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Plugin compiler pass
 *
 * @package AnimeDB\Bundle\CatalogBundle\Service\Plugin
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
        $this->compilerChain($container, 'anime_db.plugin.filler',  'anime_db.filler');
        $this->compilerChain($container, 'anime_db.plugin.search',  'anime_db.search');
        $this->compilerChain($container, 'anime_db.plugin.import',  'anime_db.import');
        $this->compilerChain($container, 'anime_db.plugin.item',    'anime_db.item');
        $this->compilerChain($container, 'anime_db.plugin.setting', 'anime_db.setting');
    }

    /**
     * Compiler chain
     *
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $chain_name
     * @param string $tag
     */
    private function compilerChain(ContainerBuilder $container, $chain_name, $tag)
    {
        if ($definition = $container->getDefinition($chain_name)) {
            $taggedServices = $container->findTaggedServiceIds($tag);
            foreach ($taggedServices as $id => $tagAttributes) {
                foreach ($tagAttributes as $attributes) {
                    $definition->addMethodCall('addPlugin', [new Reference($id)]);
                }
            }
        }
    }
}