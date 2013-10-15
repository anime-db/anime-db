<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Plugin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Search item for filler
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Plugin
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Search extends AbstractType
{
    /**
     * Form name
     *
     * @var string
     */
    const FORM_NAME = 'anime_db_catalog_plugin_search';

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('name', 'text', [
                'label' => 'Name',
                'attr' => [
                    'placeholder' => 'One Piece',
                ],
            ]);
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return self::FORM_NAME;
    }
}