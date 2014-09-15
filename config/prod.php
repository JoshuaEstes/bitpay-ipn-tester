<?php

use Silex\Provider\FormServiceProvider;

// configure your app for the production environment

$app['twig.path'] = array(__DIR__.'/../templates');
$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');
$app['twig.form.templates'] = array(
    'form_table_layout.html.twig',
);

$app->register(new FormServiceProvider());
$app->register(new \Silex\Provider\ValidatorServiceProvider());
$app->register(new \Silex\Provider\TranslationServiceProvider(), array(
    'translator.domains' => array(),
));

$app->register(new \Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../var/logs/silex_prod.log',
));

$app['notificationURL'] = 'http://bitpay-ipn-tester.herokuapp.com/index_dev.php/ipn';
