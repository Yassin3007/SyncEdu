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

class Student extends Model
{
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
        'wallet_balance'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            $student->generateQrCode();
        });
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
