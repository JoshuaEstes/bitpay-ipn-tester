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
        ->add('notificationUrl', 'url', array(
            'required' => true,
            'attr' => array('size' => '255')
        ))
        ->add('id', 'text', array(
            'required' => false,
            'attr' => array('size' => '64')
        ))
        ->add('url', 'url', array(
            'required' => false,
            'attr' => array('size' => '64')
        ))
        ->add('status', 'choice', array(
            'required' => false,
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
            'required' => false,
            'attr' => array('cols' => '60', 'rows' => '6')
        ))
        ->add('price', 'text', array(
            'required' => false,
        ))
        ->add('btcPrice', 'text', array(
            'required' => false,
        ))
        ->add('currency', 'choice', array(
            'required' => false,
            'choices' => array(
                'BTC' => 'BTC',
                'USD' => 'USD',
            ),
        ))
        ->add('invoiceTime', 'text', array(
            'required' => false,
        ))
        ->add('expirationTime', 'text', array(
            'required' => false,
        ))
        ->add('currentTime', 'text', array(
            'required' => false,
        ))
        ->add('btcPaid', 'text', array(
            'required' => false,
        ))
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
            'json' => array(
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
                'btcPaid'        => $data['btcPaid'],
            ),
        ));
        try {
            $ipnResponse = $client->send($ipnRequest);
        } catch (\Exception $e) {
            $ipnResponse = $e->getResponse();
        }
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
