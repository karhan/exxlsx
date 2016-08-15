<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core{

/**
 * Description of BaseController
 *
 * @author itadmin
 */
   abstract class BaseController {
          protected function convert_number_to_words($number,$currency=FALSE) { 
            $hyphen      = ' ';
            $conjunction = ' ';
            $separator   = ' ';
            $negative    = 'mənfi ';
            $decimal     = ' manat '; 
            $sn     = ' qəpik '; 
            $dictionary  = array(
                0                   => 'sıfır',
                1                   => 'bir',
                2                   => 'iki',
                3                   => 'üç',
                4                   => 'dörd',
                5                   => 'beş',
                6                   => 'altı',
                7                   => 'yeddi',
                8                   => 'səkkiz',
                9                   => 'doqquz',
                10                  => 'on', 
                20                  => 'iyirmi',
                30                  => 'otuz',
                40                  => 'qırx',
                50                  => 'əlli',
                60                  => 'altmış',
                70                  => 'yetmiş',
                80                  => 'səksən',
                90                  => 'doxsan',
                100                 => 'yüz',
                1000                => 'min',
                1000000             => 'milyon',
                1000000000          => 'milyard',
                1000000000000       => 'trillion' 
            );

            if (!is_numeric($number)) {
                return false;
            }

            if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
                // overflow
                trigger_error(
                    'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                    E_USER_WARNING
                );
                return false;
            }

            if ($number < 0) {
                return $negative . convert_number_to_words(abs($number));
            }

            $string = $fraction = null;

            if (strpos($number, '.') !== false) {
                list($number, $fraction) = explode('.', $number); 
            }

            switch (true) {
                case $number < 11:
                    $string = $dictionary[$number];
                    break;
                case $number < 100:
                    $tens   = ((int) ($number / 10)) * 10;
                    $units  = $number % 10;
                    $string = $dictionary[$tens];
                    if ($units) {
                        $string .= $hyphen . $dictionary[$units];
                    }
                    break;
                case $number < 1000:
                    $hundreds  = $number / 100;
                    $remainder = $number % 100;
                                $ht=$hundreds<2?'':$dictionary[$hundreds] ;
                    $string = trim($ht. ' ' . $dictionary[100]);
                    if ($remainder) {
                        $string .= $conjunction . convert_number_to_words($remainder);
                    }
                    break;
                default:
                    $baseUnit = pow(1000, floor(log($number, 1000)));
                    $numBaseUnits = (int) ($number / $baseUnit);
                                $pt=$number<2000?'':convert_number_to_words($numBaseUnits);
                    $remainder = $number % $baseUnit;
                    $string = trim($pt . ' ' . $dictionary[$baseUnit]);
                    if ($remainder) {
                        $string .= $remainder < 100 ? $conjunction : $separator;
                        $string .= convert_number_to_words($remainder); 
                    }  
                    break;
            }

            if (null !== $fraction && is_numeric($fraction)) { 
                        $fraction=substr($fraction,0,2);

                        $fraction=strlen($fraction)<2?$fraction.'0':$fraction;

                        if((int)$fraction>0){ 
                                $string .= $decimal;	
                                $string= $string.' '.convert_number_to_words((int)$fraction).$sn;
                                $currency=FALSE;
                        }

            }
            $string=$currency!=FALSE?$string.$decimal:$string;
            return $string;
        }
    }
}