<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Field\LocalPath;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Local path choice form
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Field\LocalPath
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
        $builder->add('path', 'text', [
            'label' => 'Path',
            'required' => true,
            'attr' => [
                'placeholder' => $this->getUserHomeDir()
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

    /**
     * Get user home dir
     *
     * @return string
     */
    private function getUserHomeDir() {
        if ($home = getenv('HOME')) {
            return $home;
        } elseif (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            return '/home/'.get_current_user();
        } elseif (is_dir($win7path = 'C:\Users'.DIRECTORY_SEPARATOR.get_current_user())) { // is Windows 7 or Vista
            return $win7path;
        } else {
            return 'C:\Documents and Settings'.DIRECTORY_SEPARATOR.get_current_user();
        }
    }
}