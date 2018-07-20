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
    protected $description = 'Parses all listed urls';

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

        $parser = new CatalogParser($config['parse_num'], $config['update_num']);

        foreach ($config['urls'] as $url) {
            $parser->parse($url);
        }
    }
}
