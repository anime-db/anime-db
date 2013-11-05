<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Entity;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AnimeDb\Bundle\CatalogBundle\Form\Entity\Image;
use AnimeDb\Bundle\CatalogBundle\Form\Entity\Name;
use AnimeDb\Bundle\CatalogBundle\Form\Entity\Source;
use AnimeDb\Bundle\CatalogBundle\Form\Field\Image as ImageField;
use AnimeDb\Bundle\CatalogBundle\Form\Field\LocalPath as LocalPathField;

/**
 * Item form
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Item extends AbstractType
{
    /**
     * Can refill item
     *
     * @var boolean
     */
    private $refillable = false;

    /**
     * Construct
     *
     * @param boolean $refillable
     */
    public function __construct($refillable = false)
    {
        $this->refillable = $refillable;
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attr = $this->refillable ? ['data-type' => 'refill'] : [];
        $builder
            ->add('name', null, [
                'label' => 'Main name'
            ])
            ->add('names', 'collection', [
                'type'         => new Name(),
                'allow_add'    => true,
                'by_reference' => false,
                'allow_delete' => true,
                'label'        => 'Other names',
                'options'      => [
                    'required' => false
                ],
                'attr' => $attr
            ])
            ->add('cover', new ImageField(), [
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
            ->add('episodes_number', null, [
                'required' => false,
                'label'    => 'Number of episodes',
            ])
            ->add('duration')
            ->add('type', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Type',
                'property' => 'name'
            ])
            ->add('genres', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Genre',
                'property' => 'name',
                'multiple' => true,
                'attr' => $attr
            ])
            ->add('manufacturer', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Country',
                'property' => 'name'
            ])
            ->add('path', new LocalPathField(), [
                'required' => false,
                'attr' => [
                    'placeholder' => $this->getUserHomeDir()
                ]
            ])
            ->add('translate', null, [
                'required' => false
            ])
            ->add('summary', null, [
                'required' => false,
                'attr' => $attr
            ])
            ->add('episodes', null, [
                'required' => false,
                'attr' => $attr
            ])
            ->add('file_info', null, [
                'required' => false
            ])
            ->add('storage', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Storage',
                'property' => 'name',
            ])
            ->add('sources', 'collection', [
                'type'         => new Source(),
                'allow_add'    => true,
                'by_reference' => false,
                'allow_delete' => true,
                'label'        => 'External sources',
                'options'      => [
                    'required' => false
                ],
            ])
            ->add('images', 'collection', [
                'type'         => new Image(),
                'allow_add'    => true,
                'by_reference' => false,
                'allow_delete' => true,
                'label'        => 'Other images',
                'options'      => [
                    'required' => false
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
            'data_class' => 'AnimeDb\Bundle\CatalogBundle\Entity\Item'
        ]);
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'anime_db_catalog_entity_item';
    }

    /**
     * Get user home dir
     *
     * @return string
     */
    protected function getUserHomeDir() {
        if ($home = getenv('HOME')) {
            $last = substr($home, strlen($home), 1);
            if ($last == '/' || $last == '\\') {
                return $home;
            } else {
                return $home.DIRECTORY_SEPARATOR;
            }
        } elseif (!defined('PHP_WINDOWS_VERSION_BUILD')) {
            return '/home/'.get_current_user().'/';
        } elseif (is_dir($win7path = 'C:\Users\\'.get_current_user().'\\')) { // is Windows 7 or Vista
            return $win7path;
        } else {
            return 'C:\Documents and Settings\\'.get_current_user().'\\';
        }
    }
}