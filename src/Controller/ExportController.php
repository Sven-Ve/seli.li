<?php

namespace App\Controller;

use App\Repository\LinkRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/manage/export')]
class ExportController extends _BaseController
{
  #[Route('/', name: 'export2excel')]
  public function exportToExcel(LinkRepository $linkRep): void
  {
    $this->denyAccessUnlessGranted('ROLE_USER');
    $filename = 'selili';
    $links = $linkRep->qbShowLinksByUser($this->getUser())->getQuery()->execute();

    $streamedResponse = new StreamedResponse();

    $streamedResponse->setCallback(function () use ($links) {
      // Generating SpreadSheet
      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();
      $sheet->setTitle('Links');

      $sheet->setCellValue('A1', 'ID');
      $sheet->setCellValue('B1', 'Name');
      $sheet->setCellValue('C1', 'Link');
      $sheet->setCellValue('D1', 'Description');
      $sheet->setCellValue('E1', 'Category');
      $sheet->setCellValue('F1', 'User');

      $sheet->getStyle('A1:F1')->getFont()->setBold(true);

      $row = 1;
      foreach ($links as $link) {
        ++$row;

        $sheet->fromArray(
          [
            $link->getID(),
            $link->getName(),
            $link->getUrl(),
            $link->getDescription(),
            $link->getCategory()->getName(),
            $link->getUser()->getUserIdentifier(),
          ],
          null,
          'A' . $row);
      }

      // Write and send created spreadsheet
      $writer = new Xlsx($spreadsheet);
      $writer->save('php://output');

      // This exit(); is required to prevent errors while opening the generated .xlsx
      exit;
    });

    $streamedResponse->setStatusCode(Response::HTTP_OK);
    $streamedResponse->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $streamedResponse->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '.xlsx"');
    $streamedResponse->send();
  }
}
