<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'unit_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke Unit asal user (misal instalasi / ruangan di RS).
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Tiket yang diajukan oleh user ini (sebagai pelapor).
     */
    public function reportedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    /**
     * Alias pelapor tickets()
     */
    public function tickets(): HasMany
    {
        return $this->reportedTickets();
    }

    /**
     * Tiket yang ditugaskan kepada teknisi ini.
     */
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'teknisi_id');
    }

    /**
     * Komentar yang dikirimkan oleh user ini di tiket.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'user_id');
    }

    /**
     * Helper cek apakah user memiliki role admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Helper cek apakah user memiliki role teknisi.
     */
    public function isTeknisi(): bool
    {
        return $this->role === 'teknisi';
    }

    /**
     * Helper cek apakah user memiliki role pelapor.
     */
    public function isPelapor(): bool
    {
        return $this->role === 'pelapor';
    }
}
