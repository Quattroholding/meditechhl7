<?php

namespace Tests\Feature\Dashboard\Accounting;

use App\Livewire\Dashboard\Accounting\RevenueChart;
use App\Models\Client;
use App\Models\ClientInvoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RevenueChartTest extends TestCase
{
    use RefreshDatabase;

    public function test_revenue_chart_loads_data_for_last_six_months(): void
    {
        $component = new RevenueChart();
        $component->mount();

        $this->assertCount(6, $component->months);
        $this->assertCount(6, $component->totalRevenues);
        $this->assertCount(6, $component->netUtilities);
        $this->assertCount(6, $component->taxAmounts);
    }

    public function test_revenue_chart_calculates_total_revenue_correctly(): void
    {
        $client = Client::factory()->create();

        ClientInvoice::factory()
            ->for($client)
            ->create([
                'status' => 'paid',
                'paid_at' => now(),
                'total' => 500.00,
                'subtotal' => 467.29,
                'tax_amount' => 32.71,
            ]);

        ClientInvoice::factory()
            ->for($client)
            ->create([
                'status' => 'paid',
                'paid_at' => now(),
                'total' => 250.00,
                'subtotal' => 233.65,
                'tax_amount' => 16.35,
            ]);

        $component = new RevenueChart();
        $component->mount();

        // Should be 750 for current month (last in array)
        $this->assertEquals(750.00, $component->totalRevenues[5]);
    }

    public function test_revenue_chart_calculates_net_utility_correctly(): void
    {
        $client = Client::factory()->create();

        ClientInvoice::factory()
            ->for($client)
            ->create([
                'status' => 'paid',
                'paid_at' => now(),
                'subtotal' => 100.00,
                'discount_amount' => 20.00,
                'tax_amount' => 5.60,
            ]);

        ClientInvoice::factory()
            ->for($client)
            ->create([
                'status' => 'paid',
                'paid_at' => now(),
                'subtotal' => 100.00,
                'discount_amount' => 0,
                'tax_amount' => 7.00,
            ]);

        $component = new RevenueChart();
        $component->mount();

        // Should be (100-20) + (100-0) = 180 for current month
        $this->assertEquals(180.00, $component->netUtilities[5]);
    }

    public function test_revenue_chart_calculates_tax_amount_correctly(): void
    {
        $client = Client::factory()->create();

        ClientInvoice::factory()
            ->for($client)
            ->create([
                'status' => 'paid',
                'paid_at' => now(),
                'tax_amount' => 50.00,
            ]);

        ClientInvoice::factory()
            ->for($client)
            ->create([
                'status' => 'paid',
                'paid_at' => now(),
                'tax_amount' => 25.00,
            ]);

        $component = new RevenueChart();
        $component->mount();

        // Should be 75.00 for current month
        $this->assertEquals(75.00, $component->taxAmounts[5]);
    }

    public function test_revenue_chart_only_counts_paid_invoices(): void
    {
        $client = Client::factory()->create();

        ClientInvoice::factory()
            ->for($client)
            ->create([
                'status' => 'pending',
                'total' => 500.00,
                'tax_amount' => 50.00,
            ]);

        ClientInvoice::factory()
            ->for($client)
            ->create([
                'status' => 'paid',
                'paid_at' => now(),
                'total' => 300.00,
                'subtotal' => 280.37,
                'discount_amount' => 0,
                'tax_amount' => 19.63,
            ]);

        $component = new RevenueChart();
        $component->mount();

        // Should only include the paid invoice
        $this->assertEquals(300.00, $component->totalRevenues[5]);
        $this->assertEquals(280.37, $component->netUtilities[5]);
        $this->assertEquals(19.63, $component->taxAmounts[5]);
    }
}
