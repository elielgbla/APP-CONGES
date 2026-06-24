<?php

namespace App\Models;

use App\Models\Employe;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journalisation extends Model
{
    protected $table = 'journalisation';
    protected $primaryKey = 'code_log';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'code_log',
        'matricule_employe',
        'action',
        'table_concernee',
        'details',
        'ip_address',
        'date_action',
    ];

    protected static function booted()
    {
        static::creating(function ($journal) {
            if (empty($journal->code_log)) {
                $position = static::count() + 1;
                $journal->code_log = 'LOG' . str_pad($position, 7, '0', STR_PAD_LEFT);
            }

            if (empty($journal->date_action)) {
                $journal->date_action = now();
            }
        });
    }

    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class, 'matricule_employe', 'matricule_employe');
    }
}
