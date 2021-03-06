<?php
namespace Markaos\BakAPI\Web {

  class SubjectsPage extends BasePage {

    public function onRequest() {
      $userClient = \Markaos\BakAPI\BakAPI::getClient($this->getUID());
      $userData = $userClient->getData();
      $this->setTitle($userData["name"]);

      $collection = ContentBuilder::makeCollection();

      $data = $this->getData();
      $subjects = array();
      $merge = $this->getPreferences()->getValue("subjects_merge", "true") == "true";
      $cnt = $this->getPreferences()->getValue("subjects_count_lessons", "true") == "true";

      if($merge) {
        foreach($data[BAKAPI_SECTION_SUBJECTS] as $subject) {
          $short = $subject["short"];
          if(!isset($subjects[$short])) {
            $subjects[$short] = $subject;
            $subjects[$short]["teachers"] = [$subject["teachers"]];
            $subjects[$short]["emails"] = [$subject["emails"]];
          } else {
            $subjects[$short]["teachers"][] = $subject["teachers"];
            $subjects[$short]["emails"][] = $subject["emails"];
          }
        }
      } else {
        foreach($data[BAKAPI_SECTION_SUBJECTS] as $subject) {
          $subject["teachers"] = [$subject["teachers"]];
          $subject["emails"]   = [$subject["emails"]];
          $subjects[] = $subject;
        }
      }

      if($cnt) {
        foreach($data[BAKAPI_SECTION_TIMETABLE_STABLE] as $lesson) {
          $weight = 1;
          if(isset($lesson["cycle"]) && $lesson["cycle"] != "") {
            $weight = 0.5;
          }

          if($merge) {
            if(!isset($subjects[$lesson["short"]])) continue;
            if(!isset($subjects[$lesson["short"]]["count"])) {
              $subjects[$lesson["short"]]["count"] = $weight;
            } else {
              $subjects[$lesson["short"]]["count"] += $weight;
            }
          } else {
            foreach($subjects as $id => $subject) {
              if($subject["short"] == $lesson["short"] &&
                $subject["teachers"][0] == $lesson["teacher"]) {
                if(!isset($subjects[$id]["count"])) {
                  $subjects[$id]["count"] = $weight;
                } else {
                  $subjects[$id]["count"] += $weight;
                }
              }
            }
          }
        }
      }

      foreach($subjects as $subject) {
        $t = "";
        $first = true;
        foreach($subject["teachers"] as $id => $teacher) {
          if(!$first) $t .= ", ";
          $first = false;
          $t .= $teacher;
          if($subject["emails"][$id] != "") {
            $t .= " (<a href=\"mailto:" . $subject["emails"][$id] .
              "\" class=\"green-text\" style=\"text-decoration: underline;\">" .
              $subject["emails"][$id] . "</a>)";
          }
        }

        $c = "";
        if($cnt) {
          $c = " hodin";
          if($subject["count"] < 1) {
            $c = " hodiny";
          } else if ($subject["count"] == 1) {
            $c = " hodina";
          } else if ($subject["count"] > 1 && $subject["count"] < 6) {
            $c = " hodiny";
          }
          $c = $subject["count"] . $c;
        }

        $collection->addItem (
          ContentBuilder::makeBlock()
            ->addContentNode(
              ContentBuilder::makeText()
                ->setAttribute("style", "font-weight: 500")
                ->setContents($subject["name"])
                ->build()
            )
            ->addContentNode(ContentBuilder::makeLineBreak()->build())
            ->addContentNode(
              ContentBuilder::makeText()
                ->setContents($t)
                ->build()
            )
            ->addContentNode(ContentBuilder::makeLineBreak()->build())
            ->addContentNode(
              ContentBuilder::makeText()
                ->setAttribute("style", $cnt ? "" : "display: none;")
                ->setContents("" . $c . " týdně")
                ->build()
            )
            ->build()
        );
      }

      $this->addContentNode(ContentBuilder::makeBlock()
        ->addClass("container")
        ->addClass("row")
        ->setAttribute("style", "margin-bottom: 0px;")
        ->addContentNode(
          ContentBuilder::makeBlock()
            ->addContentNode($collection->build())
            ->build()
        )
        ->build());
      $this->addPermanentMenuEntry(ContentBuilder::makeBlock("a")
        ->setAttribute("href", "?frontend=cz.markaos.bakapi.web&sync=true")
        ->addContentNode(
          ContentBuilder::makeText("i")
            ->addClass("material-icons")
            ->setContents("loop")
            ->build()
        )
        ->build());
      $this->addMenuEntrySimple("Rozvrh", "&action=timetable");
      $this->addMenuEntrySimple("Známky", "&action=grades");
      $this->addMenuEntrySimple("Akce", "&action=events");
      $this->addMenuEntrySimple("Úkoly", "&action=homework");
      $this->addMenuEntrySimple("Předměty", "&action=subjects", false, true);
      $this->addMenuEntrySimple("Nastavení", "&action=preferences&section=subjects");
      $this->addMenuEntrySimple("Odhlásit", "&logout=true");
      $this->finish();
    }
  }
}
?>
