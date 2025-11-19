<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Services\DocumentNumberGenerator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentNumberGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_backdated_invoice_numbers_are_considered_when_generating_sequence(): void
    {
        $company = Company::factory()->create();

        $client = Client::create([
            'company_id' => $company->id,
            'name' => 'Client Test',
            'nif' => 'A12345678',
        ]);

        Invoice::create([
            'company_id' => $company->id,
            'client_id' => $client->id,
            'name' => 'FA-25-0005',
            'date' => Carbon::create(2024, 12, 31),
            'state' => 'in_process',
            'base_imponible' => 100,
            'iva' => 21,
            'monto_iva' => 21,
            'total' => 121,
        ]);

        $nextNumber = DocumentNumberGenerator::generate(
            Invoice::class,
            'name',
            'FA',
            $company->id,
            Carbon::create(2025, 1, 5)
        );

        $this->assertSame('FA-25-0006', $nextNumber);
    }
}
