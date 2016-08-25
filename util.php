<?php
namespace Markaos\BakAPI {
  define("BAKAPI_DB_VERSION", 1);

  class Util {

    public static function loadPage($url) {
      $ch = \curl_init();
      \curl_setopt($ch, CURLOPT_URL, $url);
      \curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
      \curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      \curl_setopt ($ch, CURLOPT_USERAGENT, "");
      \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
      \curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
      $store = \curl_exec ($ch);
      \curl_close($ch);
      return $store;
    }

    public static function getSettings() {
      if(!\file_exists("config.ini")) {
        \file_put_contents("config.ini", "; Put your settings here");
      }

      $settings = \parse_ini_file("defaults.ini");
      $custom = \parse_ini_file("config.ini");

      return \array_merge($settings, $custom);
    }

    public static function getDatabase() {
      $settings = \Markaos\BakAPI\Util::getSettings();
      return new $settings["database"]();
    }

    public static function initBakAPI() {
      $db = \Markaos\BakAPI\Util::getDatabase();

      $table = "meta";
      $structure = [
        "version" => "int"
      ];
      $db->createTable($table, $structure);

      $table = "users";
      $structure = [
        "UID" => "string:128",
        "client" => "string:64",
        "data" => "string:512",
        "lastUpdateFast" => "int",
        "lastUpdateSlow" => "int"
      ];
      $db->createTable($table, $structure);

      $table = BAKAPI_SECTION_GRADES;
      $structure = [
        "UID"         => "string:128",
        "subject"     => "string:64",
        "title"       => "string:128",
        "description" => "string:256",
        "grade"       => "string:4",
        "weight"      => "int",
        "date"        => "int",
      ];
      $db->createTable($table, $structure);

      $table = BAKAPI_SECTION_SUBJECTS;
      $structure = [
        "UID"         => "string:128",
        "name"        => "string:64",
        "teachers"    => "string:128",
        "emails"      => "string:128",
        "short"       => "string:8"
      ];
      $db->createTable($table, $structure);

      $table = BAKAPI_SECTION_EVENTS;
      $structure = [
        "UID"         => "string:128",
        "name"        => "string:64",
        "description" => "string:512",
        "timerange"   => "string:16",
        "rooms"       => "string:128",
        "teachers"    => "string:64",
        "classes"     => "string:64",
        "show"        => "int",
        "date"        => "int"
      ];
      $db->createTable($table, $structure);

      $table = BAKAPI_SECTION_MESSAGES;
      $structure = [
        "UID"         => "string:128",
        "from"        => "string:64",
        "contents"    => "string:2048",
        "sysid"       => "string:64",
        "date"        => "int"
      ];
      $db->createTable($table, $structure);

      $table = BAKAPI_SECTION_HOMEWORK;
      $structure = [
        "UID"         => "string:128",
        "subject"     => "string:64",
        "issued"      => "int",
        "deadline"    => "int",
        "state"       => "string:32",
        "description" => "string:1024"
      ];
      $db->createTable($table, $structure);

      $table = BAKAPI_SECTION_TIMETABLE_CAPTIONS;
      $structure = [
        "UID"         => "string:128",
        "caption"     => "string:4",
        "begin"       => "string:8",
        "end"         => "string:8"
      ];
      $db->createTable($table, $structure);

      $table = BAKAPI_SECTION_TIMETABLE_STABLE;
      $structure = [
        "UID"         => "string:128",
        "caption"     => "string:4",
        "day"         => "string:4",
        "type"        => "string:4",
        "short"       => "string:8",
        "steacher"    => "string:8",
        "teacher"     => "string:64",
        "shortRoom"   => "string:8",
        "shortGroup"  => "string:8",
        "group"       => "string:16",
        "cycle"       => "string:4"
      ];
      $db->createTable($table, $structure);

      $table = BAKAPI_SECTION_TIMETABLE_OVERLAY;
      $structure = [
        "UID"         => "string:128",
        "caption"     => "string:4",
        "day"         => "string:4",
        "type"        => "string:4",
        "short"       => "string:8",
        "steacher"    => "string:8",
        "teacher"     => "string:64",
        "shortRoom"   => "string:8",
        "shortGroup"  => "string:8",
        "group"       => "string:16",
        "theme"       => "string:64"
      ];
      $db->createTable($table, $structure);

      $table = BAKAPI_SECTION_TIMETABLE_CYCLES;
      $structure = [
        "UID"         => "string:128",
        "mondayDate"  => "int",
        "cycle"       => "string:8"
      ];
      $db->createTable($table, $structure);
    }
  }
}
?>
