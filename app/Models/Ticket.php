<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'tickets';

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode',
        'user_id',
        'unit_id',
        'category_id',
        'judul',
        'deskripsi',
        'prioritas',
        'status',
        'teknisi_id',
        'lampiran',
        'waktu_selesai',
    ];

    /**
     * Casting atribut tipe data khusus.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'waktu_selesai' => 'datetime',
        ];
    }

    /**
     * Relasi ke User yang membuat laporan tiket ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias relasi ke pelapor untuk kejelasan penamaan domain.
     */
    public function pelapor(): BelongsTo
    {
        return $this->user();
    }

    /**
     * Relasi ke Teknisi (User) yang ditugaskan menangani tiket ini.
     */
    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    /**
     * Relasi ke Unit asal pelapor / lokasi keluhan terjadi.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Relasi ke Kategori masalah IT (Komputer, Jaringan, dll.).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Relasi ke daftar komentar dan diskusi teknis pada tiket ini.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id');
    }
}
