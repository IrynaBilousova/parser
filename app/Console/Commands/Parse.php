<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Parser\CatalogParser;
use App\Parser\ObjectParser;

class Parse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parses all listed urls.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $config = require(app_path('Parser/config.php'));

        static::validateConfig($config);

        echo "Please, wait. It may take some time.";
        //parse each category
        foreach ($config['urls'] as $url) {
            $parser = new CatalogParser($config['parse_num'], $config['update_num']);
            $parser->parse($url);
        }
        echo "\nParsed successfully.\n";
    }

    public static function validateConfig($config)
    {
        if(!$config['urls']) throw new \Exception('No urls to parse.');
        elseif (!$config['parse_num']) throw new \Exception('Number of items to parse is not specified.');
        elseif(!$config['update_num']) throw new \Exception('Number of items to update is not specified.');

        elseif(!is_numeric($config['parse_num']) || $config['parse_num'] < 1 || $config['parse_num'] != round($config['parse_num']))
            throw new \Exception('Number of items to parse must be a positive integer.');

        elseif(!is_numeric($config['update_num']) || $config['update_num'] < 1 || $config['update_num'] != round($config['update_num']))
            throw new \Exception('Number of items to update must be a positive integer.');

    }
}
