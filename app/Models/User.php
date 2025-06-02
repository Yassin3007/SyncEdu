<?php

namespace App\Models;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles ,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'national_id',
        'phone',
        'start_date',
        'end_date',
        'active',
        'salary',
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
            'start_date' => 'date',
            'end_date' => 'date',
            'active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->generateQrCode();
        });
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('active', false);
    }

    /**
     * Get the user's full name with additional info.
     */
    public function getFullInfoAttribute()
    {
        return $this->name . ' (' . $this->email . ')';
    }

    /**
     * Check if user is currently employed (between start and end date).
     */
    public function isCurrentlyEmployed()
    {
        $today = now()->toDateString();

        if (!$this->start_date) {
            return false;
        }

        if ($this->start_date > $today) {
            return false;
        }

        if ($this->end_date && $this->end_date < $today) {
            return false;
        }

        return true;
    }

    /**
     * Generate QR code using endroid/qr-code (no deprecation warnings)
     */
    public function generateQrCode()
    {
        // Generate unique string for QR code
        $qrString = 'USER_' . Str::upper(Str::random(10)) . '_' . time();

        // Set the QR code string
        $this->qrcode = $qrString;

        // Generate QR code using endroid/qr-code (no deprecation warnings)
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($qrString)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();

        // Create filename
        $filename = 'qrcodes/user_' . Str::random(20) . '.png';

        // Store the QR code image
        Storage::disk('public')->put($filename, $result->getString());

        // Set the image path
        $this->qrcode_image = $filename;
    }

    public function getQrCodeUrlAttribute()
    {
        return $this->qrcode_image ? Storage::url($this->qrcode_image) : null;
    }

    public function regenerateQrCode()
    {
        if ($this->qrcode_image && Storage::disk('public')->exists($this->qrcode_image)) {
            Storage::disk('public')->delete($this->qrcode_image);
        }

        $this->generateQrCode();
        $this->save();
    }
}
