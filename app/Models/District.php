<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_ar',
        'city_en',
        'city_ar',
    ];

    /**
     * Append the localized name attribute
     */
    protected $appends = ['name', 'city'];

    /**
     * Get the name according to current locale
     */
    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }

    /**
     * Get the city according to current locale
     */
    public function getCityAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->city_ar : $this->city_en;
    }

    /**
     * Get name in specific language
     */
    public function getName($lang = null)
    {
        if (!$lang) {
            $lang = app()->getLocale();
        }

        return $lang === 'ar' ? $this->name_ar : $this->name_en;
    }

    /**
     * Get city in specific language
     */
    public function getCity($lang = null)
    {
        if (!$lang) {
            $lang = app()->getLocale();
        }

        return $lang === 'ar' ? $this->city_ar : $this->city_en;
    }

    /**
     * Scope to search by name in both languages
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name_en', 'like', "%{$search}%")
                ->orWhere('name_ar', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to filter by city
     */
    public function scopeByCity($query, $city)
    {
        return $query->where(function($q) use ($city) {
            $q->where('city_en', $city)
                ->orWhere('city_ar', $city);
        });
    }

    /**
     * Get districts with localized names for API responses
     */
    public function toArray()
    {
        $array = parent::toArray();

        // Add localized fields
        $array['name'] = $this->getName();
        $array['city'] = $this->getCity();

        return $array;
    }
}
