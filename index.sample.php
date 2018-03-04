<?php

include_once "autoload.php";

$im = new InstanceManager();
$im->add('config', new Config($im, 'config.ini.php', 'dev'));
$im->add('db', new DB($im, 'default'));
$im->add('request', new Request($im));
$im->add('response', new Response($im));
$im->add('router', new Router($im));
$im->add('view', new View($im));
$im->add('user', new User($im));
$im->add('mailer', new Mailer($im));
$im->add('translation', new Translation($im));

// app specific
$im->add('userComponent', new UserComponent($im));
$im->add('welcomeComponent', new WelcomeComponent($im));
$im->add('app', new WebApplication($im));
$im->get('app')->run();