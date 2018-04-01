<?php

include_once "autoload.php";

$im = InstanceManager::getInstance();
$im->add('config', new Config('config.ini.php', 'dev'));
$im->add('db', new DB('default'));
$im->add('request', new Request());
$im->add('response', new Response());
$im->add('router', new Router());
$im->add('view', new View());
$im->add('user', new User());
$im->add('mailer', new Mailer());
$im->add('translation', new Translation());

// app specific
$im->add('userComponent', new UserComponent());
$im->add('welcomeComponent', new WelcomeComponent());
$im->add('adminComponent', new AdminComponent());
$im->add('app', new WebApplication());
$im->get('app')->run();