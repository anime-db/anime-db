<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
 
namespace AnimeDB\CatalogBundle\Service\Autofill\Filler;

use AnimeDB\CatalogBundle\Service\Autofill\Filler\Filler;
use Buzz\Browser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Autofill from site world-art.ru
 * 
 * @link http://world-art.ru/
 * @package AnimeDB\CatalogBundle\Service\Autofill\Filler
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class WorldArtRu implements Filler
{
    /**
     * Title
     *
     * @var string
     */
    const NAME = 'World-Art.ru';

    /**
     * Filler http host
     *
     * @var string
     */
    const HOST = 'http://www.world-art.ru/';

    /**
     * Path for search
     *
     * @var string
     */
    const SEARH_URL = 'search.php?public_search=#NAME#&global_sector=animation';

    /**
     * XPath for list search items
     *
     * @var string
     */
    const XPATH_FOR_LIST = '//body/table/tr/td/center/table/tr/td/table/tr/td/table/tr/td';

    /**
     * Browser
     *
     * @var \Buzz\Browser
     */
    private $browser;

    /**
     * Construct
     *
     * @param \Buzz\Browser $browser
     */
    public function __construct(Browser $browser)
    {
        $this->browser = $browser;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() {
        return self::NAME;
    }

    /**
     * Search source by name
     *
     * Return structure
     * <code>
     * [
     *     {
     *         'name': string,
     *         'source': string,
     *         'description': string
     *     }
     * ]
     * </code>
     *
     * @param string $name
     *
     * @return array
     */
    public function search($name)
    {
        $url = str_replace('#NAME#', urlencode($name), self::SEARH_URL);
        // get list from xpath
        $crawler = $this->getCrawlerFromUrl(self::HOST.$url)
            ->filterXPath(self::XPATH_FOR_LIST);

        $list = array();
        foreach ($crawler as $el) {
            $elc = new Crawler($el);
            /* @var $link Crawler */
            $link = $elc->filter('a')->first();
            // has link on source
            if ($link->count() && ($href = $link->attr('href')) && ($name = $link->text())) {
                $list[] = array(
                    'name'        => str_replace(array("\r\n", "\n"), ' ', $name),
                    'source'      => self::HOST.$href,
                    'description' => trim(str_replace($name, '', $elc->text())),
                );
            }
        }

        return $list;
    }

    /**
     * Fill item from source
     *
     * @param string $source
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item|null
     */
    public function fill($source)
    {
        if (!$this->isSupportSource($source)) {
            return null;
        }
        $crawler = $this->getCrawlerFromUrl($source);
        // TODO requires the implementation of
        return null;
    }

    /**
     * Filler is support this source
     *
     * @param string $source
     *
     * @return boolean
     */
    public function isSupportSource($source) {
        return is_string($source) && strpos($source, self::HOST) == 0;
    }

    /**
     * Get Crawler from url
     *
     * Receive content from the URL, cleaning using Tidy and creating DOM document
     *
     * @param string $url
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    private function getCrawlerFromUrl($url) {
        // get content
        /* @var $response \Buzz\Message\Response */
        $response = $this->browser->get($url);
        if ($response->getStatusCode() !== 200 || !($html = $response->getContent())) {
            return null;
        }
        $html = iconv('windows-1251', 'utf-8', $html);

        // clean content
        $config = array(
            'output-xhtml' => true,
            'indent' => true,
            'indent-spaces' => 0,
            'fix-backslash' => true,
            'hide-comments' => true,
            'drop-empty-paras' => true,
        );
        $tidy = new \tidy();
        $tidy->ParseString($html, $config, 'utf8');
        $tidy->cleanRepair();
        $html = $tidy->root()->value;
        // remove noembed
        $html = preg_replace('/<noembed>.*?<\/noembed>/is', '', $html);

        // load Crawler
        $crawler = new Crawler();
        $crawler->addHtmlContent($html);
        return $crawler;
    }
}