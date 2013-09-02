<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use AnimeDB\Bundle\CatalogBundle\Plugin\CustomMenu;
use AnimeDB\Bundle\CatalogBundle\Plugin\Chain;

/**
 * Menu builder
 *
 * @package AnimeDB\Bundle\CatalogBundle\Menu
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Builder extends ContainerAware
{
    /**
     * Builder main menu
     * 
     * @param \Knp\Menu\FactoryInterface $factory
     * @param array $options
     *
     * @return 
     */
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        /* @var $menu \Knp\Menu\ItemInterface */
        $menu = $factory->createItem('root');

        $menu->addChild('Search', ['route' => 'home_search']);
        $add = $menu->addChild('Add record');
        $settings = $menu->addChild('Settings');

        // add search plugin items
        $this->addPluginItems(
            $this->container->get('anime_db.plugin.search'),
            $add,
            'item_search',
            'Search source of filling',
            'Search the source and fill record from it'
        );
        // add filler plugin items
        $this->addPluginItems(
            $this->container->get('anime_db.plugin.filler'),
            $add,
            'item_filler',
            'Fill from source',
            'Fill record from source (example source is URL)'
        );
        // add import plugin items
        $this->addPluginItems(
            $this->container->get('anime_db.plugin.import'),
            $add,
            'item_import',
            'Import items'
        );
        // add settings plugin items
        $chain = $this->container->get('anime_db.plugin.setting');
        foreach ($chain->getPlugins() as $plugin) {
            $plugin->buildMenu($settings);
        }

        // add manually
        $add->addChild('Add manually', ['route' => 'item_add_manually']);
        $settings->addChild('File storages', ['route' => 'storage_list']);
        $settings->addChild('List of notice', ['route' => 'notice_get_list']);
        $settings->addChild('General', ['route' => 'home_settings']);

        return $menu;
    }

    /**
     * Add plugin items in menu
     *
     * @param \AnimeDB\Bundle\CatalogBundle\Service\Plugin\Chain $chain
     * @param \Knp\Menu\ItemInterface $root
     * @param string $route
     * @param string $label
     * @param string|null $title
     */
    private function addPluginItems(Chain $chain, ItemInterface $root, $route, $label, $title = '')
    {
        if (count($chain->getPlugins())) {
            $group = $root->addChild($label);
            if ($title) {
                $group->setAttribute('title', $this->container->get('translator')->trans($title));
            }
        }

        // add child items
        foreach ($chain->getPlugins() as $plugin) {
            if ($plugin instanceof CustomMenu) {
                $plugin->buildMenu($group);
            } else {
                $group->addChild($plugin->getTitle(), [
                    'route' => $route,
                    'routeParameters' => ['plugin' => $plugin->getName()]
                ]);
            }
        }

        // if group is empty, remove it
        if (count($chain->getPlugins()) && !count($group)) {
            $root->removeChild($label);
        }
    }

    /**
     * Builder main menu
     * 
     * @param \Knp\Menu\FactoryInterface $factory
     * @param array $options
     *
     * @return 
     */
    public function itemMenu(FactoryInterface $factory, array $options)
    {
        if (empty($options['id']) || empty($options['name'])) {
            throw new \InvalidArgumentException('Unknown element id or name');
        }
        /* @var $menu \Knp\Menu\ItemInterface */
        $menu = $factory->createItem('root');
        $params = ['id' => $options['id'], 'name' => $options['name']];

        // add settings plugin items
        $chain = $this->container->get('anime_db.plugin.item');
        foreach ($chain->getPlugins() as $plugin) {
            $plugin->buildMenu($menu);
        }

        //$menu->addChild('Refill from source'); // TODO issue #38
        //$menu->addChild('Complement directory'); // TODO issue #34
        $menu->addChild('Change record', ['route' => 'item_change', 'routeParameters' => $params])
            ->setLinkAttribute('class', 'change');
        $menu->addChild('Delete record', ['route' => 'item_delete', 'routeParameters' => $params])
            ->setLinkAttribute('class', 'delete');

        return $menu;
    }
}