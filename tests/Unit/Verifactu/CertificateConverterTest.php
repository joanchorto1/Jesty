<?php

namespace Tests\Unit\Verifactu;

use App\Services\Verifactu\CertificateConverter;
use OpenSSLAsymmetricKey;
use OpenSSLCertificate;
use PHPUnit\Framework\TestCase;

class CertificateConverterTest extends TestCase
{
    public function testItConvertsPfxToPemWithoutExternalBinary(): void
    {
        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('The OpenSSL extension is required for this test.');
        }

        $pfxPassword = 'S3cretPass!';
        $pfxPath = tempnam(sys_get_temp_dir(), 'vf_pfx_');
        $pemPath = tempnam(sys_get_temp_dir(), 'vf_pem_');

        try {
            [$certificate, $privateKey] = $this->generateSelfSignedCertificate();

            $exported = openssl_pkcs12_export($certificate, $pfxBundle, $privateKey, $pfxPassword);
            $this->assertTrue($exported, 'Failed to export the PKCS#12 bundle.');

            $writeResult = file_put_contents($pfxPath, $pfxBundle);
            $this->assertIsInt($writeResult);

            $converter = new CertificateConverter();
            $resultPath = $converter->convertPfxToPem($pfxPath, $pfxPassword, $pemPath);

            $this->assertFileExists($resultPath);
            $pemContent = file_get_contents($resultPath);
            $this->assertNotFalse($pemContent);
            $this->assertStringContainsString('-----BEGIN PRIVATE KEY-----', $pemContent);
            $this->assertStringContainsString('-----BEGIN CERTIFICATE-----', $pemContent);
        } finally {
            @unlink($pfxPath);
            @unlink($pemPath);
        }
    }

    /**
     * @return array{0: OpenSSLCertificate|resource|string, 1: OpenSSLAsymmetricKey|resource}
     */
    private function generateSelfSignedCertificate(): array
    {
        $distinguishedName = [
            'countryName' => 'ES',
            'stateOrProvinceName' => 'Barcelona',
            'localityName' => 'Barcelona',
            'organizationName' => 'Jesty Test Suite',
            'organizationalUnitName' => 'QA',
            'commonName' => 'jesty.test',
            'emailAddress' => 'qa@jesty.test',
        ];

        $privateKey = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        $this->assertTrue(is_resource($privateKey) || $privateKey instanceof OpenSSLAsymmetricKey);

        $csr = openssl_csr_new($distinguishedName, $privateKey, ['digest_alg' => 'sha256']);
        $this->assertNotFalse($csr);

        $certificate = openssl_csr_sign($csr, null, $privateKey, 365, ['digest_alg' => 'sha256']);
        $this->assertNotFalse($certificate);

        return [$certificate, $privateKey];
    }
}
