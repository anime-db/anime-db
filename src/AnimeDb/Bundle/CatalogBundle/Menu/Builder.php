<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use AnimeDb\Bundle\CatalogBundle\Plugin\Chain;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;

/**
 * Menu builder
 *
 * @package AnimeDb\Bundle\CatalogBundle\Menu
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
            $this->container->get('anime_db.plugin.search_fill'),
            $add,
            'Search source of filling',
            'Search the source and fill record from it'
        );
        // add filler plugin items
        $this->addPluginItems(
            $this->container->get('anime_db.plugin.filler'),
            $add,
            'Fill from source',
            'Fill record from source (example source is URL)'
        );
        // add import plugin items
        $this->addPluginItems($this->container->get('anime_db.plugin.import'), $add, 'Import items');
        // add settings plugin items
        foreach ($this->container->get('anime_db.plugin.setting')->getPlugins() as $plugin) {
            $plugin->buildMenu($settings);
        }

        // add manually
        $add->addChild('Add manually', ['route' => 'item_add_manually']);
        $settings->addChild('File storages', ['route' => 'storage_list']);
        $settings->addChild('List of notice', ['route' => 'notice_list']);
        $plugins = $settings->addChild('Plugins');
        $settings->addChild('Update', ['route' => 'update']);
        $settings->addChild('General', ['route' => 'home_settings']);

        // plugins
        $plugins->addChild('Installed', ['route' => 'plugin_installed']);
        $plugins->addChild('Store', ['route' => 'plugin_store']);

        return $menu;
    }

    /**
     * Add plugin items in menu
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Service\Plugin\Chain $chain
     * @param \Knp\Menu\ItemInterface $root
     * @param string $label
     * @param string|null $title
     */
    private function addPluginItems(Chain $chain, ItemInterface $root, $label, $title = '')
    {
        if (count($chain->getPlugins())) {
            $group = $root->addChild($label);
            if ($title) {
                $group->setAttribute('title', $this->container->get('translator')->trans($title));
            }
        }

        // add child items
        foreach ($chain->getPlugins() as $plugin) {
            $plugin->buildMenu($group);
        }
    }

    /**
     * Builder main menu
     * 
     * @param \Knp\Menu\FactoryInterface $factory
     * @param array $item
     *
     * @return 
     */
    public function itemMenu(FactoryInterface $factory, array $options)
    {
        if (empty($options['item']) || !($options['item'] instanceof Item)) {
            throw new \InvalidArgumentException('Item is not found');
        }
        /* @var $menu \Knp\Menu\ItemInterface */
        $menu = $factory->createItem('root');
        $params = ['id' => $options['item']->getId(), 'name' => $options['item']->getName()];

        $menu->addChild('Change record', ['route' => 'item_change', 'routeParameters' => $params])
            ->setLinkAttribute('class', 'icon-label icon-edit');

        // add settings plugin items
        $chain = $this->container->get('anime_db.plugin.item');
        /* @var $plugin \AnimeDb\Bundle\CatalogBundle\Plugin\Item\Item */
        foreach ($chain->getPlugins() as $plugin) {
            $plugin->buildMenu($menu, $options['item']);
        }

        $menu->addChild('Delete record', ['route' => 'item_delete', 'routeParameters' => $params])
            ->setLinkAttribute('class', 'icon-label icon-delete')
            ->setLinkAttribute('data-message', $this->container->get('translator')->trans(
                'Are you sure want to delete %name%?',
                ['%name%' => $options['item']->getName()]
            ));

        return $menu;
    }
}