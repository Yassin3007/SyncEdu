<?php

namespace App\Models;

use App\Filters\Filters;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use  HasApiTokens ;
    protected $fillable = [
        'name_en',
        'name_ar',
        'national_id',
        'guardian_number',
        'phone',
        'division_id',
        'school',
        'stage_id',
        'grade_id',
        'subscription_type',
        'wallet_balance',
        'password',
        'image',
        'verified_at',
        'verification_code',
        'status',
        'points'
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            $student->generateQrCode();
        });
    }

    public function generateVerificationCode()
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'verification_code' => $code,
//            'verification_code_expires_at' => now()->addMinutes(15), // Code expires in 15 minutes
        ]);

        return $code;
    }

    /**
     * Verify the provided code
     */
    public function verifyCode($code)
    {
        return $this->verification_code === $code ;
//            $this->verification_code_expires_at &&
//            $this->verification_code_expires_at->isFuture();
    }

    /**
     * Clear verification code after use
     */
    public function clearVerificationCode()
    {
        $this->update([
            'verification_code' => null,
//            'verification_code_expires_at' => null,
        ]);
    }

    protected $appends = ['name'] ;

    public function scopeFilter($query, Filters $filter)
    {
        return $filter->apply($query);
    }

    public function getNameAttribute(){
        return $this->{'name_' . app()->getLocale()};
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function grade(){
        return $this->belongsTo(Grade::class);
    }

    /**
     * Generate QR code using endroid/qr-code (no deprecation warnings)
     */
    public function generateQrCode()
    {
        // Generate unique string for QR code
        $qrString = 'STUDENT_' . Str::upper(Str::random(10)) . '_' . time();

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
        $filename = 'qrcodes/student_' . Str::random(20) . '.png';

        // Store the QR code image
        Storage::disk('public')->put($filename, $result->getString());

        // Set the image path
        $this->qrcode_image = $filename;
    }

    public function getQrCodeUrlAttribute()
    {
        if (!$this->qrcode_image) {
            return null;
        }

        // Return full URL path
        return url(Storage::url($this->qrcode_image));
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
