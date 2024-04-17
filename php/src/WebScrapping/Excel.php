<?php

namespace Chuva\Php\WebScrapping;

use Chuva\Php\WebScrapping\Entity\Paper;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

/**
 * Fill Excel with scrapped data.
 */
class Excel {

    /**
     * Send all data to Excel resume file.
     */
    public function sendToExcel($data): void {
        try {
            $writer = WriterEntityFactory::createXLSXWriter();
            $writer->openToFile(__DIR__ . '/../../assets/scrapp.xlsx');

            $boldStyle = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
                ->setFontBold()
                ->setFontSize(12)
                ->setCellAlignment(\Box\Spout\Common\Entity\Style\CellAlignment::CENTER)
                ->build();

            $titleCells = [
                "ID", "Title", "Type", "Author 1", "Author 1 Institution", "Author 2",
                "Author 2 Institution", "Author 3", "Author 3 Institution", "Author 4", "Author 4 Institution",
                "Author 5", "Author 5 Institution", "Author 6", "Author 6 Institution", "Author 7",
                "Author 7 Institution", "Author 8", "Author 8 Institution", "Author 9", "Author 9 Institution",
            ];

            $titlesFromValues = WriterEntityFactory::createRowFromArray($titleCells, $boldStyle);
            $writer->addRow($titlesFromValues);

            $style = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
                ->setFontSize(12)
                ->setCellAlignment(\Box\Spout\Common\Entity\Style\CellAlignment::CENTER)
                ->build();

            foreach ($data as $element) {
                $cells = [];
                $cells[] = $element->id ?? "";
                $cells[] = $element->title ?? "";
                $cells[] = $element->type ?? "";

                foreach ($element->authors ?? [] as $author) {
                    $cells[] = $author->name;
                    $cells[] = $author->institution;
                }

                $rowFromValues = WriterEntityFactory::createRowFromArray($cells, $style);
                $writer->addRow($rowFromValues);
            }

            $writer->close();
            echo "Success";

        } catch (\Exception $e) {
            echo "Error while trying to send data to Excel:\n" . $e->getMessage();
        }
    }
}
