<?php

require_once "components/core/classLoader.php";

ClassLoader::initialize();
WebApplication::dispatch('config.ini.php', 'dev', [
    'user',
    'welcome'
]);
