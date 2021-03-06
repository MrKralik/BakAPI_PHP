<?php
namespace Markaos\BakAPI {
  require_once "bakapi.php";
  require_once "Web/Util.php";
  require_once "Web/ContentBuilder.php";
  require_once "Web/BasePage.php";
  require_once "Web/Preferences.php";
  require_once "Web/Registration.php";
  require_once "Web/MainPage.php";
  require_once "Web/TimetablePage.php";
  require_once "Web/GradesPage.php";
  require_once "Web/HomeworkPage.php";
  require_once "Web/SubjectsPage.php";
  require_once "Web/EventsPage.php";
  require_once "Web/PreferencesPage.php";

  session_start();

  class WebFrontend implements \Markaos\BakAPI\IFrontend {

    public function handleRequest() {
      $db = $this->getDatabase();
      if($db === false) {
        \Markaos\BakAPI\Web\Registrator::handleRequest($this, false, false, false);
        return;
      }

      if(isset($_GET["logout"])) {
        unset($_SESSION["UID"]);
        unset($_SESSION["name"]);
        unset($_SESSION["server"]);
        $this->handleRequest();
        return;
      }

      if(isset($_GET["sync"])) {
        \Markaos\BakAPI\BakAPI::syncData($_SESSION["UID"]);
        $db = $this->getDatabase();
      }

      $p = new Web\Preferences($_SESSION["UID"]);

      if(!isset($_GET["action"])) {
        \Markaos\BakAPI\Web\MainPage::handleRequest($this, $db, $_SESSION["UID"], $p);
      } else if ($_GET["action"] == "timetable") {
        \Markaos\BakAPI\Web\TimetablePage::handleRequest($this, $db, $_SESSION["UID"], $p);
      } else if ($_GET["action"] == "grades") {
        \Markaos\BakAPI\Web\GradesPage::handleRequest($this, $db, $_SESSION["UID"], $p);
      } else if ($_GET["action"] == "homework") {
        \Markaos\BakAPI\Web\HomeworkPage::handleRequest($this, $db, $_SESSION["UID"], $p);
      } else if ($_GET["action"] == "subjects") {
        \Markaos\BakAPI\Web\SubjectsPage::handleRequest($this, $db, $_SESSION["UID"], $p);
      } else if ($_GET["action"] == "events") {
        \Markaos\BakAPI\Web\EventsPage::handleRequest($this, $db, $_SESSION["UID"], $p);
      } else if ($_GET["action"] == "preferences") {
        \Markaos\BakAPI\Web\PreferencesPage::handleRequest($this, $db, $_SESSION["UID"], $p);
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
