<?php

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

function getIpnForm(\Silex\Application $app)
{
    return $app['form.factory']->createBuilder('form', array())
        ->add('notificationUrl', 'text', array(
            'attr' => array('size' => '255')
        ))
        ->add('id', 'text', array(
            'attr' => array('size' => '64')
        ))
        ->add('url', 'text', array(
            'attr' => array('size' => '64')
        ))
        ->add('status', 'choice', array(
            'choices' => array(
                'new'       => 'new',
                'paid'      => 'paid',
                'confirmed' => 'confirmed',
                'complete'  => 'complete',
                'expired'   => 'expired',
                'invalid'   => 'invalid',
            ),
        ))
        ->add('posData', 'textarea', array(
            'attr' => array('cols' => '60', 'rows' => '6')
        ))
        ->add('price', 'text')
        ->add('btcPrice', 'text')
        ->add('currency', 'choice', array(
            'choices' => array(
                'BTC' => 'BTC',
                'USD' => 'USD',
            ),
        ))
        ->add('invoiceTime', 'text')
        ->add('expirationTime', 'text')
        ->add('currentTime', 'text')
        ->getForm();
}

$app->get('/', function () use ($app) {
    $form = getIpnForm($app);
    return $app['twig']->render(
        'index.html',
        array(
            'form'         => $form->createView(),
            'ipn_request'  => null,
            'ipn_response' => null,
        )
    );
})
->bind('homepage');

$app->post('/', function (Request $request) use ($app) {
    $form = getIpnForm($app);
    $form->handleRequest($request);
    $result = null;

    if ($form->isValid()) {
        $client     = new Client();
        $data       = $form->getData();
        $ipnRequest = $client->createRequest('POST', $data['notificationUrl'], array(
            'body' => array(
                'id'             => $data['id'],
                'url'            => $data['url'],
                'status'         => $data['status'],
                'posData'        => $data['posData'],
                'price'          => $data['price'],
                'btcPrice'       => $data['btcPrice'],
                'currency'       => $data['currency'],
                'invoiceTime'    => $data['invoiceTime'],
                'expirationTime' => $data['expirationTime'],
                'currentTime'    => $data['currentTime'],
            )
        ));
        $ipnResponse = $client->send($ipnRequest);
    }

    return $app['twig']->render(
        'index.html',
        array(
            'form'         => $form->createView(),
            'ipn_request'  => $ipnRequest->__toString(),
            'ipn_response' => $ipnResponse->__toString(),
        )
    );
})->bind('send_ipn');

$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html',
        'errors/'.substr($code, 0, 2).'x.html',
        'errors/'.substr($code, 0, 1).'xx.html',
        'errors/default.html',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
