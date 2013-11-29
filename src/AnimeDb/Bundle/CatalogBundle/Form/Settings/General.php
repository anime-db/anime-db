<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Settings;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain;

/**
 * General settings form
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Settings
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class General extends AbstractType
{
    /**
     * Plugin search chain
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain
     */
    protected $chain;

    /**
     * Construct
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain $chain
     */
    public function __construct(Chain $chain)
    {
        $this->chain = $chain;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $search_choices = ['' => 'No'];
        /* @var $plugin \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search */
        foreach ($this->chain->getPlugins() as $plugin) {
            $search_choices[$plugin->getName()] = $plugin->getTitle();
        }

        $builder
            // TODO temporarily disabled #69
            /* ->add('serial_number', 'text', [
                'label' => 'Serial number'
            ]) */
            ->add('locale', 'locale', [
                'label' => 'Language'
            ])
            ->add('task_scheduler', 'checkbox', [
                'required' => false,
                'label' => 'Task scheduler'
            ])
            ->add('default_search', 'choice', [
                'required' => false,
                'choices' => $search_choices,
                'label' => 'Default search plugin'
            ]);
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'anime_db_catalog_settings_general';
    }
}