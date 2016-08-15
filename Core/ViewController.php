<?php
/**
 * @author Dimous <kasimowsky@gmail.com>
 * @copyright (c) 2016, Dimous
 */

namespace Core {
    class ViewController {
        protected $_oTemplate = NULL;
        ///
            public function __construct() {
                $this->_oTemplate = new Template($this->_sTemplateFileName);
            } 
        
    }    
}
?>
