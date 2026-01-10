<?php

namespace Illuminate\Http;

interface Request
{
    /**
     * @return \Botble\ACL\Models\User|null
     */
    public function user($guard = null);
}