<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('send-mail', function () {
    try {
        \Illuminate\Support\Facades\Mail::raw('Congrats for sending test email with Mailtrap via SMTP!', function ($message) {
            $message->to('misaelbarreraojeda@gmail.com')
                ->subject('You are awesome (Standard Laravel)!');
        });
        $this->info('Email enviado correctamente usando el sistema estándar de Laravel.');
    } catch (\Exception $e) {
        $this->error('Error al enviar: ' . $e->getMessage());
    }
})->purpose('Send Mail via Standard Laravel SMTP');
