<?php

namespace App\Events;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyGenerated
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public User $user,
        public Company $company,
        public string $country,
        public string $language = 'en',
        public string $currency = 'USD'
    ) {}
}
