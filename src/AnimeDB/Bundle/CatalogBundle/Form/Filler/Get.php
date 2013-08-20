<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Form\Filler;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Get item from filler
 *
 * @deprecated Use plugins
 *
 * @package AnimeDB\Bundle\CatalogBundle\Form\Filler
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Get extends AbstractType
{
    /**
     * Filler titles
     *
     * @var array
     */
    private $filler_titles = [];

    /**
     * Construct
     *
     * @param array $filler_titles Filler titles
     */
    public function __construct(array $filler_titles)
    {
        $this->filler_titles = $filler_titles;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('url', 'text', [
                'label' => 'URL address',
                'attr' => [
                    'placeholder' => 'http://',
                ],
            ])
            ->add('filler', 'choice', [
                'label' => 'Source',
                'choices' => $this->filler_titles
            ]);
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'filler_get';
    }
}