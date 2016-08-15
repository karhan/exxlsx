<?php
/**
 * @author Dimous <kasimowsky@gmail.com>
 * @copyright (c) 2016, Dimous
 */

namespace Controllers {
    final class Admin extends \Core\ViewController {
        private $__oPdo = NULL;

        protected $_sTemplateFileName = "Views/main.php";

        function __construct($oSettings) {
            parent::__construct();
            ///
            session_start();
            ///
            $this->__oPdo = new \PDO("mysql:host={$oSettings["db"]["host"]};dbname={$oSettings["db"]["dbname"]};charset=utf8", $oSettings["db"]["dbuser"], $oSettings["db"]["dbpassword"]);
            ///
            $this->__oPdo->exec("set names utf8");
        }
        //---
        
        private function checkAccess() {
            return isset($_SESSION["loggedin"]) && (boolean) $_SESSION["loggedin"]; // INPUT_SESSION ещё не реализовано
        }
        //---

        public function login() {
            if ($this->checkAccess()) {
                header("Location: /admin/dashboard/");
                ///
                exit;
            }
            ///
            if ("POST" === filter_input(INPUT_SERVER, "REQUEST_METHOD")) {
                $nFilter = FILTER_SANITIZE_FULL_SPECIAL_CHARS | FILTER_SANITIZE_ENCODED;
                $oStatement = $this->__oPdo->prepare("SELECT EXISTS(SELECT 1 FROM users WHERE login = :login AND password = PASSWORD(:password)) as result");
                ///
                $oStatement->bindParam(":login", filter_input(INPUT_POST, "login", $nFilter), \PDO::PARAM_STR);
                $oStatement->bindParam(":password", filter_input(INPUT_POST, "password", $nFilter), \PDO::PARAM_STR);
                ///
                if ($oStatement->execute()) {
                    $oResult = $oStatement->fetch(\PDO::FETCH_ASSOC);
                    ///
                    if ((boolean) $oResult["result"]) {
                        $_SESSION["loggedin"] = TRUE;
                        ///
                        header("Location: /admin/dashboard/");
                        ///
                        exit;
                    } else
                        $this->_oTemplate->sMessage = "Введено неправильное имя или пароль";
                }
            }
            ///
            return $this->_oTemplate->render("Views/admin/login.phtml");
        }
        //---
        
        public function logout() {
            unset($_SESSION["loggedin"]);
            ///
            header("Location: /admin/");
        }
        //---
        
        public function dashboard() {
            if (! $this->checkAccess()) throw new \Exception("Нет доступа", 401);
            ///
            try {
                $this->_oTemplate->aReviews = $this->__oPdo->query("SELECT * FROM reviews")->fetchAll();
            } catch (\PDOException $oPDOException) {
            }
            ///
            return $this->_oTemplate->render("Views/admin/dashboard.phtml");
        }
        //---
        
        /**
         * задумывал по RESTful-канонам, должен был быть PUT, но что-то пошло не так
         */
        public function putReview() {
            if (! $this->checkAccess()) throw new \Exception("Нет доступа", 401);
            ///
            $aPostKeys = array_keys($_POST);
            ///
            foreach ($aPostKeys as $sKey)
                if ("id" <> $sKey) $aKeys[] = "{$sKey} = :{$sKey}";
            ///
            if (array_key_exists("text", $_POST)) $aKeys[] = "state = 2"; // особая уличная магия
            ///
            $oStatement = $this->__oPdo->prepare(sprintf("UPDATE reviews SET %s WHERE id = :id", implode(", ", $aKeys)));
            ///
            $oStatement->bindParam(":id", filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT), \PDO::PARAM_INT); // INPUT_REQUEST ещё не реализован
            ///
            foreach ($aPostKeys as $sKey)
                if ("id" <> $sKey) $oStatement->bindParam(":{$sKey}", filter_input(INPUT_POST, $sKey, FILTER_SANITIZE_FULL_SPECIAL_CHARS | FILTER_SANITIZE_ENCODED), \PDO::PARAM_STR);
            ///
            $oStatement->execute();
        }
    }
}
?>
