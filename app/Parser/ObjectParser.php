<?php

namespace App\Parser;

use Symfony\Component\DomCrawler\Crawler;
use App\ProductProperty;
use App\Product;

class ObjectParser extends Parser
{
    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Parse additional data from a page with a single product
     * for `product_properties` table.
     *
     * @var string
     */

    public function parse($url)
    {
        $html = parent::curl_get($url);

        $crawler = new Crawler($html);

        $crawler->filterXPath('//div[@class="short-desc"]/table/tr')
            ->each(function (Crawler $node) {
                $attributes['name'] = $node->filterXPath('//td[@class="prop"]')->text();
                $attributes['value'] = $node->filterXPath('//td[@class="prop"]')->nextAll()->text();
                $attributes['product_id'] = $this->product->id;
                $property = ProductProperty::create($attributes);
                $property->product()->associate($this->product);
        });
    }

}
