<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Search;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;
use Knp\Menu\ItemInterface;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Search as SearchForm;
use AnimeDb\Bundle\CatalogBundle\Plugin\Filler\Filler;

/**
 * Plugin search
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Search
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Search extends Plugin
{
    /**
     * Filler
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Plugin\Filler\Filler
     */
    protected $filler;

    /**
     * Search source by name
     *
     * Return structure
     * <code>
     * [
     *     \AnimeDb\Bundle\CatalogBundle\Plugin\Search\Item
     * ]
     * </code>
     *
     * @param array $data
     *
     * @return array
     */
    abstract public function search(array $data);

    /**
     * Build menu for plugin
     *
     * @param \Knp\Menu\ItemInterface $item
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function buildMenu(ItemInterface $item)
    {
        $item->addChild($this->getTitle(), [
            'route' => 'item_search',
            'routeParameters' => ['plugin' => $this->getName()]
        ]);
    }

    /**
     * Get form
     *
     * Form must contain the "name" field to enter the name of the desired item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Form\Plugin\Search
     */
    public function getForm()
    {
        return new SearchForm();
    }

    /**
     * Set filler
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Plugin\Filler\Filler $filler
     */
    public function setFiller(Filler $filler)
    {
        $this->filler = $filler;
    }

    /**
     * Get link for fill item
     *
     * @param mixed $data
     *
     * @return string
     */
    public function getLinkForFill($data)
    {
        if (!($this->filler instanceof Filler)) {
            throw new \LogicException('Link cannot be built without a Filler');
        }
        return $this->filler->getLinkForFill($data);
    }
}