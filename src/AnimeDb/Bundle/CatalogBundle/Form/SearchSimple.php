<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Search simple form for home page
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class SearchSimple extends AbstractType
{
    /**
     * Autocomplete source
     *
     * @var string|null
     */
    private $source;

    /**
     * Construct
     *
     * @param string|null $source
     */
    public function __construct($source = null)
    {
        $this->source = $source;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'search', [
                'label' => 'Name',
                'required' => false,
                'attr' => $this->source ? ['data-source' => $this->source] : []
            ]);
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'anime_db_catalog_search_items';
    }
}