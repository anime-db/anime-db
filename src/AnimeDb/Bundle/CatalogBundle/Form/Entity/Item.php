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
use AnimeDb\Bundle\AppBundle\Form\Field\Image as ImageField;
use AnimeDb\Bundle\AppBundle\Form\Field\LocalPath as LocalPathField;
use AnimeDb\Bundle\CatalogBundle\Entity\Item as ItemEntity;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Chain;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller;
use Symfony\Component\Templating\EngineInterface as TemplatingInterface;

/**
 * Item form
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Item extends AbstractType
{
    /**
     * Refiller chain
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Chain
     */
    private $chain;

    /**
     * Templating
     *
     * @var \Symfony\Component\Templating\EngineInterface
     */
    private $templating;

    /**
     * Set refiller chain
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Chain $chain
     */
    public function setRefillerChain(Chain $chain)
    {
        $this->chain = $chain;
    }

    /**
     * Set templating
     *
     * @param \Symfony\Component\Templating\EngineInterface $templating
     */
    public function setTemplating(TemplatingInterface $templating)
    {
        $this->templating = $templating;
    }

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
                'label'        => 'Other names',
                'options'      => [
                    'required' => false
                ],
                'attr' => $this->getRefillAttr(Refiller::FIELD_NAMES, $options['data'])
            ])
            ->add('cover', new ImageField(), [
                'required' => false
            ])
            ->add('date_start', 'date', [
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'attr' => $this->getRefillAttr(Refiller::FIELD_DATE_START, $options['data'])
            ])
            ->add('date_end', 'date', [
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'required' => false,
                'attr' => $this->getRefillAttr(Refiller::FIELD_DATE_END, $options['data'])
            ])
            ->add('episodes_number', null, [
                'required' => false,
                'label'    => 'Number of episodes',
                'attr' => $this->getRefillAttr(Refiller::FIELD_EPISODES_NUMBER, $options['data'])
            ])
            ->add('duration', null, [
                'attr' => $this->getRefillAttr(Refiller::FIELD_DURATION, $options['data'])
            ])
            ->add('type', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Type',
                'property' => 'name'
            ])
            ->add('genres', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Genre',
                'property' => 'name',
                'multiple' => true,
                'attr' => $this->getRefillAttr(Refiller::FIELD_GENRES, $options['data'])
            ])
            ->add('manufacturer', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Country',
                'property' => 'name',
                'attr' => $this->getRefillAttr(Refiller::FIELD_MANUFACTURER, $options['data'])
            ])
            ->add('path', new LocalPathField(), [
                'required' => false,
                'attr' => [
                    'placeholder' => $this->getUserHomeDir()
                ]
            ])
            ->add('translate', null, [
                'required' => false,
                'attr' => $this->getRefillAttr(Refiller::FIELD_TRANSLATE, $options['data'])
            ])
            ->add('summary', null, [
                'required' => false,
                'attr' => $this->getRefillAttr(Refiller::FIELD_SUMMARY, $options['data'])
            ])
            ->add('episodes', null, [
                'required' => false,
                'attr' => $this->getRefillAttr(Refiller::FIELD_EPISODES, $options['data'])
            ])
            ->add('file_info', null, [
                'required' => false,
                'attr' => $this->getRefillAttr(Refiller::FIELD_FILE_INFO, $options['data'])
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
                'attr' => $this->getRefillAttr(Refiller::FIELD_SOURCES, $options['data'])
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
                'attr' => $this->getRefillAttr(Refiller::FIELD_IMAGES, $options['data'])
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

    /**
     * Get the field refill attributes
     *
     * @param string $field
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item|null $item
     *
     * @return array
     */
    protected function getRefillAttr($field, ItemEntity $item = null)
    {
        // item exists and can be refilled
        if ($item instanceof ItemEntity && $item->getName() &&
            ($plugins = $this->chain->getPluginsThatCanFillItem($item, $field))
        ) {
            /* @var $plugin \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller */
            foreach ($plugins as $key => $plugin) {
                $plugins[$key] = [
                    'name' => $plugin->getName(),
                    'title' => $plugin->getTitle(),
                    'can_refill' => $plugin->isCanRefill($item, $field) // can refill or can search
                ];
            }

            return [
                'data-type' => 'refill',
                'data-plugins' => $this->templating->render('AnimeDbCatalogBundle:Form:refillers.html.twig', [
                    'item' => $item,
                    'field' => $field,
                    'plugins' => $plugins
                ])
            ];
        }
        return [];
    }
}