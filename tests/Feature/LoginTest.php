<?php

declare(strict_types=1);

namespace Tests\Feature;

use DOMDocument;
use Override;

final class LoginTest extends FeatureTestCase
{
    private static string $sessionName;
    private static string $sessionId;
    private static string $csrfToken;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $response = self::$client->sendRequest(self::$requestFactory->createRequest('GET', './'));
        $body = (string) $response->getBody();
        $dom = new DOMDocument();

        if ($body) {
            @$dom->loadHTML($body);
        }

        $forms = $dom->getElementsByTagName('form');

        self::assertSame(200, $response->getStatusCode());
        self::assertCount(1, $forms);
        self::assertSame('post', $forms->item(0)?->getAttribute('method'));
        self::assertSame('./login', $forms->item(0)->getAttribute('action'));

        $inputs = $forms->item(0)->getElementsByTagName('input');
        $buttons = $forms->item(0)->getElementsByTagName('button');

        self::assertCount(3, $inputs ?? []);
        self::assertCount(1, $buttons ?? []);
        self::assertSame('csrf_token', $inputs->item(0)?->getAttribute('name'));
        self::assertSame('correo', $inputs->item(1)?->getAttribute('name'));
        self::assertSame('clave', $inputs->item(2)?->getAttribute('name'));
        self::assertSame('submit', $buttons->item(0)?->getAttribute('type'));

        [self::$sessionName, self::$sessionId] = explode('=', explode(';', $response->getHeader('set-cookie')[0])[0]);
        self::$csrfToken = $inputs->item(0)->getAttribute('value');
    }

    public function test_with_valid_credentials(): void
    {
        $body = http_build_query([
            'correo' => 'admin@sipan.com',
            'clave' => 'admin123',
        ]);

        $request = self::$requestFactory
            ->createRequest('post', './login')
            ->withBody(self::$streamFactory->createStream($body))
            ->withHeader('content-type', 'application/x-www-form-urlencoded')
            ->withHeader('cookie', "{$this::$sessionName}={$this::$sessionId}")
            ->withHeader('X-CSRF-Token', self::$csrfToken);

        $response = self::$client->sendRequest($request);
        $body = (string) $response->getBody();

        self::assertSame('application/json', $response->getHeaderLine('content-type'));

        $body = json_decode($body, true);

        self::assertIsArray($body);
        self::assertTrue($body['success']);
        self::assertSame('Inicio de sesión exitoso', $body['message']);
    }

    public function test_with_empty_credentials(): void
    {
        $response = self::$client->sendRequest(self::$requestFactory->createRequest('post', './login'));
        $body = (string) $response->getBody();

        self::assertSame('application/json', $response->getHeaderLine('content-type'));

        $body = json_decode($body, true);

        self::assertIsArray($body);
        self::assertFalse($body['success']);
        self::assertSame('Correo y contraseña son requeridos', $body['message']);
    }

    /** @dataProvider getInvalidCredentials */
    public function test_with_invalid_credentials(string $email, string $password): void
    {
        $body = http_build_query([
            'correo' => $email,
            'clave' => $password,
        ]);

        $request = self::$requestFactory
            ->createRequest('post', './login')
            ->withBody(self::$streamFactory->createStream($body))
            ->withHeader('content-type', 'application/x-www-form-urlencoded')
            ->withHeader('cookie', "{$this::$sessionName}={$this::$sessionId}")
            ->withHeader('X-CSRF-Token', self::$csrfToken);

        $response = self::$client->sendRequest($request);
        $body = (string) $response->getBody();

        self::assertSame('application/json', $response->getHeaderLine('content-type'));

        $body = json_decode($body, true);

        self::assertIsArray($body);
        self::assertFalse($body['success']);
        self::assertSame('Credenciales incorrectas', $body['message']);
    }

    /** @return array<string, array{string, string}> */
    public static function getInvalidCredentials(): array
    {
        return [
            'invalid email' => [uniqid(), 'admin123'],
            'invalid password' => ['admin@sipan.com', uniqid()],
        ];
    }

    public function test_without_csrf_token(): void
    {
        $body = http_build_query([
            'correo' => 'admin@sipan.com',
            'clave' => 'admin123',
        ]);

        $request = self::$requestFactory
            ->createRequest('post', './login')
            ->withBody(self::$streamFactory->createStream($body))
            ->withHeader('content-type', 'application/x-www-form-urlencoded');

        $response = self::$client->sendRequest($request);
        $body = (string) $response->getBody();

        self::assertSame('application/json', $response->getHeaderLine('content-type'));

        $body = json_decode($body, true);

        self::assertIsArray($body);
        self::assertFalse($body['success']);
        self::assertSame('Token de seguridad inválido. Por favor, recarga la página.', $body['message']);
    }

    public function test_with_an_invalid_csrf_token(): void
    {
        $body = http_build_query([
            'correo' => 'admin@sipan.com',
            'clave' => 'admin123',
        ]);

        $request = self::$requestFactory
            ->createRequest('post', './login')
            ->withBody(self::$streamFactory->createStream($body))
            ->withHeader('content-type', 'application/x-www-form-urlencoded')
            ->withHeader('cookie', "{$this::$sessionName}={$this::$sessionId}")
            ->withHeader('X-CSRF-Token', 'invalid_token');

        $response = self::$client->sendRequest($request);
        $body = (string) $response->getBody();

        self::assertSame('application/json', $response->getHeaderLine('content-type'));

        $body = json_decode($body, true);

        self::assertIsArray($body);
        self::assertFalse($body['success']);
        self::assertSame('Token de seguridad inválido. Por favor, recarga la página.', $body['message']);
    }

    public function test_without_session_id(): void
    {
        $body = http_build_query([
            'correo' => 'admin@sipan.com',
            'clave' => 'admin123',
        ]);

        $request = self::$requestFactory
            ->createRequest('post', './login')
            ->withBody(self::$streamFactory->createStream($body))
            ->withHeader('content-type', 'application/x-www-form-urlencoded')
            ->withHeader('X-CSRF-Token', self::$csrfToken);

        $response = self::$client->sendRequest($request);
        $body = (string) $response->getBody();

        self::assertSame('application/json', $response->getHeaderLine('content-type'));

        $body = json_decode($body, true);

        self::assertIsArray($body);
        self::assertFalse($body['success']);
        self::assertSame('Token de seguridad inválido. Por favor, recarga la página.', $body['message']);
    }

    public function test_with_an_invalid_session_id(): void
    {
        $body = http_build_query([
            'correo' => 'admin@sipan.com',
            'clave' => 'admin123',
        ]);

        $request = self::$requestFactory
            ->createRequest('post', './login')
            ->withBody(self::$streamFactory->createStream($body))
            ->withHeader('content-type', 'application/x-www-form-urlencoded')
            ->withHeader('cookie', "{$this::$sessionName}=" . uniqid())
            ->withHeader('X-CSRF-Token', self::$csrfToken);

        $response = self::$client->sendRequest($request);
        $body = (string) $response->getBody();

        self::assertSame('application/json', $response->getHeaderLine('content-type'));

        $body = json_decode($body, true);

        self::assertIsArray($body);
        self::assertFalse($body['success']);
        self::assertSame('Token de seguridad inválido. Por favor, recarga la página.', $body['message']);
    }
}
