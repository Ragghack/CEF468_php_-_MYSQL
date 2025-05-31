<?php
class Database {
    private static $conn;

    public static function connect() {
        if (!self::$conn) {
            self::$conn = new mysqli("localhost", "root", "", "LibraryDB");
            if (self::$conn->connect_error) {
                die("Connection failed: " . self::$conn->connect_error);
            }
        }
        return self::$conn;
    }

    public static function disconnect() {
        if (self::$conn) {
            self::$conn->close();
            self::$conn = null;
        }
    }
}
?>
