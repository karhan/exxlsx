<?php
/**
 * @author Dimous <kasimowsky@gmail.com>
 * @copyright (c) 2016, Dimous
 */

namespace Core {    
    final class Template {
        private $__sFile = NULL, $__oData = NULL;
        ///
        public function __construct($sFile = NULL) {
            $this->__sFile = $sFile;
            $this->__oData = [];
        }
        //---

        public function __isset($sName) {
            return isset($this->__oData[$sName]);
        }
        //---

        public function __unset($sName) {
            unset($this->__oData[$sName]);
        }
        //---

        /**
         * передача partials
         */
        public function __set($sName, $oValue) {
            if ($oValue instanceof self) $oValue = $oValue->render();
            ///
            $this->__oData[$sName] = $oValue;
        }
        //---

        public function __get($sName) {
            return $this->__oData[$sName];
        }
        //---

        public function bind($sName, & $oVariable) {
            $this->__oData[$sName] =& $oVariable;
        }
        //---

        public function render($sFile = NULL) {
            if (empty($sFile))
                $sFile = $this->__sFile;
            ///
            if (file_exists($sFile)) {
                extract($this->__oData, EXTR_OVERWRITE);
                ///
                ob_start();
                ///
                require $sFile;
            } else {
                ob_end_clean();
                ///
                throw new \Exception("Файл шаблона не найден", 2);
            }
            ///
            return ob_get_clean();
        }
        
        

        
    }
}
?>
