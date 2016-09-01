<?php
namespace Markaos\BakAPI {
  require_once "bakapi.php";
  require_once "Web/ContentBuilder.php";
  require_once "Web/BasePage.php";
  require_once "Web/Registration.php";
  require_once "Web/MainPage.php";

  session_start();

  class WebFrontend implements \Markaos\BakAPI\IFrontend {

    public function handleRequest() {
      $db = $this->getDatabase();
      if($db === false) {
        \Markaos\BakAPI\Web\Registrator::handleRequest($this, false, false);
        return;
      }

      if(isset($_GET["logout"])) {
        unset($_SESSION["UID"]);
        $this->handleRequest();
        return;
      }

      if(!isset($_GET["action"])) {
        \Markaos\BakAPI\Web\MainPage::handleRequest($this, $db, $_SESSION["UID"]);
      }
    }

    private function getDatabase() {
      if(!isset($_SESSION["UID"])) {
        // Not registered yet
        return false;
      }

      return \Markaos\BakAPI\BakAPI::getFullDatabase($_SESSION["UID"]);
    }
  }
}
?>
