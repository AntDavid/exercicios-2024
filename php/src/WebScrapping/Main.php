<?php

namespace Chuva\Php\WebScrapping;

use Chuva\Php\WebScrapping\Entity\Paper;
use Chuva\Php\WebScrapping\Entity\Person;

libxml_use_internal_errors(true);

/**
 * Runner for the Webscrapping exercise.
 */
class Main {

    /**
     * Main runner, instantiates a Scrapper and runs.
     */
    public static function run(): void {
        $scrapper = new Scrapper();
        $excel = new Excel();

        $data = $scrapper->scrap();
        
        return $excel->sendToExcel($data);
    }
}