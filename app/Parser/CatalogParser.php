<?php

namespace App\Parser;

use Symfony\Component\DomCrawler\Crawler;
use App\Product;
use App\Parser\ObjectParser;

class CatalogParser extends Parser
{
    //TODO: использовать эти значения для ограничения парсера
    protected $parse_num;

    protected $update_num;

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
        $html = parent::curl_get($url);

        $crawler = new Crawler($html);

        $category = $crawler->filterXPath('//h1[contains(@class, "oth")]')->text();

        $crawler->filterXPath('//tr[contains(@class, "list-")]')->each(function (Crawler $item) use ($category) {
            $product = Product::createRecord(static::getProductAttributes($item, $category));

            //Parse page with description
            $objectParser = new ObjectParser($product);
            $objectParser->parse(static::getProductUrl($item));

        });

        $nextUrl = static::getNextPageUrl($crawler);
        if ($nextUrl) $this->parse($nextUrl);
    }


    /**
     * Get url of the next page if it exists.
     *
     * @param Crawler $crawler
     * @return string|void
     */
    public static function getNextPageUrl(Crawler $crawler)
    {
        $nextPage = $crawler->filterXPath('//a[@id="pager_next"]');

        if (!$nextPage) return;

        return 'm.ua' . $nextPage->attr('href');
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
        $attributes['price'] = $item->filterXPath('//span[contains(@id, "price_")]')->text();

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

