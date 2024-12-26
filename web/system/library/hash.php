<?php
/**
 * Hash class
 */
class Hash {
    /**
     * Hash the given value.
     *
     * @param  string $value
     * @return string
     *
     * @throws \RuntimeException
     */
    public static function make($value) {
        $hash = password_hash($value, PASSWORD_DEFAULT);
        if ($hash === false) {
            throw new RuntimeException('Error hashing value. Check system compatibility with password_hash().');
        }

        return $hash;
    }

    /**
     * Check the given plain value against a hash.
     *
     * @param  string $value
     * @param  string $hashedValue
     * @return bool
     */
    public static function check($value, $hashedValue) {
        if (strlen($hashedValue) === 0) {
            return false;
        }

        return password_verify($value, $hashedValue);
    }
}