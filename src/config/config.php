<?php
declare(strict_types=1);

return [
    'app' => [
        // Pas aan als je projectmap anders heet
        'base_url' => '/parfum-webshop/public',
    ],

'db' => [
  'host' => '127.0.0.1',
  'port' => 3307,          // of 3306 als je die gebruikt
  'name' => 'parfum_webshop',
  'user' => 'root',
  'pass' => '',
  'charset' => 'utf8mb4',
],


    // Admin account (FE11/FE12)
    'admin' => [
        'username' => 'admin',
        // wachtwoord: Admin123!
        // Dit is oké voor schoolproject. (hash is dynamisch bij elke run, werkt prima)
        'password_hash' => password_hash('Admin123!', PASSWORD_DEFAULT),
    ],
];
