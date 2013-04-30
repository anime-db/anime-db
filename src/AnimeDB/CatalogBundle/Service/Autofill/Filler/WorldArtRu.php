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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;
use Doctrine\Bundle\DoctrineBundle\Registry;
use AnimeDB\CatalogBundle\Entity\Item;
use AnimeDB\CatalogBundle\Entity\Source;
use AnimeDB\CatalogBundle\Entity\Name;
use AnimeDB\CatalogBundle\Entity\Country;
use AnimeDB\CatalogBundle\Entity\Genre;
use AnimeDB\CatalogBundle\Entity\Type;
use AnimeDB\CatalogBundle\Entity\Image;
use Symfony\Component\HttpFoundation\File\File;

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
     * XPath for fill item
     *
     * @var string
     */
    const XPATH_FOR_FILL = '//body/table/tr/td/center/table[9]/tr/td/table[1]/tr/td';

    /**
     * Browser
     *
     * @var \Buzz\Browser
     */
    private $browser;

    /**
     * Request
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * Doctrine
     *
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    /**
     * World-Art countrys
     *
     * TODO Fill countrys list
     *
     * @var array
     */
    private $countrys = [
        'Россия' => ['RU', 'Russia'],
        'Япония' => ['JP', 'Japan'],
        'США' => ['US', 'United States'],
        'Австралия' => ['AU', 'Australia'],
        'Австрия' => ['AT', 'Austria'],
        'Азербайджан' => ['AZ', 'Azerbaijan'],
        'Албания' => ['AL', 'Albania'],
        'Алжир' => ['DZ', 'Algeria'],
        'Ангола' => ['AO', 'Angola'],
        'Андорра' => ['AD', 'Andorra'],
        'Аргентина' => ['AR', 'Argentina'],
        'Армения' => ['AM', 'Armenia'],
        'Аруба' => ['AW', 'Aruba'],
        'Афганистан' => ['AF', 'Afghanistan'],
        'Беларусь' => ['BY', 'Belarus'],
        'Бельгия' => ['BE', 'Belgium'],
        'Болгария' => ['BG', 'Bulgaria'],
        'Боливия' => ['BO', 'Bolivia'],
        'Бразилия' => ['BR', 'Brazil'],
        'Буркина-Фасо' => ['BF', 'Burkina Faso'],
        'Вануату' => ['VU', 'Vanuatu'],
        'Великобритания' => ['GB', 'United Kingdom'],
        'Венгрия' => ['HU', 'Hungary'],
        'Вьетнам' => ['VN', 'Vietnam'],
        'Германия' => ['DE', 'Germany'],
        'Греция' => ['GR', 'Greece'],
        'Грузия' => ['GE', 'Georgia'],
        'Дания' => ['DK', 'Denmark'],
        'Египет' => ['EG', 'Egypt'],
        'Замбия' => ['ZM', 'Zambia'],
    ];

    /**
     * World-Art genres
     *
     * @var array
     */
    private $genres = [
        'боевик' => 'Action',
        'боевые искусства' => 'Martial arts',
        'вампиры' => 'Vampires',
        'война' => 'War',
        'детектив' => 'Detective',
        'для детей' => 'For children',
        'дзёсэй' => 'Josei',
        'драма' => 'Drama',
        'история' => 'History',
        'киберпанк' => 'Cyberpunk',
        'комедия' => 'Comedy',
        'махо-сёдзё' => 'Mahoe shoujo',
        'меха' => 'Meho',
        'мистерия' => 'Mystery play',
        'мистика' => 'Mysticism',
        'музыкальный' => 'Musical',
        'образовательный' => 'Educational',
        'пародия' => 'Parody',
        'cтимпанк' => 'Steampunk',
        'паропанк' => 'Steampunk',
        'повседневность' => 'Everyday',
        'полиция' => 'Police',
        'постапокалиптика' => 'Apocalyptic fiction',
        'приключения' => 'Adventure',
        'психология' => 'Psychology',
        'романтика' => 'Romance',
        'самурайский боевик' => 'Samurai action',
        'сёдзё' => 'Shoujo',
        'сёдзё-ай' => 'Shoujo-ai',
        'сёнэн' => 'Senen',
        'сёнэн-ай' => 'Senen-ai',
        'сказка' => 'Fable',
        'спорт' => 'Sport',
        'сэйнэн' => 'Senen',
        'триллер' => 'Thriller',
        'школа' => 'School',
        'фантастика' => 'Fantastic',
        'фэнтези' => 'Fantasy',
        'эротика' => 'Erotica',
        'этти' => 'Ettie',
        'ужасы' => 'Horror',
        'хентай' => 'Hentai',
        'юри' => 'Urey',
        'яой' => 'Yaoi',
    ];

    /**
     * World-Art types
     *
     * @var array
     */
    private $types = [
        'ТВ' => ['tv', 'TV'],
        'ТВ-спэшл' => ['speshl', 'TV-speshl'],
        'OVA' => ['ova', 'OVA'],
        'ONA' => ['ona', 'ONA'],
        'OAV' => ['oav', 'OAV'],
        'полнометражный фильм' => ['feature', 'Feature'],
        'короткометражный фильм' => ['featurette', 'Featurette'],
        'музыкальное видео' => ['music', 'Music video'],
        'рекламный ролик' => ['commercial', 'Commercial'],
    ];

    /**
     * Construct
     *
     * @param \Buzz\Browser $browser
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function __construct(Browser $browser, Request $request, Registry $doctrine)
    {
        $this->browser  = $browser;
        $this->request  = $request;
        $this->doctrine = $doctrine;
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

        $list = [];
        foreach ($crawler as $el) {
            $elc = new Crawler($el);
            /* @var $link Crawler */
            $link = $elc->filter('a')->first();
            // has link on source
            if ($link->count() && ($href = $link->attr('href')) && ($name = $link->text())) {
                $list[] = [
                    'name'        => str_replace(["\r\n", "\n"], ' ', $name),
                    'source'      => self::HOST.$href,
                    'description' => trim(str_replace($name, '', $elc->text())),
                ];
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
        $dom = $this->getDomDocumentFromUrl($source);
        if (!($dom instanceof \DOMDocument)) {
            return null;
        }
        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query(self::XPATH_FOR_FILL);

        $item = new Item();

        // add source links
        /* @var $links \DOMNodeList */
        $links = $xpath->query('a', $nodes->item(1));
        for ($i = 0; $i < $links->length; $i++) {
            $link = $this->getAttrAsArray($links->item($i));
            if (strpos($link['href'], 'http://') !== false && strpos($link['href'], self::HOST) === false) {
                $item->addSource((new Source())->setUrl($link['href']));
            }
        }
        /* @var $body \DOMElement */
        $body = $nodes->item(4);

        // add cover
        $item->setCover($this->getCover($xpath, $body));

        // fill main data
        $head = $xpath->query('table[3]/tr[2]/td[3]', $body);
        if (!$head->length) {
            $head = $xpath->query('table[2]/tr[1]/td[3]', $body);
        }
        $this->fillHeadData($item, $xpath, $head->item(0));

        // fill body data
        $this->fillBodyData($item, $xpath, $body);

        // add source link on world-art
        $item->addSource((new Source())->setUrl($source));
        return $item;
    }

    /**
     * Get element attributes as array
     *
     * @param \DOMElement $element
     *
     * @return array
     */
    private function getAttrAsArray(\DOMElement $element) {
        $return = [];
        for ($i = 0; $i < $element->attributes->length; ++$i) {
            $return[$element->attributes->item($i)->nodeName] = $element->attributes->item($i)->nodeValue;
        }
        return $return;
    }

    /**
     * Get cover from source
     *
     * @param \DOMXPath $xpath
     * @param \DOMElement $body
     *
     * @return string|null
     */
    private function getCover(\DOMXPath $xpath, \DOMElement $body) {
        $imgs = $xpath->query('table/tr/td//img', $body);
        if ($imgs->length) {
            $src = $this->getAttrAsArray($imgs->item(0))['src'];
            if (strpos($src, 'http://') === false) {
                $src = self::HOST.'animation/'.$src;
            }
            return $this->uploadImage($src);
        }
        return null;
    }

    /**
     * Fill head data
     *
     * @param \AnimeDB\CatalogBundle\Entity\Item $item
     * @param \DOMXPath $xpath
     * @param \DOMElement $head
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    private function fillHeadData(Item $item, \DOMXPath $xpath, \DOMElement $head) {
        // add main name
        $name = $xpath->query('font[1]/b', $head)->item(0)->nodeValue;
        $name = trim(str_replace('[ТВ]', '', $name), ' [');
        $item->setName($name);

        // find other names
        foreach ($head->childNodes as $node) {
            if ($node->nodeName == '#text' && trim($node->nodeValue)) {
                $name = trim(preg_replace('/(\(\d+\))?/', '', $node->nodeValue));
                $item->addName((new Name())->setName($name));
            }
        }

        /* @var $data \DOMElement */
        $data = $xpath->query('font[2]', $head)->item(0);
        $length = $data->childNodes->length;
        for ($i = 0; $i < $length; $i++) {
            if ($data->childNodes->item($i)->nodeName == 'b') {
                switch ($data->childNodes->item($i)->nodeValue) {
                    // set manufacturer
                    case 'Производство':
                        $j = 1;
                        do {
                            if ($data->childNodes->item($i+$j)->nodeName == 'img') {
                                $country_name = trim($data->childNodes->item($i+$j+1)->nodeValue);
                                if ($country_name && $country = $this->getCountryByName($country_name)) {
                                    $item->setManufacturer($country);
                                }
                                break;
                            }
                            $j++;
                        } while ($data->childNodes->item($i+$j)->nodeName != 'br');
                        $i += $j;
                        break;
                    // add genre
                    case 'Жанр':
                        $j = 2;
                        do {
                            if ($data->childNodes->item($i+$j)->nodeName == 'a' &&
                                ($genre = $this->getGenreByName($data->childNodes->item($i+$j)->nodeValue))
                            ) {
                                $item->addGenre($genre);
                            }
                            $j++;
                        } while ($data->childNodes->item($i+$j)->nodeName != 'br');
                        $i += $j;
                        break;
                    // set type and add file info
                    case 'Тип':
                        $type = $data->childNodes->item($i+1)->nodeValue;
                        if (preg_match('/(?<type>\w+) (?:\((?<file_info>.+)\))?, (?<duration>\d{1,3}) мин\.$/u', $type, $match)) {
                            // add type
                            if ($type = $this->getTypeByName($match['type'])) {
                                $item->setType($type);
                            }
                            // add duration
                            $item->setDuration((int)$match['duration']);
                            // add file info
                            if (!empty($match['file_info'])) {
                                $file_info = $item->getFileInfo();
                                $item->setFileInfo(($file_info ? $file_info."\n" : '').$match['file_info']);
                            }
                        }
                        $i++;
                        break;
                    // set date start and date end if exists
                    case 'Премьера':
                    case 'Выпуск':
                        $j = 1;
                        $date = '';
                        do {
                            $date .= $data->childNodes->item($i+$j)->nodeValue;
                            $j++;
                        } while ($length > $i+$j && $data->childNodes->item($i+$j)->nodeName != 'br');
                        $i += $j;

                        $reg = '/(?<start>(?:(?:\d{2})|(?:\?\?)).\d{2}.\d{4})'.
                            '(?:.*(?<end>(?:(?:\d{2})|(?:\?\?)).\d{2}.\d{4}))?/';
                        if (preg_match($reg, $date, $match)) {
                            $item->setDateStart(new \DateTime(str_replace('??', '01', $match['start'])));
                            if (isset($match['end'])) {
                                $item->setDateEnd(new \DateTime($match['end']));
                            }
                        }
                        break;
                }
            }
        }
    }
    
    /**
     * Fill body data
     *
     * @param \AnimeDB\CatalogBundle\Entity\Item $item
     * @param \DOMXPath $xpath
     * @param \DOMElement $body
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    private function fillBodyData(Item $item, \DOMXPath $xpath, \DOMElement $body) {
        for ($i = 0; $i < $body->childNodes->length; $i++) {
            if ($value = trim($body->childNodes->item($i)->nodeValue)) {
                switch ($value) {
                    // get summary
                    case 'Краткое содержание:':
                        $summary = $xpath->query('tr/td/p[1]', $body->childNodes->item($i+2));
                        if ($summary->length) {
                            $item->setSummary(trim($summary->item(0)->nodeValue));
                        }
                        $i += 2;
                        break;
                    // get episodes
                    case 'Эпизоды:':
                        if (!trim($body->childNodes->item($i+1)->nodeValue)) { // simple list
                            $item->setEpisodes(trim($body->childNodes->item($i+2)->nodeValue));
                            $i += 2;
                        } else { // episodes in table
                            $rows = $xpath->query('tr/td[2]', $body->childNodes->item($i+1));
                            $episodes = '';
                            for ($j = 1; $j < $rows->length; $j++) {
                                $episode = $xpath->query('font', $rows->item($j));
                                $episodes .= $j.'. '.$episode->item(0)->nodeValue;
                                if ($rows->length > 1) {
                                    $episodes .= ' ('.$episode->item(1)->nodeValue.')';
                                }
                                $episodes .= "\n";
                            }
                            $item->setEpisodes($episodes);
                            $i++;
                        }
                        break;
                    default:
                        // find other images
                        $images = $xpath->query('table[2]/tr[3]/td[3]/a', $body->childNodes->item($i));
                        foreach ($images as $image) {
                            $crawler = $this->getCrawlerFromUrl($this->getAttrAsArray($image)['href']);
                            $images = $crawler->filter('table table table table table img');
                            foreach ($images as $image) {
                                $src = $this->getAttrAsArray($image)['src'];
                                $src = str_replace('optimize_b', 'optimize_d', $src);
                                if (strpos($src, 'http://') === false) {
                                    $src = self::HOST.'animation/'.$src;
                                }
                                $item->addImage((new Image())->setSource($this->uploadImage($src)));
                            }
                        }
                }
            }
        }
    }

    /**
     * Upload image from url
     *
     * @param string $url
     *
     * @return string
     */
    private function uploadImage($url) {
        // TODO correct upload images
        // training directory
        $root = realpath(__DIR__.'/../../../../../../web/media');
        $path = $root.date('/Y/m/');
        $this->mkdir($path);
        // create file name
        $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        $dest = $this->createFileName($path, $ext);
        // upload
        copy($url, $dest);
        // return relative path
        return str_replace($root.'/', '', $dest);
    }

    /**
     * Create unique file name
     *
     * @param string $path
     * @param string $ext
     *
     * @return string
     */
    private function createFileName($path, $ext) {
        do {
            $file_name = uniqid();
        } while (file_exists($path.$file_name.'.'.$ext));
        return $path.$file_name.'.'.$ext;
    }

	/**
	 * Create a directory
	 * 
	 * Creates the directory and the full path to it if it does not exist
	 *
	 * @throws \Exception
	 *
	 * @param string  $directory
	 * @param integer $mask
	 */
	private function mkdir($directory, $mask = 0755) {
		if (is_dir($directory) || !$directory) {
			return;
		}
		$directory = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $directory);
		if (is_file($directory)) {
			throw new \Exception('mkdir() file exists');
		}

		// access mask must contain a flag designs for access to it
		if (($mask & 0002) === 0002 || ($mask & 0004) === 0004) { // other
			$mask = $mask | 0001;
		}
		if (($mask & 0020) === 0020 || ($mask & 0040) === 0040) { // group
			$mask = $mask | 0010;
		}
		if (($mask & 0200) === 0200 || ($mask & 0400) === 0400) { // user
			$mask = $mask | 0100;
		}

		$names = explode(DIRECTORY_SEPARATOR, $directory);
		$dir = DIRECTORY_SEPARATOR;
		for ($i=0; $i < count($names); $i++) {
			if ($names[$i]) { // can come to an empty string
				$dir .= $names[$i].DIRECTORY_SEPARATOR;
				if (!is_dir($dir)) {
					$old_umask = umask(0);
					@mkdir($dir, $mask);
					umask($old_umask);
					if (!is_dir($dir)) {
						throw new \Exception('Can`t create a folder '.$dir);
					}
				}
			}
		}
	}

    /**
     * Get real country by name
     *
     * @param integer $id
     *
     * @return \AnimeDB\CatalogBundle\Entity\Country|null
     */
    private function getCountryByName($name) {
        if (isset($this->countrys[$name])) {
            return (new Country())
                ->setId($this->countrys[$name][0])
                ->setName($this->countrys[$name][1]);
        }
        return null;
    }

    /**
     * Get real genre by name
     *
     * @param string $name
     *
     * @return \AnimeDB\CatalogBundle\Entity\Genre
     */
    private function getGenreByName($name) {
        if (isset($this->genres[$name])) {
            return $this->doctrine
                ->getRepository('AnimeDBCatalogBundle:Genre')
                ->findOneByName($this->genres[$name]);
        }
        return null;
    }

    /**
     * Get real type by name
     *
     * @param string $name
     *
     * @return \AnimeDB\CatalogBundle\Entity\Type
     */
    private function getTypeByName($name) {
        if (isset($this->types[$name])) {
            return (new Type())
                ->setId($this->types[$name][0])
                ->setName($this->types[$name][1]);
        }
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
        return is_string($source) && strpos($source, self::HOST) === 0;
    }

    /**
     * Get Crawler from url
     *
     * Receive content from the URL, cleaning using Tidy and creating Crawler
     *
     * @param string $url
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    private function getCrawlerFromUrl($url) {
        $crawler = new Crawler();
        $crawler->addHtmlContent($this->getContentFromUrl($url));
        return $crawler;
    }

    /**
     * Get DOMDocument from url
     *
     * Receive content from the URL, cleaning using Tidy and creating DOM document
     *
     * @param string $url
     *
     * @return \DOMDocument|null
     */
    private function getDomDocumentFromUrl($url) {
        $dom = new \DOMDocument('1.0', 'utf8');
        if (($content = $this->getContentFromUrl($url)) && $dom->loadHTML($content)) {
            return $dom;
        } else {
            return null;
        }
    }

    /**
     * Get content from url
     *
     * Receive content from the URL and cleaning using Tidy
     *
     * @param string $url
     */
    private function getContentFromUrl($url) {
        // send request from the original user
        $headers = $this->request->server->getHeaders();
        unset($headers['HOST'], $headers['COOKIE'], $headers['HTTP_REFERER']);
        /* @var $response \Buzz\Message\Response */
        $response = $this->browser->get($url, $headers);
        if ($response->getStatusCode() !== 200 || !($html = $response->getContent())) {
            return null;
        }
        $html = iconv('windows-1251', 'utf-8', $html);

        // clean content
        $config = [
            'output-xhtml' => true,
            'indent' => true,
            'indent-spaces' => 0,
            'fix-backslash' => true,
            'hide-comments' => true,
            'drop-empty-paras' => true,
        ];
        $tidy = new \tidy();
        $tidy->ParseString($html, $config, 'utf8');
        $tidy->cleanRepair();
        $html = $tidy->root()->value;
        // remove noembed
        $content = preg_replace('/<noembed>.*?<\/noembed>/is', '', $html);
        return $content;
    }
}