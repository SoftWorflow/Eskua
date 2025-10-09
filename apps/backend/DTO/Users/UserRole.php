<?php

class UserRole {
    public const Admin = 'admin';
    public const Teacher = 'teacher';
    public const Student = 'student';
    public const Guest = 'guest';

    public static function values(): array {
        return [
            self::Admin,
            self::Teacher,
            self::Student,
            self::Guest,
        ];
    }

    // Validar que un valor exista
    public static function isValid(string $role): bool {
        return in_array($role, self::values(), true);
    }
}

?>