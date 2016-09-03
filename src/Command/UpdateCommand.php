<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\StoreEvents;
use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded;
use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Updated;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Composer;
use AnimeDb\Bundle\AnimeDbBundle\Client\GitHub;
use Composer\Package\Package;
use Composer\IO\ConsoleIO;

class UpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('animedb:update')
            ->setDescription('Update the application');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $composer Composer */
        $composer = $this->getContainer()->get('anime_db.composer');
        $composer->setIO(new ConsoleIO($input, $output, $this->getHelperSet()));

        /* @var $github GitHub */
        $github = $this->getContainer()->get('anime_db.client.github');

        // search tag with new version of application
        $output->writeln('Search for a new version of the application');
        $tag = $github->getLastRelease('anime-db/anime-db');
        $tag['version'] = Composer::getVersionCompatible($tag['name']);
        $current_version = Composer::getVersionCompatible($composer->getRootPackage()->getPrettyVersion());

        // update itself
        if ($tag && version_compare($tag['version'], $current_version) == 1) {
            $this->doUpdateItself($tag, $composer, $output);
        } else {
            $output->writeln('<info>Application has already been updated to the latest version</info>');
        }
        unset($github, $tag, $current_version);

        // update from composer
        if ($composer->getInstaller()->run() === 0) {
            $output->writeln('<info>Update requirements has been completed</info>');
        } else {
            $output->writeln('<error>During updating dependencies error occurred</error>');
        }
        $output->writeln('<info>Updating the application has been completed<info>');
    }

    /**
     * @param array $tag
     * @param Composer $composer
     * @param OutputInterface $output
     */
    protected function doUpdateItself(array $tag, Composer $composer, OutputInterface $output)
    {
        $output->writeln('Discovered a new version of the application: <info>'.$tag['name'].'</info>');

        // create install package
        $package = new Package('anime-db/anime-db', $tag['version'], $tag['name']);
        $package->setDistType('zip');
        $package->setDistUrl($tag['zipball_url']);
        $package->setInstallationSource('dist');

        // download new version
        $target = sys_get_temp_dir().'/anime-db';
        $this->getContainer()->get('filesystem')->remove($target);
        $composer->download($package, $target);

        $new_package = $composer->getPackageFromConfigFile($target.'/composer.json');
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        // notify about downloaded
        $dispatcher->dispatch(
            StoreEvents::DOWNLOADED,
            new Downloaded($target, $new_package, $composer->getRootPackage())
        );

        // rewriting the application files
        $this->rewriting($target);

        // notify about updated
        // event will be sent after the update application components
        $this->getContainer()->get('anime_db.event_dispatcher')
            ->dispatch(StoreEvents::UPDATED, new Updated($new_package));

        $composer->reload();
        $output->writeln('<info>Update itself has been completed</info>');
    }

    /**
     * Rewrite the application files.
     *
     * @param string $from
     */
    protected function rewriting($from)
    {
        $fs = $this->getContainer()->get('filesystem');
        try {
            $fs->remove(
                Finder::create()
                    ->files()
                    ->ignoreUnreadableDirs()
                    ->in($this->getContainer()->getParameter('kernel.root_dir').'/../src')
                    ->in($this->getContainer()->getParameter('kernel.root_dir'))
            );
        } catch (\Exception $e) {
        } // ignore errors during the removal of the old application

        // copy new version
        $fs->mirror(
            $from,
            $this->getContainer()->getParameter('kernel.root_dir').'/../',
            null,
            ['override' => true, 'copy_on_windows' => true]
        );

        // remove downloaded files
        $fs->remove($from);
    }
}
