<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
 
namespace AnimeDB\Bundle\CatalogBundle\Service\Autofill\Filler;

use AnimeDB\Bundle\CatalogBundle\Service\Autofill\Filler\Filler;
use Buzz\Browser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;
use Doctrine\Bundle\DoctrineBundle\Registry;
use AnimeDB\Bundle\CatalogBundle\Entity\Item;
use AnimeDB\Bundle\CatalogBundle\Entity\Source;
use AnimeDB\Bundle\CatalogBundle\Entity\Name;
use AnimeDB\Bundle\CatalogBundle\Entity\Country;
use AnimeDB\Bundle\CatalogBundle\Entity\Genre;
use AnimeDB\Bundle\CatalogBundle\Entity\Type;
use AnimeDB\Bundle\CatalogBundle\Entity\Image;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;
use AnimeDB\Bundle\CatalogBundle\Entity\Field\Image as ImageField;
use Symfony\Component\Validator\Validator;

/**
 * Autofill from site world-art.ru
 * 
 * @link http://world-art.ru/
 * @package AnimeDB\Bundle\CatalogBundle\Service\Autofill\Filler
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
    const XPATH_FOR_LIST = '//center/table/tr/td/table/tr/td/table/tr/td';

    /**
     * XPath for fill item
     *
     * @var string
     */
    const XPATH_FOR_FILL = '//center/table[@height="58%"]/tr/td/table[1]/tr/td';

    /**
     * Default HTTP User-Agent
     *
     * @var string
     */
    const DEFAULT_USER_AGENT = 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.2 Safari/537.36';

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
     * Validator
     *
     * @var \Symfony\Component\Validator\Validator
     */
    private $validator;

    /**
     * Filesystem
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $fs;

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
     * @param \Symfony\Component\Validator\Validator $validator
     */
    public function __construct(
        Browser $browser,
        Request $request,
        Registry $doctrine,
        Validator $validator
    ) {
        $this->browser  = $browser;
        $this->request  = $request;
        $this->doctrine = $doctrine;
        $this->validator = $validator;
        $this->fs = new Filesystem();
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
        $name = iconv('utf-8', 'cp1251', $name);
        $url = str_replace('#NAME#', urlencode($name), self::SEARH_URL);
        // get list from xpath
        $dom = $this->getDomDocumentFromUrl(self::HOST.$url);
        $xpath = new \DOMXPath($dom);

        // if for request is found only one result is produced forwarding
        $refresh = $xpath->query('//meta[@http-equiv="Refresh"]/@content');
        if ($refresh->length) {
            list(, $url) = explode('url=', $refresh->item(0)->nodeValue, 2);
            // add http if need
            if ($url[0] == '/') {
                $url = self::HOST.substr($url, 1);
            }
            return [
                [
                    'name'        => iconv('cp1251', 'utf-8', $name),
                    'source'      => $url,
                    'description' => '',
                ]
            ];
        }

        $rows = $xpath->query(self::XPATH_FOR_LIST);

        $list = [];
        foreach ($rows as $el) {
            $link = $xpath->query('a', $el);
            // has link on source
            if ($link->length &&
                ($href = $link->item(0)->getAttribute('href')) &&
                ($name = $link->item(0)->nodeValue)
            ) {
                $list[] = [
                    'name'        => str_replace(["\r\n", "\n"], ' ', $name),
                    'source'      => self::HOST.$href,
                    'description' => trim(str_replace($name, '', $el->nodeValue)),
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
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item|null
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

        // add source link on world-art
        $item->addSource((new Source())->setUrl($source));

        // add other source links
        /* @var $links \DOMNodeList */
        $links = $xpath->query('a', $nodes->item(1));
        for ($i = 0; $i < $links->length; $i++) {
            $link = $this->getAttrAsArray($links->item($i));
            if (strpos($link['href'], 'http://') !== false && strpos($link['href'], self::HOST) === false) {
                $item->addSource((new Source())->setUrl($link['href']));
            }
        }
        /* @var $body \DOMElement */
        if (!($body = $nodes->item(4))) {
            throw new \LogicException('Incorrect data structure at source');
        }

        // add cover
        $item->setCover($this->getCover($xpath, $body));

        // fill main data
        $head = $xpath->query('table[3]/tr[2]/td[3]', $body);
        if (!$head->length) {
            $head = $xpath->query('table[2]/tr[1]/td[3]', $body);
        }
        $this->fillHeadData($item, $xpath, $head->item(0));

        // fill body data
        $this->fillBodyData($item, $xpath, $body, $source);
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
            if (preg_match('/\/(?<id>\d+)\/(?<file>\d+\.(?:jpe?g|png|gif))$/', $src, $mat)) {
                return $this->uploadImage($src, $mat['id'].'/'.$mat['file']);
            }
        }
        return null;
    }

    /**
     * Fill head data
     *
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Item $item
     * @param \DOMXPath $xpath
     * @param \DOMElement $head
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
     */
    private function fillHeadData(Item $item, \DOMXPath $xpath, \DOMElement $head) {
        // add main name
        $name = $xpath->query('font[1]/b', $head)->item(0)->nodeValue;
        // clear
        $name = preg_replace('/\[?(ТВ|OVA|ONA)(\-\d)?\]?/', '', $name); // example: [TV-1]
        $name = preg_replace('/\(фильм \w+\)/u', '', $name); // example: (фильм седьмой)
        $name = trim($name, " [\r\n\t"); // clear trash
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
                        if (preg_match('/(?<type>[\w\s]+)(?: \((?<file_info>.+)\))?, (?<duration>\d{1,3}) мин\.$/u', $type, $match)) {
                            // add type
                            if ($type = $this->getTypeByName(trim($match['type']))) {
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
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Item $item
     * @param \DOMXPath $xpath
     * @param \DOMElement $body
     * @param string $source
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
     */
    private function fillBodyData(Item $item, \DOMXPath $xpath, \DOMElement $body, $source) {
        // id from source
        $id = 0;
        if (preg_match('/id=(?<id>\d+)/', $source, $mat)) {
            $id = $mat['id'];
        }

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
                        // get frames
                        if (strpos($value, 'кадры из аниме') !== false && $id) {
                            $crawler = $this->getCrawlerFromUrl(self::HOST.'animation/animation_photos.php?id='.$id);
                            $images = $crawler->filter('table table table img');
                            foreach ($images as $image) {
                                $src = $this->getAttrAsArray($image)['src'];
                                $src = str_replace('optimize_b', 'optimize_d', $src);
                                if (strpos($src, 'http://') === false) {
                                    $src = self::HOST.'animation/'.$src;
                                }
                                if (preg_match('/\-(?<image>\d+)\-optimize_d(?<ext>\.jpe?g|png|gif)/', $src, $mat) &&
                                    $src = $this->uploadImage($src, $id.'/'.$mat['image'].$mat['ext'])
                                ) {
                                    $item->addImage((new Image())->setSource($src));
                                }
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
     * @param string|null $target
     *
     * @return string
     */
    private function uploadImage($url, $target = null) {
        $image = new ImageField();
        $image->setRemote($url);
        $image->upload($this->validator, $target);
        return $image->getPath();
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
     * Get real country by name
     *
     * @param integer $id
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Country|null
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
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Genre
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
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Type
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
        // send headers from original request
        $headers = [
            'User-Agent' => $this->request->server->get('HTTP_USER_AGENT', self::DEFAULT_USER_AGENT)
        ];
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
        // ignore blocks
        $html = preg_replace('/<noembed>.*?<\/noembed>/is', '', $html);
        $html = preg_replace('/<noindex>.*?<\/noindex>/is', '', $html);
        // remove noembed
        return $html;
    }
}