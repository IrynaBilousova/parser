<?php

namespace App\Parser;

use Symfony\Component\DomCrawler\Crawler;
use App\Product;
use App\Parser\ObjectParser;

class CatalogParser extends Parser
{

    protected $parse_num;

    protected $category;

    protected $update_num;

    protected $parsed = 0;

    protected $updated = 0;

    public function __construct($parse_num, $update_num)
    {
        $this->parse_num = $parse_num;
        $this->update_num = $update_num;
    }

    /**
     * Parse all data from given category url for each product.
     * Then go to each product`s description page and parse properties.
     * If there is next page, repeat again.
     *
     * @var string
     */
    public function parse($url)
    {
        if ($this->parsed < $this->parse_num) {

            $html = parent::curl_get($url);

            $crawler = new Crawler($html);

            if (!$this->category) $this->category = $crawler->filterXPath('//h1')->text();

            $crawler->filterXPath('//tr[contains(@class, "list-")]')->each(function (Crawler $item) {
                if ($this->parsed < $this->parse_num) {
                    $can_update = $this->updated < $this->update_num;
                    $results = Product::manageRecords(static::getProductAttributes($item, $this->category), $can_update);
                    $product = $results['product'];

                    $this->updateCounter($results['status']);

                    //Parse page with description
                    if ($product) {
                        $objectParser = new ObjectParser($product);
                        $objectParser->parse(static::getProductUrl($item));
                    }
                } else {
                    return;
                }
            });

            $nextUrl = static::getNextPageUrl($crawler);
            if ($nextUrl) $this->parse($nextUrl);
        }
    }

    /**
     * Count parsed and updated items.
     *
     * @string $status
     */
    public  function updateCounter($status)
    {
        if ($status === 'created') {
            $this->parsed += 1;
        } elseif ($status === 'updated') {
            $this->updated += 1;
        }
    }
    /**
     * Get url of the next page if it exists.
     *
     * @param Crawler $crawler
     * @return string|void
     */
    public static function getNextPageUrl(Crawler $crawler)
    {
        $nextPage = $crawler->filterXPath('//*[@id="pager_next"]');

       try{
           return 'm.ua' . $nextPage->attr('href');
       } catch (\InvalidArgumentException $exception) {
           return;
       }
    }

    /**
     * Get all product attributes to create a new record.
     *
     * @param Crawler $item
     * @return mixed
     */
    public static function getProductAttributes(Crawler $item, $category)
    {
        $attributes['category'] = $category;
        $attributes['name'] = $item->filterXPath('//div[contains(@class, "list-model-title")]')->first()->text();

        try
        {
            $attributes['price'] = $item->filterXPath('//span[contains(@id, "price_")]')->text();
        } catch (\Exception $exception) {
           $attributes['price'] = $item->filterXPath('//div[@class="hotprices-div"]')->filter('span')->first()->text();
        }


        $descNode = $item->filterXPath('//div[contains(@class, "list-model-desc")]');
        if ($descNode) $attributes['description'] = $descNode->text();

        return $attributes;
    }

    /**
     * Get url of page with product description.
     *
     * @var Crawler
     * @return string
     */
    public static function getProductUrl(Crawler $item)
    {
        $url = $item->filterXPath('//div[contains(@class, "list-model-title")]')
            ->first()
            ->filter('a')
            ->attr('href');

        return 'http://m.ua' . $url;
    }
}

