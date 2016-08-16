<?php
/**
 * @author Dimous <kasimowsky@gmail.com>
 * @copyright (c) 2016, Dimous
 */

namespace Controllers {
    final class Index extends \Core\ViewController {
        private $__oPdo = NULL;
        
        protected $_sTemplateFileName = "Views/main.php";
         
        function __construct($oSettings) {
            parent::__construct();
            ///
            $this->__oPdo = new \PDO("mysql:host={$oSettings["db"]["host"]};dbname={$oSettings["db"]["dbname"]};charset=utf8", $oSettings["db"]["dbuser"], $oSettings["db"]["dbpassword"]);
            ///
            $this->__oPdo->exec("set names utf8"); // если проигнорирует при подключении
        }
        //---
        
        public function index() {
            try {
                $this->_oTemplate->cOfficers = $this->__oPdo->query("SELECT id,name FROM branch_co WHERE id>1000 ORDER BY name ASC")->fetchAll();
                } 
            catch (\PDOException $oPDOException) {
            }
            return $this->_oTemplate->render("Views/index.phtml");
        }
        //---
        
        public function getReviews() {
            exit('getReviews');
           
        }
        
        
        public function postReview() {
            exit('postReview');
          
        }
    }
}
?>
