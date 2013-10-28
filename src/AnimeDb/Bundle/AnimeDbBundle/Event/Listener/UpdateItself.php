<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Event\Listener;

use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded;

/**
 * Update itself listener
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Event\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class UpdateItself
{
    /**
     * Add requirements in composer.json from old version
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded $event
     */
    public function onAppDownloadedAddComposerRequirements(Downloaded $event)
    {
        $old_config = file_get_contents(__DIR__.'/../../../../../../composer.json');
        $old_config = json_decode($old_config, true);

        $new_config = file_get_contents($event->getPath().'/composer.json');
        $new_config = json_decode($new_config, true);

        $new_config['require'] = array_merge($old_config['require'], $new_config['require']);
        $new_config = json_encode($new_config, JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        file_put_contents($event->getPath().'/composer.json', $new_config);
    }
}