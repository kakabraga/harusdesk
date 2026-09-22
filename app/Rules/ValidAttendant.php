<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidAttendant implements ValidationRule
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
        if (is_null($value)) {
            return;
        }

        $enterpriseId = $this->enterpriseId ?? auth()->user()?->enterprise_id;

        $user = User::withoutGlobalScopes()
            ->where('id', $value)
            ->where('enterprise_id', $enterpriseId)
            ->first();

        if (! $user) {
            $fail('The selected attendant does not exist for this enterprise.');

            return;
        }

        if (! $user->active) {
            $fail('The selected attendant is inactive.');

            return;
        }

        if (! in_array($user->role, ['attendant', 'admin'])) {
            $fail('The selected user cannot be assigned as an attendant.');

            return;
        }
    }
}
