<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOStatement;

/**
 * اتصال قاعدة البيانات (PDO) — Prepared Statements فقط
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $driver = Config::get('db.driver', 'mysql');
            if ($driver === 'sqlite') {
                $dsn = 'sqlite:' . Config::get('db.database');
                self::$pdo = new PDO($dsn, null, null, self::options());
            } else {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    Config::get('db.host', 'localhost'),
                    Config::get('db.port', '3306'),
                    Config::get('db.name'),
                    Config::get('db.charset', 'utf8mb4')
                );
                self::$pdo = new PDO($dsn, Config::get('db.user'), Config::get('db.pass'), self::options());
            }
        }
        return self::$pdo;
    }

    private static function options(): array
    {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
    }

    /** استعلام يُرجع عدة صفوف */
    public static function rows(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    /** استعلام يُرجع صفًا واحدًا */
    public static function row(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /** قيمة واحدة */
    public static function scalar(string $sql, array $params = []): mixed
    {
        $value = self::run($sql, $params)->fetchColumn();
        return $value === false ? null : $value;
    }

    /** إدراج صف وإرجاع المعرّف */
    public static function insert(string $sql, array $params = []): string
    {
        self::run($sql, $params);
        return self::pdo()->lastInsertId();
    }

    /** تحديث/حذف وإرجاع عدد الصفوف */
    public static function exec(string $sql, array $params = []): int
    {
        return self::run($sql, $params)->rowCount();
    }

    private static function run(string $sql, array $params): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
