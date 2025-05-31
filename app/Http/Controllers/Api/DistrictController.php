<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    /**
     * Display a listing of all districts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = District::query();

            // Optional: Filter by city if provided
            if ($request->has('city')) {
                $query->byCity($request->city);
            }

            // Optional: Search by name
            if ($request->has('search')) {
                $query->search($request->search);
            }

            // Optional: Order by name (localized)
            $locale = app()->getLocale();
            $orderField = $locale === 'ar' ? 'name_ar' : 'name_en';
            $query->orderBy($orderField);

            // Optional: Pagination
            if ($request->has('per_page')) {
                $perPage = min($request->per_page, 100); // Limit to 100 per page
                $districts = $query->paginate($perPage);
            } else {
                $districts = $query->get();
            }

            return response()->json([
                'success' => true,
                'data' => $districts,
                'message' => 'Districts retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving districts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get districts for a specific city (alternative method)
     */
    public function getByCity($city = 'Cairo')
    {
        try {
            $locale = app()->getLocale();
            $orderField = $locale === 'ar' ? 'name_ar' : 'name_en';

            $districts = District::byCity($city)
                ->orderBy($orderField)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $districts,
                'count' => $districts->count(),
                'message' => "Districts for {$city} retrieved successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving districts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get districts with specific language
     */
    public function getByLanguage(Request $request, $lang = 'en')
    {
        try {
            $query = District::query();

            // Filter by city if provided
            if ($request->has('city')) {
                $query->byCity($request->city);
            }

            // Search if provided
            if ($request->has('search')) {
                $query->search($request->search);
            }

            $orderField = $lang === 'ar' ? 'name_ar' : 'name_en';
            $districts = $query->orderBy($orderField)->get();

            // Transform data to show only requested language
            $transformedData = $districts->map(function ($district) use ($lang) {
                return [
                    'id' => $district->id,
                    'name' => $district->getName($lang),
                    'city' => $district->getCity($lang),
                    'name_en' => $district->name_en,
                    'name_ar' => $district->name_ar,
                    'city_en' => $district->city_en,
                    'city_ar' => $district->city_ar,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedData,
                'count' => $transformedData->count(),
                'language' => $lang,
                'message' => "Districts retrieved in {$lang} language"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving districts: ' . $e->getMessage()
            ], 500);
        }
    }
}
