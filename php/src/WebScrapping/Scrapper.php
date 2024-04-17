<?php

namespace Chuva\Php\WebScrapping;

use Chuva\Php\WebScrapping\Entity\Paper;
use Chuva\Php\WebScrapping\Entity\Person;
use PHPUnit\Event\Code\Throwable;

use function PHPUnit\Framework\throwException;
use Exception;

/**
 * Does the scrapping of a webpage.
 */
class Scrapper {

  /**
   * Loads paper information from the HTML and returns the array with the data.
   */
    public function scrap(): array {
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
            throw new Exception('Error trying to get HTML elements');
        }

        $i = 0;
        $data = [];
        $authorsTotal = $authorsElement->count();

        // for each paper
        for ($i; $i <= $idElement->length; $i++) {
            $id = $idElement->item($i)->textContent ?? "";
            $title = $titleElement->item($i)->textContent ?? "";
            $type = $typeElement->item($i)->textContent ?? "";

            // Extracting authors
            $authorsNames = [];
            $authorsInstitutes = [];
            $authorsCount = 0;

            foreach ($authorsElement as $node) {
                if ($authorsCount >= $authorsTotal){
                    break;
                }

                if ($authorsCount != $i){
                    $authorsCount++;
                    continue;
                }

                $authorsInfo = $node->getElementsByTagName('span');

                foreach ($authorsInfo as $author) {
                    $authorsNames[] = $author->nodeValue;
                    $authorsInstitutes[] = $author->getAttribute('title');
                }

                $authorsCount++;
            }

            $persons = [];

            for ($j = 0; $j < count($authorsNames); $j++) {
                $persons[] = new Person($authorsNames[$j], $authorsInstitutes[$j]);
            }

            $data[] = new Paper($id, $title, $type, $persons);

        }

        return array_filter($data);

    }
}