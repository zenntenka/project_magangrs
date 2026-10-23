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

    /**
     * Scope untuk memfilter data tiket pada modul laporan admin.
     * Dipisahkan ke scope model agar query dapat dipakai ulang secara konsisten
     * pada tampilan web, ekspor PDF, maupun ekspor Excel/CSV.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterReport($query, array $filters = [])
    {
        return $query
            ->when(!empty($filters['tanggal_mulai']), function ($q) use ($filters) {
                $q->whereDate('tickets.created_at', '>=', $filters['tanggal_mulai']);
            })
            ->when(!empty($filters['tanggal_selesai']), function ($q) use ($filters) {
                $q->whereDate('tickets.created_at', '<=', $filters['tanggal_selesai']);
            })
            ->when(!empty($filters['unit_id']), function ($q) use ($filters) {
                $q->where('tickets.unit_id', $filters['unit_id']);
            })
            ->when(!empty($filters['category_id']), function ($q) use ($filters) {
                $q->where('tickets.category_id', $filters['category_id']);
            })
            ->when(!empty($filters['status']), function ($q) use ($filters) {
                $q->where('tickets.status', $filters['status']);
            })
            ->when(!empty($filters['teknisi_id']), function ($q) use ($filters) {
                if ($filters['teknisi_id'] === 'unassigned') {
                    $q->whereNull('tickets.teknisi_id');
                } else {
                    $q->where('tickets.teknisi_id', $filters['teknisi_id']);
                }
            });
    }

    /**
     * Helper durasi penyelesaian tiket dalam format yang mudah dibaca.
     */
    public function getDurasiPenyelesaianAttribute(): string
    {
        if (!$this->waktu_selesai) {
            return '-';
        }

        return $this->created_at->diffForHumans($this->waktu_selesai, true);
    }
}
