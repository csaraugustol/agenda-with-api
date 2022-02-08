<?php

namespace Tests\Mocks\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use Illuminate\Foundation\Testing\WithFaker;

class BaseProvider
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();
    }

    /**
     * Realiza o mock da response que vamos receber da Swap
     * quando consumir o método que envia a requisição para a API deles
     *
     * @param         $service
     * @param integer $status
     * @param         $body
     * @param array   $headers
     */
    public function setMockRequest($service, int $status, $body = null, array $headers = [])
    {
        $mock = new MockHandler([
            new Response(
                $status,
                $headers,
                !is_null($body) ? json_encode($body) : $body
            )
        ]);

        $handler = HandlerStack::create($mock);

        $client = new Client(compact('handler'));

        $service->setClient($client);
    }
}
