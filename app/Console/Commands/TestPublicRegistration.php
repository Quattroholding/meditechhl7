<?php

namespace App\Console\Commands;

use App\Http\Controllers\PublicRegistrationController;
use App\Http\Requests\PublicRegistrationRequest;
use App\Services\FileService;
use App\Services\NeoPaymentsService;
use App\Services\PractitionerService;
use App\Services\ReferralService;
use App\Services\SubscriptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class TestPublicRegistration extends Command
{
    protected $signature = 'test:public-registration';

    protected $description = 'Test public registration with random user data and test credit card';

    public function handle()
    {
        $this->info('🧪 Testing Public Registration with NeoPayments...');
        $this->newLine();

        // Generate random user data
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $email = fake()->unique()->safeEmail();
        $phone = '+507'.fake()->numerify('########'); // Panama phone format
        $password = 'Test1234!';

        // Test credit card data (VISA test card)
        $cardNumber = '4196581200000003';
        $cardHolder = strtoupper($firstName.' '.$lastName);
        $cvv = '123';
        $expMonth = '12';
        $expYear = '27';

        // Package and practitioner data
        $packageId = 1; // Básico
        $identifierType = 'CC';
        $identifier = fake()->numerify('8-###-####');
        $gender = fake()->randomElement(['male', 'female']);
        $medicalSpecialityId = 3; // Cardiología

        $this->table(
            ['Field', 'Value'],
            [
                ['First Name', $firstName],
                ['Last Name', $lastName],
                ['Email', $email],
                ['Phone', $phone],
                ['Password', $password],
                ['Package', 'Básico (ID: 1)'],
                ['Identifier', $identifier],
                ['Gender', $gender],
                ['Speciality', 'Cardiología (ID: 3)'],
                ['Card Number', $cardNumber],
                ['Card Holder', $cardHolder],
                ['Exp Date', $expMonth.'/'.$expYear],
                ['CVV', $cvv],
            ]
        );

        $this->newLine();
        $this->info('Sending registration request...');

        // Prepare request data
        $data = [
            // User data
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'password_confirmation' => $password,

            // Package
            'package_id' => $packageId,

            // Practitioner (required for package with max_users=1)
            'identifier_type' => $identifierType,
            'identifier' => $identifier,
            'gender' => $gender,
            'medical_speciality' => $medicalSpecialityId,

            // Credit Card
            'card_number' => $cardNumber,
            'card_holder' => $cardHolder,
            'exp_month' => $expMonth,
            'exp_year' => $expYear,

            // Terms
            'terms_and_privacy' => '1',
        ];

        try {
            // Create a mock request
            $request = PublicRegistrationRequest::create('/register/client', 'POST', $data);
            $request->setLaravelSession(app('session.store'));

            // Instantiate services
            $subscriptionService = app(SubscriptionService::class);
            $fileService = app(FileService::class);
            $practitionerService = app(PractitionerService::class);
            $referralService = app(ReferralService::class);
            $neoPaymentsService = app(NeoPaymentsService::class);

            // Create controller instance
            $controller = new PublicRegistrationController;

            $this->newLine();

            // Call the store method
            $response = $controller->store(
                $request,
                $subscriptionService,
                $fileService,
                $practitionerService,
                $referralService,
                $neoPaymentsService
            );

            if ($response->isRedirect() && str_contains($response->getTargetUrl(), '/register/success')) {
                $this->info('✅ Registration successful!');
                $this->newLine();
                $this->info('Redirected to: '.$response->getTargetUrl());
            } else {
                $this->error('❌ Registration failed or unexpected redirect!');
                $this->error('Redirect URL: '.$response->getTargetUrl());
            }

            $this->newLine();
            $this->info('📋 Check the logs for detailed information:');
            $this->info('tail -f storage/logs/laravel-'.now()->format('Y-m-d').'.log');

            return Command::SUCCESS;
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error('❌ Validation failed!');
            $this->newLine();
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->error("  • $field: $message");
                }
            }

            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('❌ Exception occurred!');
            $this->error($e->getMessage());
            $this->error($e->getTraceAsString());

            return Command::FAILURE;
        }
    }
}
