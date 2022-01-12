<?php

namespace Tests\Traits;

trait MockClassMethod
{
    /**
     * Array dos métodos que serão mockados
     *
     * @var array
     */
    protected $mockMethods = [];

    /**
     * Array dos valores que o método mockado retornará
     * (success e data)
     *
     * @var array
     */
    protected $mockValues = [];

    /**
     * Adiciona um método para ser mockado, junto com
     * o valor que será retornado
     *
     * @param string $method
     * @param mixed $data
     *
     * @return void
     */
    public function addMockMethod(string $method, $data): void
    {
        array_push($this->mockMethods, $method);
        array_push($this->mockValues, $data);
    }

    /**
     * Reseta os valores do mock
     */
    public function resetMock()
    {
        $this->mockMethods = [];
        $this->mockValues = [];
    }

    /**
     * Executa o mock, ao instanciar a classe o retorno dos métodos passados
     * serão mockados de acordo com os valores informados
     *
     * @return void
     */
    public function applyMock(string $class): void
    {
        $mock = $this->createPartialMock(
            $class,
            $this->mockMethods
        );

        foreach ($this->mockMethods as $i => $methodName) {
            $mock->method($methodName)->willReturn($this->mockValues[$i]);
        }

        $this->app->instance($class, $mock);

        $this->resetMock();
    }

    /**
     * Mock para múltiplas chamada de um mesmo método
     *
     * @return void
     */
    public function applyMockMultipleCalls(string $class, string $method, array $responses): void
    {
        $mock = $this->createPartialMock(
            $class,
            [$method]
        );

        $count = 0;

        foreach ($responses as $response) {
            $mock->expects($this->at($count))
                ->method($method)
                ->willReturn($response);

            $count++;
        }

        $this->app->instance($class, $mock);
    }
}
