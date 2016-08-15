<?php
/**
 * @author Dimous <kasimowsky@gmail.com>
 * @copyright (c) 2016, Dimous
 */

final class Application {
    private $__oRoutes = NULL, $__oSettings = NULL;
    ///
    function __construct($oRoutes, $oSettings) {
        $this->__oRoutes = $oRoutes;
        $this->__oSettings = $oSettings;
    }
    //---
    
    public function run($sRequest) {
        $bFound = FALSE;
        $sResponse = NULL;
        $sRequestMethod = filter_input(INPUT_SERVER, "REQUEST_METHOD");
        ///
        
        foreach ($this->__oRoutes as $oRoute)
            if (($sRequestMethod === $oRoute["method"] || "*" === $oRoute["method"]) && 1 === preg_match("#{$oRoute["pattern"]}#is", $sRequest, $aMatches)) {
                if (array_key_exists("default", $oRoute))
                    foreach ($oRoute["default"] as $sKey => $oValue)
                        if (! array_key_exists($sKey, $aMatches) || empty($aMatches[$sKey])) $aMatches[$sKey] = $oValue;
                ///
                $sAction =& $oRoute["action"];
                
                $oController = new $oRoute["controller"]($this->__oSettings, (object) $aMatches);
                ///
               
                if (is_callable([$oController, $sAction])) { // на тестовом хостинге древний PHP, не понимающий короткую запись массивов
                    $bFound = TRUE;
                    $sResponse = $oController->{$sAction}();
                    ///
                    break;
                }
            }
        ///
        if (! $bFound)
            throw new Exception("Страница не найдена", 404);
        ///
        return $sResponse;
    }
}
?>
