<?php

require_once "components/core/classLoader.php";

ClassLoader::initialize();
WebApplication::initialize('config.ini.php', 'dev');

// app specific
$im->add('userComponent', new UserComponent());
$im->add('welcomeComponent', new WelcomeComponent());
$im->add('adminComponent', new AdminComponent());
$im->add('app', new WebApplication());
$im->get('app')->run();