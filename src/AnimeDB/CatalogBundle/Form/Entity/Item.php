<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\CatalogBundle\Form\Entity;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AnimeDB\CatalogBundle\Form\Entity\Image;
use AnimeDB\CatalogBundle\Form\Entity\Name;
use AnimeDB\CatalogBundle\Form\Entity\Source;

/**
 * Item form
 *
 * @package AnimeDB\CatalogBundle\Form\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Item extends AbstractType
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Main name'
            ])
            ->add('names', 'collection', [
                'type'         => new Name(),
                'allow_add'    => true,
                'by_reference' => false,
                'allow_delete' => true,
                'attr'         => ['class' => 'b-col-r'],
                'label'        => 'Other names',
                'options'      => [
                    'required' => false,
                    'attr'     => ['class' => 'b-col-i']
                ],
            ])
            // TODO do something with downloading images from an url
            ->add('cover', 'text', [
//                 'property_path' => 'WebPath',
                'required' => false
            ])
            ->add('date_start', 'date', [
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
            ])
            ->add('date_end', 'date', [
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('duration')
            ->add('images', 'collection', [
                'type'         => new Image(),
                'allow_add'    => true,
                'by_reference' => false,
                'allow_delete' => true,
                'label'        => 'Other images',
                'attr'         => ['class' => 'b-col-r'],
                'options'      => [
                    'required' => false,
                    'attr'     => ['class' => 'b-col-i'],
                ],
            ])
            ->add('type', 'entity', [
                'class'    => 'AnimeDBCatalogBundle:Type',
                'property' => 'name'
            ])
            ->add('genres', 'entity', [
                'class'    => 'AnimeDBCatalogBundle:Genre',
                'property' => 'name',
                'multiple' => true
            ])
            ->add('manufacturer', 'entity', [
                'class'    => 'AnimeDBCatalogBundle:Country',
                'property' => 'name'
            ])
            ->add('path')
            ->add('translate', null, [
                'required' => false
            ])
            ->add('summary', null, [
                'required' => false
            ])
            ->add('episodes', null, [
                'required' => false
            ])
            ->add('file_info', null, [
                'required' => false
            ])
            ->add('storage', 'entity', [
                'class'    => 'AnimeDBCatalogBundle:Storage',
                'property' => 'name',
            ])
            ->add('sources', 'collection', [
                'type'         => new Source(),
                'allow_add'    => true,
                'by_reference' => false,
                'allow_delete' => true,
                'attr'         => ['class' => 'b-col-r'],
                'label'        => 'External sources',
                'options'      => [
                    'required' => false,
                    'attr'     => ['class' => 'b-col-i']
                ],
            ])
        ;
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::setDefaultOptions()
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AnimeDB\CatalogBundle\Entity\Item'
        ]);
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