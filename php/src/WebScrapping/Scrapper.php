<?php

namespace Chuva\Php\WebScrapping;

use Chuva\Php\WebScrapping\Entity\Paper;
use Chuva\Php\WebScrapping\Entity\Person;

/**
 * Does the scrapping of a webpage.
 */
class Scrapper {

  /**
   * Loads paper information from the HTML and returns the array with the data.
   */
  public function scrap($filteredCardData, $authorsInfos): array {
    return [
      new Paper(
        $filteredCardData[0],
        $filteredCardData[1],
        $filteredCardData[2],
        self::createPerson($authorsInfos)
      ),
    ];
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