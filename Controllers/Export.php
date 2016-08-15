<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Controllers{

/**
 * Description of Base
 *final class Index extends \Core\ViewController 
 * @author itadmin
 */
    final class Export extends \Core\BaseController {
        
        private $objReader;
        private $objDrawing;
        private $objWriter; 
        private $objPHPExcel;
        
        function __construct($oSettings) {
            
            require_once 'Core/Classes/PHPExcel/IOFactory.php';
            
            $this->objReader = \PHPExcel_IOFactory::createReader('Excel5'); 
            
            $sheetnames = ['pb','data'];
            
            $worksheetList = $this->objReader->listWorksheetNames("Assets/file/template.xls");

            foreach($sheetnames as $sheetname){

                    if (!in_array($sheetname,$worksheetList)){ 
                        throw new \Exception("Sheet not found!", 404);
                        exit();
                    } 
            } 
        }
        
        public function GenerateFile() {
            $this->objPHPExcel = $this->objReader->load("Assets/file/template.xls");
            $this->objWriter = \PHPExcel_IOFactory::createWriter( $this->objPHPExcel , 'Excel2007');
            $this->objDrawing = new \PHPExcel_Worksheet_Drawing(); 
            $this->objPHPExcel->setActiveSheetIndexByName('data')->setCellValue('myvariable', '555');
            $this->objPHPExcel->setActiveSheetIndexByName('pb'); 
            $this->objDrawing->setName('My Image');
            $this->objDrawing->setDescription('The Image that I am inserting');
            $this->objDrawing->setPath('Assets/images/imgf.png');
            $this->objDrawing->setCoordinates('B2');
            $this->objDrawing->setWorksheet($this->objPHPExcel->getActiveSheet()); 

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');


            header("Content-Disposition: attachment;filename=\"workbook1.xlsx\"");
            header('Cache-Control: max-age=0'); 
            header('Cache-Control: max-age=1'); 
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0 
            $this->objWriter->save('php://output');   
        }
        
        
        
        
        
    }
}