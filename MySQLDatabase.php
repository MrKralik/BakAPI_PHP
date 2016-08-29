<?php
namespace Markaos\BakAPI {
  // Simple MySQL implementation of IDatabase
  class MySQLDatabase implements \Markaos\BakAPI\IDatabase {
    private $db;

    public function __construct($purpose = "general") {
      $settings = \Markaos\BakAPI\Util::getSettings();
      $host = "";
      $db = "";
      $username = "";
      $password = "";
      if($purpose == "general") {
        $host = $settings["mysql_host"];
        $db = $settings["mysql_db"];
        $username = $settings["mysql_username"];
        $password = $settings["mysql_password"];
      } else if ($purpose == "log") {
        $host = isset($settings["mysql_host_log"]) ?
          $settings["mysql_host_log"] : $settings["mysql_host"];
        $db = isset($settings["mysql_db_log"]) ?
          $settings["mysql_db_log"] : $settings["mysql_db"];
        $username = isset($settings["mysql_username_log"]) ?
          $settings["mysql_username_log"] : $settings["mysql_username"];
        $password = isset($settings["mysql_password_log"]) ?
          $settings["mysql_password_log"] : $settings["mysql_password"];
      }

      $this->db = new \PDO(
        "mysql:host=" . $host . ";dbname=" . $db,
        $username,
        $password
      );
    }

    public function createTable($tablename, $structure) {
      $sql = "CREATE TABLE IF NOT EXISTS $tablename (
        _ID INT (11) AUTO_INCREMENT PRIMARY KEY,
        _DATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";

      $tmp = true;
      foreach($structure as $key => $type) {
        if(!$tmp) $sql .= ", ";
        $tmp = false;
        $type = explode(':', $type . ":x");
        $sql .= "field_$key" . " " . ($type[0] == "int" ? "INT (11)" :
          "VARCHAR (" . $type[1] . ")");
      }
      $sql .= ");";

      $res = $this->db->exec($sql);
      return $res !== false;
    }

    public function query($table, $columns, $conditions, $orderBy) {
      $sql = "SELECT ";
      $tmp = true;

      foreach ($columns as $col) {
        if(!$tmp) $sql .= ", ";
        $tmp = false;
        $col = $col == "_ID" ? $col : "field_" . $col;
        $sql .= $col;
      }

      $sql .= " FROM $table WHERE ";

      $values = array();
      $tmp = true;
      foreach ($conditions as $cond) {
        if(!$tmp) $sql .= " AND ";
        $tmp = false;
        $key = $cond["column"] == "_ID" ? "_ID" : "field_" . $cond["column"];
        $sql .= $key . " ";
        switch($cond["condition"]) {
          case "equals":
            $sql .= "=";
            break;
          case "lesser":
            $sql .= "<";
            break;
          case "greater":
            $sql .= ">";
            break;
          default:
            throw new Exception("Unknown operator");
            break;
        }
        $sql .= " ?";
        $values[] = $cond["value"];
      }

      if($orderBy !== false) {
        $sql .= " ORDER BY $orderBy";
      }

      $query = $this->db->prepare($sql);
      $query->execute($values);
      $result = $query->fetchAll();

      $r = array();
      foreach ($result as $row) {
        foreach($row as $key => $value) {
          if($key != "_ID" && $key != "_DATE") {
            unset($row[$key]);
            $row[substr($key, 6)] = $value;
          }
        }
        unset($row[0]);
        $r[] = $row;
      }

      return $r;
    }

    public function insert($table, $columns, $values) {
      $rowsCount = count($values);
      $colsCount = count($columns);

      $sql = "INSERT INTO $table (";
      $tmp = true;
      foreach($columns as $col) {
        if(!$tmp) $sql .= ", ";
        $tmp = false;
        $sql .= "field_" . $col;
      }
      $sql .= ") VALUES ";

      $vals = array();

      for($i = 0; $i < $rowsCount; $i++) {
        $sql .= "(";
        $tmp = true;
        for($j = 0; $j < $colsCount; $j++) {
          if(!$tmp) $sql .= ", ";
          $tmp = false;
          $sql .= "?";
          $vals[] = $values[$i][$j];
        }
        $sql .= ")";
      }

      $this->db->beginTransaction();
      $query = $this->db->prepare($sql);
      $res = $query->execute($vals);
      $this->db->commit();

      if($res === false) {
        \Markaos\BakAPI\Log::critical("MySQL",
          "Query failed! (\"$sql\", error: " . $this->db->errorInfo()[2] . ")");
      }
      return $res !== false;
    }

    public function modify($table, $ids, $columns, $values) {
      // TODO: stub
    }

    public function remove($table, $conditions) {
      $sql = "DELETE FROM $table WHERE ";

      $values = array();
      $tmp = true;
      foreach ($conditions as $cond) {
        if(!$tmp) $sql .= " AND ";
        $tmp = false;
        $key = $cond["column"] == "_ID" ? "_ID" : "field_" . $cond["column"];
        $sql .= $key . " ";
        switch($cond["condition"]) {
          case "equals":
            $sql .= "=";
            break;
          case "lesser":
            $sql .= "<";
            break;
          case "greater":
            $sql .= ">";
            break;
          default:
            throw new Exception("Unknown operator");
            break;
        }
        $sql .= " ?";
        $values[] = $cond["value"];
      }

      $this->db->beginTransaction();
      $query = $this->db->prepare($sql);
      $res = $query->execute($values);
      $this->db->commit();

      if($res === false) {
        \Markaos\BakAPI\Log::critical("MySQL",
          "Query failed! (\"$sql\", error: " . $this->db->errorInfo()[2] . ")");
      }
      return $res !== false;
    }
  }
}
?>
