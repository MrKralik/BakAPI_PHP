<?php
namespace Markaos\BakAPI {
  class DiffUtil {

    // Get array of differences between these two fields. Keys don't matter in
    // comparsion
    //
    // @oldA    Array containing old values
    // @newA    Array with new values
    // @return  Array wit differences
    public static function getDifferencesBakAPI($oldA, $newA) {
      // Note: modifications not supported yet
      $removed = array();
      $added = array();

      $section = BAKAPI_SECTION_GRADES;
      $a1 = $oldA[$section];
      $a2 = $newA[$section];
      DiffUtil::findDifferences($a1, $a2, $removed, $added);

      $section = BAKAPI_SECTION_SUBJECTS;
      $a1 = $oldA[$section];
      $a2 = $newA[$section];
      DiffUtil::findDifferences($a1, $a2, $removed, $added);

      $section = BAKAPI_SECTION_MESSAGES;
      $a1 = $oldA[$section];
      $a2 = $newA[$section];
      DiffUtil::findDifferences($a1, $a2, $removed, $added);

      $section = BAKAPI_SECTION_EVENTS;
      $a1 = $oldA[$section];
      $a2 = $newA[$section];
      DiffUtil::findDifferences($a1, $a2, $removed, $added);

      $section = BAKAPI_SECTION_HOMEWORK;
      $a1 = $oldA[$section];
      $a2 = $newA[$section];
      DiffUtil::findDifferences($a1, $a2, $removed, $added);

      $section = BAKAPI_SECTION_TIMETABLE_STABLE;
      $a1 = $oldA[$section];
      $a2 = $newA[$section];
      DiffUtil::findDifferences($a1, $a2, $removed, $added);

      $section = BAKAPI_SECTION_TIMETABLE_OVERLAY;
      $a1 = $oldA[$section];
      $a2 = $newA[$section];
      DiffUtil::findDifferences($a1, $a2, $removed, $added);

      $section = BAKAPI_SECTION_TIMETABLE_CYCLES;
      $a1 = $oldA[$section];
      $a2 = $newA[$section];
      DiffUtil::findDifferences($a1, $a2, $removed, $added);

      $section = BAKAPI_SECTION_TIMETABLE_CAPTIONS;
      $a1 = $oldA[$section];
      $a2 = $newA[$section];
      DiffUtil::findDifferences($a1, $a2, $removed, $added);

      $diffs = array();

      while(list(, $section) = each($removed)) {
        while(list(, $value) = each($removed[$section])) {
          $diffs[] = [
            "type" => "r",
            "table" => $section,
            "data" => $value
          ];
        }
      }

      while(list(, $section) = each($added)) {
        while(list(, $value) = each($added[$section])) {
          $diffs[] = [
            "type" => "a",
            "table" => $section,
            "data" => $value
          ];
        }
      }

      return $diffs;
    }

    // Find differences in arrays without taking keys into account
    //
    // @a1        First array, nicknamed "original"
    // @a2        Second array, "new"
    // @removed   Values unique to original array
    // @added     Values unique to new array
    public static function findDifferences($a1, $a2, &$removed, &$added) {
      while(list(, $value) = each($a1)) {
        if(!in_array($value, $a2)) {
          $removed[] = $value;
        }
      }

      while(list(, $value) = each($a2)) {
        if(!in_array($value, $a1)) {
          $added[] = $value;
        }
      }
    }
  }
}
?>