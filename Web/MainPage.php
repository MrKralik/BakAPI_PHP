<?php
namespace Markaos\BakAPI\Web {

  class MainPage extends BasePage {

    public function onRequest() {
      $userClient = \Markaos\BakAPI\BakAPI::getClient($this->getUID());
      $userData = $userClient->getData();
      $this->setTitle($userData["name"]);
      $this->addContentNode(ContentBuilder::makeBlock()
        ->addClass("valign-wrapper")
        ->addClass("row")
        ->setAttribute("style", "height: 90%")
        ->addContentNode(
          ContentBuilder::makeBlock()
            ->addClass("valign")
            ->addClass("col s12 m10 offset-m1 l8 offset-l2")
            ->addContentNode(
              ContentBuilder::makeText("h1")
                ->addClass("center-align")
                ->setContents("Vítejte v BakAPI")
                ->build()
            )
            ->build()
        )
        ->build());
      $this->addPermanentMenuEntry(ContentBuilder::makeBlock("a")
        ->setAttribute("href", "#")
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
      $this->addMenuEntrySimple("Předměty", "&action=subjects");
      $this->addMenuEntrySimple("Odhlásit", "&logout=1");
      $this->finish();
    }
  }
}
?>
