<?php

namespace App\Services\Verifactu;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use SoapClient;
use SoapFault;
use Throwable;

class SoapClientFactory
{
    /**
     * @param  array<string, mixed>  $config
     * @param  callable(string, array<string, mixed>): SoapClient|null  $clientCreator
     */
    public function __construct(
        private array $config,
        private LoggerInterface $logger,
        private $clientCreator = null
    ) {
        $this->clientCreator ??= static fn (string $wsdl, array $options): SoapClient => new SoapClient($wsdl, $options);
    }

    public function create(string $environment, string $pemPath, string $password, array $overrides = []): SoapClient
    {
        $environmentConfig = $this->config['environments'][$environment] ?? null;
        if ($environmentConfig === null) {
            throw new InvalidArgumentException(sprintf('Unknown VeriFactu environment "%s".', $environment));
        }

        $options = $this->buildOptions($environmentConfig, $pemPath, $password, $overrides);
        $wsdlCandidates = $this->buildWsdlCandidates($environmentConfig);

        $lastException = null;

        foreach ($wsdlCandidates as $index => $wsdl) {
            try {
                return ($this->clientCreator)($wsdl, $options);
            } catch (SoapFault $fault) {
                $lastException = $fault;

                if ($index === 0 && count($wsdlCandidates) > 1) {
                    $this->logger->warning(
                        'Verifactu: error carregant el WSDL remot, utilitzant la còpia local',
                        [
                            'fault_code' => $fault->faultcode,
                            'fault_message' => $fault->getMessage(),
                        ]
                    );

                    continue;
                }

                throw $fault;
            } catch (Throwable $throwable) {
                $lastException = $throwable;

                if ($index === 0 && count($wsdlCandidates) > 1) {
                    $this->logger->warning(
                        'Verifactu: error carregant el WSDL remot, utilitzant la còpia local',
                        [
                            'fault_code' => get_class($throwable),
                            'fault_message' => $throwable->getMessage(),
                        ]
                    );

                    continue;
                }

                throw $throwable;
            }
        }

        if ($lastException instanceof Throwable) {
            throw $lastException;
        }

        throw new SoapFault('WSDL', 'Unable to create VeriFactu SOAP client: no WSDL candidates available.');
    }

    /**
     * @param  array<string, mixed>  $environmentConfig
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function buildOptions(array $environmentConfig, string $pemPath, string $password, array $overrides = []): array
    {
        $defaults = [
            'trace' => true,
            'exceptions' => true,
            'cache_wsdl' => $this->config['wsdl_cache'] ?? (defined('WSDL_CACHE_NONE') ? WSDL_CACHE_NONE : 0),
            'connection_timeout' => $this->config['connection_timeout'] ?? 30,
            'local_cert' => $pemPath,
        ];

        if ($password !== '') {
            $defaults['passphrase'] = $password;
        }

        if (!empty($environmentConfig['endpoint'])) {
            $defaults['location'] = $environmentConfig['endpoint'];
        }

        $defaults['stream_context'] = $this->mergeStreamContextOptions(
            $overrides['stream_context'] ?? null,
            $pemPath,
            $password
        );

        unset($overrides['stream_context']);

        return array_replace_recursive($defaults, $overrides);
    }

    /**
     * @param  array<string, mixed>  $environmentConfig
     * @return list<string>
     */
    private function buildWsdlCandidates(array $environmentConfig): array
    {
        $candidates = [];

        if (!empty($environmentConfig['wsdl'])) {
            $candidates[] = $environmentConfig['wsdl'];
        }

        $localCandidates = array_filter([
            $environmentConfig['local_wsdl'] ?? null,
            $this->config['local_wsdl'] ?? null,
        ], static fn (?string $path) => is_string($path) && $path !== '' && is_file($path));

        foreach ($localCandidates as $path) {
            if (!in_array($path, $candidates, true)) {
                $candidates[] = $path;
            }
        }

        return $candidates;
    }

    private function mergeStreamContextOptions(mixed $context, string $pemPath, string $password)
    {
        if ($context !== null && !is_array($context) && !is_resource($context)) {
            throw new InvalidArgumentException('The stream_context override must be an array or a resource.');
        }

        $baseOptions = $this->buildDefaultStreamContextOptions($pemPath, $password);
        $incomingOptions = [];

        if (is_array($context)) {
            $incomingOptions = $context;
        } elseif (is_resource($context)) {
            $incomingOptions = stream_context_get_options($context);
        }

        $merged = $this->mergeContextOptions($baseOptions, $incomingOptions);

        return stream_context_create($merged);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildDefaultStreamContextOptions(string $pemPath, string $password): array
    {
        $sslConfig = $this->config['ssl'] ?? [];
        $cryptoMethod = $sslConfig['crypto_method'] ?? null;
        unset($sslConfig['crypto_method']);

        if ($cryptoMethod === null) {
            $cryptoMethod = defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')
                ? STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT
                : STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
        }

        $sslOptions = array_merge(
            [
                'local_cert' => $pemPath,
                'verify_peer' => $sslConfig['verify_peer'] ?? true,
                'verify_peer_name' => $sslConfig['verify_peer_name'] ?? true,
                'allow_self_signed' => $sslConfig['allow_self_signed'] ?? false,
                'crypto_method' => $cryptoMethod,
            ],
            array_filter(
                [
                    'passphrase' => $password,
                    'cafile' => $sslConfig['cafile'] ?? null,
                    'capath' => $sslConfig['capath'] ?? null,
                    'ciphers' => $sslConfig['ciphers'] ?? null,
                ],
                static fn ($value) => $value !== null && $value !== ''
            )
        );

        return ['ssl' => $sslOptions];
    }

    /**
     * @param  array<string, mixed>  $base
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function mergeContextOptions(array $base, array $overrides): array
    {
        $merged = $base;

        foreach ($overrides as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = array_replace($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
