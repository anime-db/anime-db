<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Form\Field\LocalPath;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Local path choice form
 *
 * @package AnimeDB\Bundle\CatalogBundle\Form\Field\LocalPath
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Choice extends AbstractType
{
    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $placeholder = 'C:\Documents and Settings\\'.get_current_user().'\My Documents\\';
        } else {
            $placeholder = '/home/'.get_current_user().'/';
        }
        $builder->add('path', 'text', [
            'label' => 'Path',
            'required' => true,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ]);
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'local_path_popup';
    }
}