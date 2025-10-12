<?php

namespace SIPAN;

class Database
{
  private static $instance = null;
  private $connection;

  private function __construct()
  {
    $this->connection = db()->connection();
  }

  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function getConnection()
  {
    return $this->connection;
  }

  public function query($sql, $params = [])
  {
    $stmt = $this->connection->prepare($sql);
    $stmt->execute($params);
    return $stmt;
  }

  public function fetchAll($sql, $params = [])
  {
    return $this->query($sql, $params)->fetchAll();
  }

  public function fetchOne($sql, $params = [])
  {
    return $this->query($sql, $params)->fetch();
  }

  public function execute($sql, $params = [])
  {
    return $this->query($sql, $params)->rowCount();
  }

  public function lastInsertId()
  {
    return $this->connection->lastInsertId();
  }

  public function beginTransaction()
  {
    return $this->connection->beginTransaction();
  }

  public function commit()
  {
    return $this->connection->commit();
  }

  public function rollback()
  {
    return $this->connection->rollBack();
  }
}
