<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class Guardian extends Authenticatable
{
    use  HasApiTokens ;

    protected $guarded = [] ;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
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

    public function setImageAttribute($value)
    {
        if ($value && is_object($value) && method_exists($value, 'store')) {
            // If it's an uploaded file, store it
            $this->attributes['image'] = $value->store('guardians', 'public');
        } elseif (is_string($value)) {
            // If it's already a string path, use it directly
            $this->attributes['image'] = $value;
        } else {
            // If null or empty, set to null
            $this->attributes['image'] = null;
        }
    }

    /**
     * Get the image attribute - returns full URL
     */
    public function getImageAttribute($value)
    {
        if ($value) {
            return Storage::disk('public')->url($value);
        }

        return null;
    }

    public function deleteOldImage($oldImagePath = null)
    {
        $pathToDelete = $oldImagePath ?: $this->attributes['image'];

        if ($pathToDelete && Storage::disk('public')->exists($pathToDelete)) {
            Storage::disk('public')->delete($pathToDelete);
        }
    }
}
