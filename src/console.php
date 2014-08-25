<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

$console = new Application('My Silex Application', 'n/a');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);
//$console
//    ->register('my-command')
//    ->setDefinition(array(
//        // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
//    ))
//    ->setDescription('My command description')
//    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
//        // do something
//    })
//;

$console->register('invoice:create')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        require_once __DIR__ . '/../lib/bp_lib.php';
        $faker   = \Faker\Factory::create();
        $orderId = $faker->randomNumber(7);
        $price = '0.00' . $faker->randomNumber(2);
        //$posData = array();
        $posData = (string) $faker->randomNumber(7);
        $options = array(
            'curency'           => 'USD',
            'notificationURL'   => $app['notificationURL'],
            'fullNotifications' => 'true',
            'buyerName'         => $faker->name,
            'buyerAddress1'     => $faker->streetAddress,
            'buyerCity'         => $faker->city,
            'buyerZip'          => $faker->postcode,
            'buyerEmail'        => $faker->email,
        );
        $invoice = bpCreateInvoice($orderId, $price, $posData, $options);
        var_dump(iconv_get_encoding());
        var_dump(
            $orderId,
            $price,
            $posData,
            $options
        );
        var_dump($invoice);
    });

return $console;
