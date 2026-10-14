<?php

namespace App\Models;

use App\Enums\SignatureRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservationSignature extends Model
{
    protected $table = 'firmas_observaciones';

    protected $fillable = ['observation_id', 'signature_role', 'signer_name', 'is_present'];

    protected function casts(): array
    {
        return [
            'signature_role' => SignatureRole::class,
            'is_present'     => 'boolean',
        ];
    }

    public function observation(): BelongsTo
    {
        return $this->belongsTo(Observation::class);
    }
}
