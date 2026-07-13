<?php

namespace App\Console\Commands;

use App\Models\ClientPreference;
use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Spatie\Dropbox\Client as DropboxClient;

class TestDropboxConnection extends Command
{
    protected $signature = 'dropbox:test {media_id?}';

    protected $description = 'Test Dropbox connection and URL generation';

    public function handle()
    {
        $mediaId = $this->argument('media_id');

        if ($mediaId) {
            $this->testMediaFile($mediaId);
        } else {
            $this->testConnection();
        }
    }

    protected function testConnection()
    {
        $this->info('Testing Dropbox connection...');

        // Get first client with Dropbox configured
        $config = ClientPreference::where('preference_type', 'external_storage')
            ->where('preference_key', 'config')
            ->first();

        if (! $config) {
            $this->error('No Dropbox configuration found');

            return 1;
        }

        $this->info('Client ID: '.$config->client_id);

        $value = $config->preference_value;

        if (! isset($value['access_token'])) {
            $this->error('No access token found');

            return 1;
        }

        try {
            $accessToken = Crypt::decryptString($value['access_token']);
            $this->info('Access token decrypted successfully');

            $client = new DropboxClient($accessToken);

            // Test getting account info
            $this->info('Testing account info...');
            $accountInfo = $client->rpcEndpointRequest('users/get_current_account');
            $this->info('Account: '.$accountInfo['name']['display_name']);
            $this->info('Email: '.$accountInfo['email']);

            $this->info('✓ Connection successful!');

            // List recent media files
            $this->info("\nRecent media files:");
            $media = Media::where('storage_disk', 'like', '%external%')
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();

            foreach ($media as $m) {
                $this->line("ID: {$m->id} | File: {$m->file_name} | External ID: {$m->external_id}");
            }

            $this->info("\nRun: php artisan dropbox:test <media_id> to test a specific file");

        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());
            $this->error('Trace: '.$e->getTraceAsString());

            return 1;
        }

        return 0;
    }

    protected function testMediaFile($mediaId)
    {
        $this->info("Testing media file ID: {$mediaId}");

        $media = Media::find($mediaId);

        if (! $media) {
            $this->error('Media not found');

            return 1;
        }

        $this->info("File: {$media->file_name}");
        $this->info("Path: {$media->file_path}");
        $this->info("External ID: {$media->external_id}");

        // Get client config
        $clientId = $media->encounter?->appointment?->client_id ?? auth()->user()?->default_client_id;

        if (! $clientId) {
            $this->error('Cannot determine client_id');

            return 1;
        }

        $config = ClientPreference::getExternalStorageConfig($clientId);

        if (! $config || ! isset($config['access_token'])) {
            $this->error("No Dropbox configuration found for client {$clientId}");

            return 1;
        }

        try {
            $accessToken = Crypt::decryptString($config['access_token']);
            $client = new DropboxClient($accessToken);

            // Try with external_id
            $this->info("\n1. Testing with external_id: {$media->external_id}");
            try {
                $result = $client->getTemporaryLink($media->external_id);
                $url = is_array($result) ? $result['link'] : $result;
                $this->info("✓ Success! URL: {$url}");
            } catch (\Exception $e) {
                $this->error('✗ Failed: '.$e->getMessage());

                // Try with path
                $this->info("\n2. Testing with file_path: {$media->file_path}");
                try {
                    $result = $client->getTemporaryLink($media->file_path);
                    $url = is_array($result) ? $result['link'] : $result;
                    $this->info("✓ Success with path! URL: {$url}");
                } catch (\Exception $e2) {
                    $this->error('✗ Also failed with path: '.$e2->getMessage());

                    // Try getting metadata
                    $this->info("\n3. Testing file existence with metadata...");
                    try {
                        $metadata = $client->getMetadata($media->file_path);
                        $this->info('✓ File exists in Dropbox!');
                        $this->info('Dropbox ID: '.$metadata['id']);
                        $this->info('Size: '.$metadata['size'].' bytes');

                        // Try with the real Dropbox ID from metadata
                        $this->info("\n4. Testing with metadata ID: {$metadata['id']}");
                        try {
                            $result = $client->getTemporaryLink($metadata['id']);
                            $url = is_array($result) ? $result['link'] : $result;
                            $this->info("✓ Success with metadata ID! URL: {$url}");

                            $this->warn("\n⚠ The external_id in database doesn't match Dropbox's actual ID!");
                            $this->warn("Database has: {$media->external_id}");
                            $this->warn("Should be: {$metadata['id']}");
                        } catch (\Exception $e3) {
                            $this->error('✗ Failed: '.$e3->getMessage());
                        }
                    } catch (\Exception $e3) {
                        $this->error('✗ File not found in Dropbox: '.$e3->getMessage());
                    }
                }
            }

        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
