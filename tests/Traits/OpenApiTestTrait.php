<?php

namespace App\Test\Traits;

use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait OpenApiTestTrait
{
    protected static string $openApiSpecPath = __DIR__ . '/../openapi.yaml';

    protected function validateRequest(ServerRequestInterface $request): void
    {
        $validator = (new ValidatorBuilder())->fromYamlFile(static::$openApiSpecPath)->getServerRequestValidator();
        $validator->validate($request);
    }

    protected function validateResponse(ResponseInterface $response, string $path, string $method): void
    {
        $operationAddress = new OperationAddress($path, strtolower($method));
        $validator = (new ValidatorBuilder())->fromYamlFile(static::$openApiSpecPath)->getResponseValidator();
        $validator->validate($operationAddress, $response);
    }

    protected function validateSymmetry(ServerRequestInterface $request, ResponseInterface $response): void
    {
        $validator = (new ValidatorBuilder())->fromYamlFile(static::$openApiSpecPath)->getServerRequestValidator();
        $match = $validator->validate($request);

        $responseValidator = (new ValidatorBuilder())->fromYamlFile(static::$openApiSpecPath)->getResponseValidator();
        $responseValidator->validate($match, $response);
    }
}
