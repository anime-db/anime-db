<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;
use Knp\Menu\ItemInterface;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Search as SearchForm;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Filler as FillerForm;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Plugin search
 *
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Search extends Plugin
{
    /**
     * Router
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router;

    /**
     * Filler
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler
     */
    protected $filler;

    /**
     * Search source by name
     *
     * Return structure
     * <code>
     * [
     *     \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Item
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
            'route' => 'fill_search',
            'routeParameters' => ['plugin' => $this->getName()]
        ]);
    }

    /**
     * Set router
     *
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Get form
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
     * @param \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler $filler
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
        if ($this->filler instanceof Filler) {
            return $this->filler->getLinkForFill($data);
        } else {
            return $this->router->generate(
                'fill_filler',
                [
                    'plugin' => $this->getName(),
                    FillerForm::FORM_NAME => ['url' => $data]
                ]
            );
        }
    }

    /**
     * Get link for search items
     *
     * @param string $name
     *
     * @return string
     */
    public function getLinkForSearch($name)
    {
        return $this->router->generate('fill_search', [
            'plugin' => $this->getName(),
            $this->getForm()->getName().'[name]' => $name
        ]);
    }
}