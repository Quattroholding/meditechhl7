<?php

namespace App\Notifications\Concerns;

trait ValidatesEmailChannel
{
    /**
     * Check if email is valid and not reserved by RFC 2606
     *
     * This method validates email addresses and filters out:
     * - Invalid email formats
     * - Reserved domains (RFC 2606): example.com, test.com, localhost, etc.
     * - Common test domains that may cause SMTP errors
     *
     * Use this in the via() method to prevent notification failures:
     * if ($this->isValidEmail($notifiable->email)) {
     *     $channels[] = 'mail';
     * }
     */
    protected function isValidEmail(?string $email): bool
    {
        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Reserved domains by RFC 2606 and common test domains
        $reservedDomains = [
            'example.com',
            'example.org',
            'example.net',
            'test.com',
            'test.org',
            'test.net',
            'localhost',
            'localhost.localdomain',
            'invalid',
        ];

        $domain = strtolower(substr(strrchr($email, '@'), 1));

        return ! in_array($domain, $reservedDomains);
    }

    /**
     * Check if the mail channel should be included based on email validity
     *
     * Helper method to simplify adding mail channel conditionally.
     * Returns 'mail' if email is valid, null otherwise.
     *
     * Usage in via() method:
     * return array_filter([
     *     'database',
     *     $this->getMailChannelIfValid($notifiable->email),
     * ]);
     */
    protected function getMailChannelIfValid(?string $email): ?string
    {
        return $this->isValidEmail($email) ? 'mail' : null;
    }
}
