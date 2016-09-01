<?php
namespace Markaos\BakAPI\Web {

  abstract class BasePage {
    private $context = false;
    private $data    = false;
    private $uid     = false;
    private $title   = false;
    private $links   = false;
    private $meta    = false;
    private $scripts = false;
    private $menu    = false;
    private $pmenu   = false;
    private $header  = false;
    private $content = false;
    private $footer  = false;
    private $user    = false;
    private $server  = false;

    public static function handleRequest($context, $data, $uid) {
      $instance = new static($context, $data, $uid);
      $instance->onRequest();
    }

    public function __construct($context, $data, $uid) {
      $this->context = $context;
      $this->data    = $data;
      $this->uid     = $uid;
      $this->title   = "BakAPI";
      $this->menu    = array();
      $this->pmenu   = array();
      $this->user    = "16e063t6ei";
      $this->server  = "bakalari.gfpvm.cz";
      $this->links   = [
        [
          "href" => "http://fonts.googleapis.com/icon?family=Material+Icons",
          "rel"  => "stylesheet"
        ],
        [
          "href"  => "Web/Materialize/css/materialize.min.css",
          "rel"   => "stylesheet",
          "type"  => "text/css",
          "media" => "screen,projection"
        ],
        [
          "href"  => "Web/override.css",
          "rel"   => "stylesheet",
          "type"  => "text/css",
          "media" => "screen,projection"
        ]
      ];
      $this->meta = [
        "charset" => "utf-8",
        "viewport" => "width=device-width, initial-scale=1.0"
      ];
      $this->scripts = [
        "https://code.jquery.com/jquery-2.1.1.min.js",
        "Web/Materialize/js/materialize.min.js",
        "Web/init.js"
      ];
      $this->content = array();
      $this->header = ContentBuilder::makeBlock()
        ->addClass("navbar-fixed")
        ->addContentNode(
          ContentBuilder::makeBlock("nav")
            ->addContentNode(
              ContentBuilder::makeBlock()
                ->addClass("nav-wrapper")
                ->addClass("green")
                ->addContentNode(
                  ContentBuilder::makeText("a")
                    ->addClass("brand-logo")
                    ->setAttribute("style", "padding-left: 16px;")
                    ->setContentsReference($this->title)
                    ->build()
                )
                ->addContentNode(
                  ContentBuilder::makeBlock("a")
                    ->addClass("button-collapse")
                    ->setAttribute("href", "#")
                    ->setAttribute("data-activates", "mobile-nav")
                    ->addContentNode(
                      ContentBuilder::makeText("i")
                        ->addClass("material-icons")
                        ->setContents("menu")
                        ->build()
                    )
                    ->build()
                )
                ->addContentNode(
                  ContentBuilder::makeBlock("ul")
                    ->addClass("right")
                    ->addContentNode(ContentBuilder::makeBlock("span")
                      ->addClass("hide-on-med-and-down")
                      ->setContentsReference($this->menu)
                      ->build())
                    ->addContentNode(ContentBuilder::makeBlock("span")
                      ->setContentsReference($this->pmenu)
                      ->build())
                    ->build()
                )
                ->addContentNode(
                  ContentBuilder::makeBlock("ul")
                    ->addClass("side-nav")
                    ->setId("mobile-nav")
                    ->addContentNode(
                      ContentBuilder::makeBlock("li")
                        ->addClass("userView")
                        ->addContentNode(
                          ContentBuilder::makeText()
                            ->addClass("background")
                            ->setAttribute("style", "background-color: green;")
                            ->build()
                        )
                        ->addContentNode(
                          ContentBuilder::makeText()
                            ->addClass("white-text")
                            ->addClass("name")
                            ->setContentsReference($this->user)
                            ->build()
                        )
                        ->addContentNode(
                          ContentBuilder::makeText()
                            ->addClass("white-text")
                            ->addClass("email")
                            ->setContentsReference($this->server)
                            ->build()
                        )
                        ->build()
                    )
                    ->addContentNode(
                      ContentBuilder::makeBlock("span")
                        ->setContentsReference($this->menu)
                        ->build()
                    )
                    ->build()
                )
                ->build()
            )
            ->build()
        )
        ->build();
    }

    protected function getContext() {
      return $this->context;
    }

    protected function getData() {
      return $this->data;
    }

    protected function getUID() {
      return $this->uid;
    }

    protected function setTitle($title) {
      $this->title = $title;
    }

    protected function setContents($contents) {
      $this->content = array();
      $this->content[] = $contents;
    }

    protected function addContentNode($node) {
      $this->content[] = $node;
    }

    protected function addMenuEntry($entry) {
      $this->menu[] = ContentBuilder::makeBlock("li")
        ->addContentNode($entry)
        ->build();
    }

    protected function addMenuEntrySimple($text, $link, $icon = false, $active = false) {
      $this->addMenuEntry(ContentBuilder::makeBlock("li")
        ->addClass($active ? "active" : "")
        ->addContentNode(
          ContentBuilder::makeBlock("a")
            ->setAttribute("href", "?frontend=cz.markaos.bakapi.web" . $link)
            ->addContentNode(
              ContentBuilder::makeText()
                ->setContents($text)
                ->build()
            )
            ->build()
        )
        ->build());
    }

    protected function addPermanentMenuEntry($entry) {
      $this->pmenu[] = ContentBuilder::makeBlock("li")
        ->addContentNode($entry)
        ->build();
    }

    protected function finish() {
      echo "<html>\n<head>\n";
      foreach ($this->meta as $name => $value) {
        if($name == "charset") {
          echo "  <meta charset=\"$value\" />\n";
        } else {
          echo "  <meta name=\"$name\" content=\"$value\" />\n";
        }
      }
      foreach($this->links as $link) {
        echo "  <link ";
        foreach($link as $key => $value) {
          echo "$key=\"$value\" ";
        }
        echo "/>\n";
      }
      echo "  <title>" . $this->title . "</title>\n";
      echo "</head>\n";
      echo "<body>\n";

      foreach($this->scripts as $script) {
        echo "  <script type=\"text/javascript\" src=\"$script\"></script>\n";
      }

      $this->printNode($this->header, 1);

      foreach($this->content as $node) {
        $this->printNode($node, 1);
      }
    }

    protected function printNode($node, $intendation) {
      if(!isset($node["type"])) var_dump($node);
      if($node["type"] == "container") {
        echo str_repeat("  ", $intendation) . "<" . $node["element"] .
          " class=\"" . $node["classes"] . "\" id=\"" . $node["id"] . "\" ";
        foreach($node["attributes"] as $key => $value) {
          echo "$key=\"$value\" ";
        }
        echo ">\n";
        foreach($node["contents"] as $innerNode) {
          $this->printNode($innerNode, $intendation + 1);
        }
        echo str_repeat("  ", $intendation) . "</" . $node["element"] . ">\n";
      } else {
        echo str_repeat("  ", $intendation) . "<" . $node["element"] .
          " class=\"" . $node["classes"] . "\" id=\"" . $node["id"] . "\" ";
        foreach($node["attributes"] as $key => $value) {
          echo "$key=\"$value\" ";
        }
        if(isset($node["contents"])) echo ">\n";
        else echo "/>\n";
        if(isset($node["contents"])) {
          echo str_repeat("  ", $intendation + 1) . $node["contents"] . "\n";
          echo str_repeat("  ", $intendation) . "</" . $node["element"] . ">\n";
        }
      }
    }
  }
}
?>
