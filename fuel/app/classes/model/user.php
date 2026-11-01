<?php

class Model_User
{
    public static function exists_username($username)
    {
        return \DB::select('id')->from('users')
            ->where('username', $username)
            ->execute()->count() > 0;
    }

    public static function exists_email($email)
    {
        return \DB::select('id')->from('users')
            ->where('email', $email)
            ->execute()->count() > 0;
    }
}