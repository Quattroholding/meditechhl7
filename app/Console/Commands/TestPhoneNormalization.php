<?php

namespace App\Console\Commands;

use App\Channels\WhatsAppChannel;
use Illuminate\Console\Command;

class TestPhoneNormalization extends Command
{
    protected $signature = 'phone:test-normalization {phone?}';

    protected $description = 'Test phone number normalization for WhatsApp';

    public function handle()
    {
        $testPhone = $this->argument('phone');

        if ($testPhone) {
            // Test single phone number
            $this->testSinglePhone($testPhone);
        } else {
            // Test sample phones from database
            $this->testSamplePhones();
        }

        return 0;
    }

    protected function testSinglePhone($phone)
    {
        $this->info("Testing phone: {$phone}");

        $channel = new WhatsAppChannel;
        $reflection = new \ReflectionClass($channel);
        $method = $reflection->getMethod('normalizePhoneNumber');
        $method->setAccessible(true);

        $normalized = $method->invoke($channel, $phone);

        if ($normalized) {
            $this->info("✓ Normalized: {$normalized}");
        } else {
            $this->warn('✗ Invalid phone number (returned null)');
        }
    }

    protected function testSamplePhones()
    {
        $testCases = [
            // Panama formats
            '+50760016054' => '+50760016054',
            '507 6694-8409' => '+50766948409',
            '50769121653' => '+50769121653',
            '+507 6235-4942' => '+50762354942',
            '(507) 66266088' => '+50766266088',
            '6719-7970' => '+50767197970',
            '6893-1811' => '+50768931811',
            '6663-5247' => '+50766635247',

            // USA formats
            '+1 (254) 220-3885' => '+12542203885',
            '+14705195640' => '+14705195640',
            '+1(901)297-5869' => '+19012975869',
            '1(254)4662129' => '+12544662129',

            // Colombia formats
            '+573052721165' => '+573052721165',
            '+573205415764' => '+573205415764',
            '573006089255' => '+573006089255',
            '57 3224039549' => '+573224039549',

            // Ecuador formats
            '+593-98-458-9708' => '+5939845897088',
            '+593 96 891 1181' => '+59396891118',

            // Invalid formats
            '0' => null,
            '507' => null,
            '' => null,
        ];

        $this->info('Testing phone number normalization:');
        $this->newLine();

        $passed = 0;
        $failed = 0;

        $channel = new WhatsAppChannel;
        $reflection = new \ReflectionClass($channel);
        $method = $reflection->getMethod('normalizePhoneNumber');
        $method->setAccessible(true);

        foreach ($testCases as $input => $expected) {
            $result = $method->invoke($channel, $input);

            if ($result === $expected) {
                $this->info("✓ {$input} → {$result}");
                $passed++;
            } else {
                $this->error("✗ {$input} → Expected: {$expected}, Got: {$result}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Results: {$passed} passed, {$failed} failed");
    }
}
