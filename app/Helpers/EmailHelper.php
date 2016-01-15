<?php

namespace App\Helpers;
use SendGrid;

class EmailHelper
{
    private static $api_user = 'smartdev12';
    private static $api_pass = 'kool321$';

    public static function NewUser($user)
    {
        $sendgrid = new SendGrid(self::$api_user, self::$api_pass);
        $email    = new SendGrid\Email();

        $view = view("email.user.new", $user);
        $email->addTo($user['username'])
            ->setFrom("noreply@smartmember.com")
            ->setSubject("Welcome to Smartmember")
            ->setHtml($view);

        $sendgrid->send($email);
    }
}