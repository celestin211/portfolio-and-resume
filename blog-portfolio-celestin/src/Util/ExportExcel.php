<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ExportExcel
{
    private $controller;
    private $phpExcelObject;

    public function __construct($controller)
    {
        $this->controller = $controller;

        $this->phpExcelObject = $controller->get('phpexcel')->createPHPExcelObject();

        $this->phpExcelObject->getProperties()->setCreator('SIGNAC')
        ->setLastModifiedBy('SIGNAC')
        ->setTitle('')
        ->setSubject('')
        ->setDescription('')
        ->setKeywords("SIGNAC DGAFP CISIRH Administrateurs de l'Etat");
    }

    public function setTitle($title)
    {
        $this->phpExcelObject->getProperties()
        ->setTitle($title)
        ->setSubject($title)
        ->setDescription($title);

        // Nom de la feuille excel
        $this->phpExcelObject->getActiveSheet()->setTitle($title);
    }

    public function getPhpExcelObject()
    {
        return $this->phpExcelObject;
    }

    public function save($filename)
    {
        // Mise en gras de la 1ere ligne
        $this->phpExcelObject->getActiveSheet()->getStyle('A1:Z1')->getFont()->setBold(true);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->controller->get('phpexcel')->createWriter($this->phpExcelObject, 'Excel5');
        // create the response
        $response = $this->controller->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);

        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
