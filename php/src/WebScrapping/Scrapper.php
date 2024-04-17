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
        //para cada paper de informacao
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
            // var_dump("Authors names: ", $authorsNames);
            // Calling the scrap method from Scrapper
            $persons = [];
            //$authorsInfos = [$authorsNames, $authorsInstitutes]; 
            for ($j = 0; $j < count($authorsNames); $j++) {
                $persons[] = new Person($authorsNames[$j], $authorsInstitutes[$j]);
            }         
            $data[] = new Paper($id, $title, $type, $persons);
            
            // Check if paper is not null before adding it to data array
            
        }
        // Filter out null papers
        return array_filter($data);
 }

  /**
   * Return authors array with all data received from Person class.
   */
  private function createPerson($authors): array {
    $persons = [];
    for ($i = 0; $i <= count($authors[0]) - 1; $i++) {
      $persons[] = new Person($authors[0][$i], $authors[1][$i]);
    }
    return $persons;
  }

}