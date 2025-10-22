<?php

namespace Tests\Unit\Verifactu;

use App\Services\Verifactu\SoapClientFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use SoapFault;

class SoapClientFactoryTest extends TestCase
{
    private string $pemPath;

    protected function setUp(): void
    {
        parent::setUp();

        $path = tempnam(sys_get_temp_dir(), 'vf_pem_test_');
        if ($path === false) {
            $this->fail('Unable to create a temporary PEM file for testing.');
        }

        file_put_contents($path, <<<PEM
-----BEGIN CERTIFICATE-----
RHVtbXkgQ0VSVElGSUNBVEUK-----END CERTIFICATE-----
-----BEGIN PRIVATE KEY-----
RHVtbXkgUFJJVkFURSBLRVkK-----END PRIVATE KEY-----
PEM);

        $this->pemPath = $path;
    }

    protected function tearDown(): void
    {
        @unlink($this->pemPath);
        parent::tearDown();
    }

    public function testItCreatesClientWithCertificateAndTlsOptions(): void
    {
        $config = [
            'connection_timeout' => 25,
            'wsdl_cache' => 0,
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
            'environments' => [
                'sandbox' => [
                    'wsdl' => 'https://example.test/wsdl',
                    'endpoint' => 'https://example.test/endpoint',
                ],
            ],
        ];

        $logger = new class extends AbstractLogger {
            public array $records = [];

            public function log($level, $message, array $context = []): void
            {
                $this->records[] = compact('level', 'message', 'context');
            }
        };

        $captured = null;
        $factory = new SoapClientFactory($config, $logger, function (string $wsdl, array $options) use (&$captured) {
            $captured = compact('wsdl', 'options');

            return new class extends \SoapClient {
                public function __construct() {}
            };
        });

        $client = $factory->create('sandbox', $this->pemPath, 'secret');

        $this->assertInstanceOf(\SoapClient::class, $client);
        $this->assertSame('https://example.test/wsdl', $captured['wsdl']);
        $this->assertSame($this->pemPath, $captured['options']['local_cert']);
        $this->assertSame('secret', $captured['options']['passphrase']);
        $this->assertSame('https://example.test/endpoint', $captured['options']['location']);
        $this->assertSame(25, $captured['options']['connection_timeout']);

        $streamOptions = stream_context_get_options($captured['options']['stream_context']);
        $this->assertSame($this->pemPath, $streamOptions['ssl']['local_cert']);
        $this->assertSame('secret', $streamOptions['ssl']['passphrase']);
        $this->assertTrue($streamOptions['ssl']['verify_peer']);
        $this->assertTrue($streamOptions['ssl']['verify_peer_name']);
        $this->assertArrayHasKey('crypto_method', $streamOptions['ssl']);
    }

    public function testItFallsBackToLocalWsdl(): void
    {
        $localWsdl = tempnam(sys_get_temp_dir(), 'vf_wsdl_');
        if ($localWsdl === false) {
            $this->fail('Unable to create a temporary WSDL file.');
        }

        file_put_contents($localWsdl, '<definitions></definitions>');

        $config = [
            'environments' => [
                'sandbox' => [
                    'wsdl' => 'https://example.test/wsdl',
                    'local_wsdl' => $localWsdl,
                ],
            ],
        ];

        $logger = new class extends AbstractLogger {
            public array $records = [];

            public function log($level, $message, array $context = []): void
            {
                $this->records[] = compact('level', 'message', 'context');
            }
        };

        $attempts = [];
        $factory = new SoapClientFactory($config, $logger, function (string $wsdl, array $options) use (&$attempts, $localWsdl) {
            $attempts[] = $wsdl;

            if ($wsdl !== $localWsdl) {
                throw new SoapFault('WSDL', 'remote unavailable');
            }

            return new class extends \SoapClient {
                public function __construct() {}
            };
        });

        $factory->create('sandbox', $this->pemPath, 'secret');

        $this->assertSame(['https://example.test/wsdl', $localWsdl], $attempts);
        $this->assertNotEmpty($logger->records);
        $this->assertSame('warning', $logger->records[0]['level']);
        $this->assertSame('Verifactu: error carregant el WSDL remot, utilitzant la còpia local', $logger->records[0]['message']);
        $this->assertSame('WSDL', $logger->records[0]['context']['fault_code']);
        $this->assertSame('remote unavailable', $logger->records[0]['context']['fault_message']);

        @unlink($localWsdl);
    }

    public function testItThrowsWhenEnvironmentIsUnknown(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $logger = new class extends AbstractLogger {
            public function log($level, $message, array $context = []): void {}
        };

        $factory = new SoapClientFactory(['environments' => []], $logger);
        $factory->create('missing', $this->pemPath, 'secret');
    }

    public function testStreamContextOverrideMergesWithDefaults(): void
    {
        $config = [
            'ssl' => [
                'verify_peer' => true,
            ],
            'environments' => [
                'sandbox' => [
                    'wsdl' => 'https://example.test/wsdl',
                ],
            ],
        ];

        $logger = new class extends AbstractLogger {
            public function log($level, $message, array $context = []): void {}
        };

        $captured = null;
        $factory = new SoapClientFactory($config, $logger, function (string $wsdl, array $options) use (&$captured) {
            $captured = $options;

            return new class extends \SoapClient {
                public function __construct() {}
            };
        });

        $factory->create('sandbox', $this->pemPath, 'secret', [
            'stream_context' => [
                'ssl' => [
                    'verify_peer' => false,
                    'ciphers' => 'DEFAULT',
                ],
            ],
        ]);

        $options = stream_context_get_options($captured['stream_context']);
        $this->assertFalse($options['ssl']['verify_peer']);
        $this->assertSame('DEFAULT', $options['ssl']['ciphers']);
        $this->assertSame($this->pemPath, $options['ssl']['local_cert']);
        $this->assertSame('secret', $options['ssl']['passphrase']);
    }
}
