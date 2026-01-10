<?php

namespace Illuminate\Contracts\Auth;

interface Guard
{
    /**
     * @return \Botble\ACL\Models\User|null
     */
    public function user();
}