<?php

namespace App\Rules;

use App\Models\Sector;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class SectorAcceptsTickets implements ValidationRule
{
    public function __construct(
        private ?int $enterpriseId = null
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $enterpriseId = $this->enterpriseId ?? auth()->user()?->enterprise_id;

        $sector = Sector::withoutGlobalScopes()
            ->where('id', $value)
            ->where('enterprise_id', $enterpriseId)
            ->first();

        if (! $sector) {
            $fail('The selected sector does not exist for this enterprise.');

            return;
        }

        if (! $sector->active) {
            $fail('The selected sector is not active.');

            return;
        }

        if (! $sector->accepts_tickets) {
            $fail('The selected sector does not accept tickets.');

            return;
        }
    }
}
