<?php

namespace OpenTribes\Core\Service;

use OpenTribes\Core\Domain\Service\PasswordHasher as PasswordHasherInterface;

/**
 * Description of PasswordHasher
 *
 * @author BlackScorp<witalimik@web.de>
 */
class PasswordHasher implements PasswordHasherInterface {

    public function hash($rawPassword) {
        if (function_exists('password_hash')) {
            return password_hash($rawPassword, PASSWORD_DEFAULT);
        }
        $salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
        $salt = base64_encode($salt);
        $salt = str_replace('+', '.', $salt);
        $hash = crypt($rawPassword, '$2y$10$' . $salt . '$');
        return $hash;
    }

    public function verify($hash, $rawPassword) {
        if (function_exists('password_verify')) {
            return password_verify($rawPassword, $hash);
        }
        return crypt($rawPassword, $hash) === $hash;
    }

}