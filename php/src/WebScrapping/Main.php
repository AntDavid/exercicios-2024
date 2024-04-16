<?php

namespace Chuva\Php\WebScrapping;

use Chuva\Php\WebScrapping\Entity\Paper;

libxml_use_internal_errors(true);

/**
 * Runner for the Webscrapping exercise.
 */
class Main {

    /**
     * Main runner, instantiates a Scrapper and runs.
     */
    public static function run(): void {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $html = file_get_contents(__DIR__ . '/../../assets/origin.html');
        $dom->loadHTML($html);
        $xPath = new \DOMXPath($dom);

        $idElement = $xPath->query('//*[@class="volume-info"]');
        $titleElement = $xPath->query('//*[@class="my-xs paper-title"]');
        $typeElement = $xPath->query('//*[@class="tags mr-sm"]');
        $authorsElement = $xPath->query('//*[@class="authors"]');

        // Verifying if HTML elements were found
        if ($idElement === false || $titleElement === false || $typeElement === false || $authorsElement === false) {
            echo "Error trying to get HTML elements";
            return;
        }

        $data = [];
        for ($i = 0; $i < $idElement->length; $i++) {
            $filteredCardData = [
                $idElement->item($i)->textContent,
                $titleElement->item($i)->textContent,
                $typeElement->item($i)->textContent,
            ];

            // Extracting authors
            $authorsNames = [];
            $authorsInstitutes = [];
            foreach ($authorsElement as $node) {
                $authorsInfo = $node->getElementsByTagName('span');

                foreach ($authorsInfo as $author) {
                    $authorsNames[] = $author->nodeValue;
                    $authorsInstitutes[] = $author->getAttribute('title');
                }
            }

            // Calling the scrap method from Scrapper
            $data[] = (new Scrapper())->scrap($filteredCardData, [$authorsNames, $authorsInstitutes]);
        }

        (new Excel())->sendToExcel($data);
    }
}
