<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
    ];

    /**
     * Relasi ke seluruh tiket yang dikelompokkan dalam kategori ini.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }
}
