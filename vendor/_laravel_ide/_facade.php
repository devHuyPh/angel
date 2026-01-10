<?php

namespace Illuminate\Support\Facades;

interface Auth
{
    /**
     * @return \Botble\ACL\Models\User|false
     */
    public static function loginUsingId(mixed $id, bool $remember = false);

    /**
     * @return \Botble\ACL\Models\User|false
     */
    public static function onceUsingId(mixed $id);

    /**
     * @return \Botble\ACL\Models\User|null
     */
    public static function getUser();

    /**
     * @return \Botble\ACL\Models\User
     */
    public static function authenticate();

    /**
     * @return \Botble\ACL\Models\User|null
     */
    public static function user();

    /**
     * @return \Botble\ACL\Models\User|null
     */
    public static function logoutOtherDevices(string $password);

    /**
     * @return \Botble\ACL\Models\User
     */
    public static function getLastAttempted();
}