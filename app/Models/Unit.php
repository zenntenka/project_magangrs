<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'units';

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
    ];

    /**
     * Relasi ke seluruh pengguna (users) yang berada di unit ini.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'unit_id');
    }

    /**
     * Relasi ke seluruh tiket keluhan yang dilaporkan dari unit ini.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'unit_id');
    }
}
