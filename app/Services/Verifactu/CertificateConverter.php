<?php

namespace App\Services\Verifactu;

use App\Services\Verifactu\Exceptions\CertificateConversionException;

class CertificateConverter
{
    /**
     * Converts the provided PKCS#12 (PFX) certificate into a PEM file that contains
     * the private key, the public certificate and any intermediate certificates.
     *
     * @param  string       $pfxPath         The absolute path to the PFX file that should be converted.
     * @param  string       $password        The password used to protect the PFX bundle.
     * @param  string|null  $destinationPath Optional destination. If null a temporary file is created.
     *
     * @return string The path to the generated PEM file.
     */
    public function convertPfxToPem(string $pfxPath, string $password, ?string $destinationPath = null): string
    {
        if (!extension_loaded('openssl')) {
            throw new CertificateConversionException('The PHP OpenSSL extension is required to convert PKCS#12 certificates.');
        }

        if (!is_file($pfxPath) || !is_readable($pfxPath)) {
            throw new CertificateConversionException(sprintf('The PFX file "%s" is not readable.', $pfxPath));
        }

        $pfxContent = file_get_contents($pfxPath);
        if ($pfxContent === false) {
            throw new CertificateConversionException(sprintf('Unable to read the PFX file "%s".', $pfxPath));
        }

        $certificates = [];
        if (!openssl_pkcs12_read($pfxContent, $certificates, $password)) {
            throw new CertificateConversionException('Unable to parse the PFX bundle. Please check the password or file integrity.');
        }

        $pemContent = $this->buildPemContent($certificates);

        if ($destinationPath === null) {
            $destinationPath = $this->createTemporaryPemPath();
        }

        if (file_put_contents($destinationPath, $pemContent) === false) {
            throw new CertificateConversionException(sprintf('Unable to write the PEM file to "%s".', $destinationPath));
        }

        @chmod($destinationPath, 0600);

        return $destinationPath;
    }

    /**
     * @param  array<string, mixed>  $certificates
     */
    private function buildPemContent(array $certificates): string
    {
        $segments = [];

        if (!empty($certificates['pkey'])) {
            $segments[] = $this->normalizePemSegment($certificates['pkey']);
        }

        if (!empty($certificates['cert'])) {
            $segments[] = $this->normalizePemSegment($certificates['cert']);
        }

        if (!empty($certificates['extracerts']) && is_array($certificates['extracerts'])) {
            foreach ($certificates['extracerts'] as $extraCertificate) {
                if (!empty($extraCertificate)) {
                    $segments[] = $this->normalizePemSegment($extraCertificate);
                }
            }
        }

        if (empty($segments)) {
            throw new CertificateConversionException('The PFX file did not contain any exportable certificates.');
        }

        return implode(PHP_EOL, $segments) . PHP_EOL;
    }

    private function normalizePemSegment(string $segment): string
    {
        $segment = trim($segment);

        if ($segment === '') {
            throw new CertificateConversionException('An empty certificate segment was encountered while building the PEM file.');
        }

        if (!str_contains($segment, '-----BEGIN')) {
            $segment = "-----BEGIN CERTIFICATE-----\n" . chunk_split(base64_encode($segment), 64, "\n") . "-----END CERTIFICATE-----";
        }

        return $segment;
    }

    private function createTemporaryPemPath(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'vf_pem_');
        if ($path === false) {
            throw new CertificateConversionException('Unable to create a temporary PEM file.');
        }

        return $path;
    }
}
