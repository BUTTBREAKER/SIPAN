<?php

namespace SIPAN;

use PDO;

final readonly class Database
{
  private PDO $connection;

  private function __construct()
  {
    $this->connection = db()->connection();
  }

  public static function getInstance(): self
  {
    static $instance = null;

    if ($instance === null) {
      $instance = new self;
    }

    return $instance;
  }

  public function getConnection(): PDO
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
