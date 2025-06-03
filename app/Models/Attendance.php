<?php

namespace App\Models;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Attendance extends Model
{
    protected $guarded = [];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            $student->generateQrCode();
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class ,'qrcode','qrcode');
    }

    public function lesson(){
        return $this->belongsTo(Lesson::class ,'lesson_id','id');
    }


    public function generateQrCode()
    {
        // Generate unique string for QR code
        $qrString = 'ATTENDANCE_' . Str::upper(Str::random(10)) . '_' . time();

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
        $filename = 'qrcodes/attendance_' . Str::random(20) . '.png';

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
