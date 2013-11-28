<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;
use Knp\Menu\ItemInterface;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Filler as FillerForm;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Plugin filler
 *
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Filler extends Plugin
{
    /**
     * Router
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router;

    /**
     * Fill item from source
     *
     * @param array $data
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item|null
     */
    abstract public function fill(array $data);

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
            'route' => 'fill_filler',
            'routeParameters' => ['plugin' => $this->getName()]
        ]);
    }

    /**
     * Get form
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Form\Plugin\Filler
     */
    public function getForm()
    {
        return new FillerForm();
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
     * Get link for fill item
     *
     * @throws \LogicException
     *
     * @param mixed $data
     *
     * @return string
     */
    public function getLinkForFill($data)
    {
        if (!($this->router instanceof Router)) {
            throw new \LogicException('Link cannot be built without a Router');
        }

        return $this->router->generate(
            'fill_filler',
            [
                'plugin' => $this->getName(),
                $this->getForm()->getName() => ['url' => $data]
            ]
        );
    }
}