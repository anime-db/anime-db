<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Search items form
 *
 * @package AnimeDB\Bundle\CatalogBundle\Form
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Search extends AbstractType
{
    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('name', 'search', [
                'label' => 'Name',
                'required' => false
            ])
            ->add('date_start', 'date', [
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('date_end', 'date', [
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('type', 'entity', [
                'class'    => 'AnimeDBCatalogBundle:Type',
                'property' => 'name',
                'required' => false
            ])
            ->add('genres', 'entity', [
                'class'    => 'AnimeDBCatalogBundle:Genre',
                'property' => 'name',
                'multiple' => true,
                'required' => false
            ])
            ->add('manufacturer', 'entity', [
                'class'    => 'AnimeDBCatalogBundle:Country',
                'property' => 'name',
                'required' => false
            ])
            ->add('storage', 'entity', [
                'class'    => 'AnimeDBCatalogBundle:Storage',
                'property' => 'name',
                'required' => false
            ])
            ->add('sort_field', 'choice', [
                'label' => 'Sort by',
                'data' => 'id',
                'choices' => [
                    'date_update' => 'Last updated',
                    'name' => 'Name',
                    'date_start' => 'Date start',
                    'date_end' => 'Date end'
                ]
            ])
            ->add('sort_direction', 'choice', [
                'label' => 'Sort direction',
                'data' => 'DESC',
                'choices' => [
                    'DESC' => 'Descending',
                    'ASC' => 'Ascending',
                ]
            ]);
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'search_items';
    }
}