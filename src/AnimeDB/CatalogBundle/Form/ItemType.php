<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\CatalogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AnimeDB\CatalogBundle\Form\ImageType;
use AnimeDB\CatalogBundle\Form\NameType;
use AnimeDB\CatalogBundle\Form\SourceType;

/**
 * Item form
 *
 * @package AnimeDB\CatalogBundle\Form
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ItemType extends AbstractType
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('names', new NameType())
            ->add('names', 'collection', array(
                'type' => new NameType(),
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,
                'attr'     => array('class' => 'b-col-r'),
                'options'  => array(
                    'required'  => false,
                    'attr'      => array('class' => 'b-col-i')
                ),
            ))
            ->add('date_start')
            ->add('date_end')
            ->add('duration')
            ->add('image')
            ->add('images', 'collection', array(
                'type' => new ImageType(),
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,
                'attr'     => array('class' => 'b-col-r'),
                'options'  => array(
                    'required'  => false,
                    'attr'      => array('class' => 'b-col-i')
                ),
            ))
            ->add('type')
            ->add('genres')
            ->add('production')
            ->add('path')
            ->add('translate')
            ->add('summary')
            ->add('episodes')
            ->add('file_info')
            ->add('storage')
            ->add('sources', 'collection', array(
                'type' => new SourceType(),
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,
                'attr'     => array('class' => 'b-col-r'),
                'options'  => array(
                    'required'  => false,
                    'attr'      => array('class' => 'b-col-i')
                ),
            ))
        ;
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::setDefaultOptions()
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AnimeDB\CatalogBundle\Entity\Item'
        ));
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'animedb_catalogbundle_itemtype';
    }
}