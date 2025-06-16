<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentAuthController extends Controller
{
    /**
     * Login student by phone/password or national_id/password
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'phone' => 'nullable|string|unique:students,phone',
            'division_id' => 'nullable|string',
            'school' => 'nullable|string|max:255',
            'stage_id' => 'nullable|exists:stages,id',
            'grade_id' => 'nullable|exists:grades,id',
            'district_id' => 'nullable|exists:districts,id',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        do{
            $random_id = rand(1000000000,9999999999);
        }while(Student::where('national_id',$random_id)->exists());

        // Create the student
        $student = Student::create([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'national_id' => $random_id,
            'phone' => $request->phone,
            'division_id' => $request->division_id,
            'school' => $request->school,
            'stage_id' => $request->stage_id,
            'grade_id' => $request->grade_id,
            'district_id' => $request->district_id,
            'password' => Hash::make($request->password),
            'image' => $request->file('image'),
        ]);

        // Create token for auto-login
        $token = $student->createToken('StudentToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Student registered and logged in successfully',
            'student' => new StudentResource($student),
            'token' => $token
        ], 201);
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string', // Can be phone or national_id
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $identifier = $request->identifier;
        $password = $request->password;

        // Try to find student by phone or national_id
        $student = Student::where('phone', $identifier)
            ->orWhere('national_id', $identifier)
            ->first();

        if (!$student || !Hash::check($password, $student->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Create token for the student (using Sanctum)
        $token = $student->createToken('StudentToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'student' => new  StudentResource($student),
            'token' => $token
        ]);
    }

    /**
     * Request password reset - Generate verification code
     */
    public function requestPasswordReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'national_id' => 'required|string',
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find student by both national_id AND phone for security
        $student = Student::where('national_id', $request->national_id)
            ->where('phone', $request->phone)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'No student found with these credentials'
            ], 404);
        }

        // Generate verification code
        $code = $student->generateVerificationCode();

        // Here you would typically send the code via SMS
        // For now, we'll return it in the response (remove this in production)
        return response()->json([
            'success' => true,
            'message' => 'Verification code generated successfully',
            'code' => $code, // Remove this line in production
            'expires_at' => $student->verification_code_expires_at
        ]);
    }

    /**
     * Verify code and login student
     */
    public function verifyCodeAndLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'national_id' => 'required|string',
            'phone' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find student by both national_id AND phone
        $student = Student::where('national_id', $request->national_id)
            ->where('phone', $request->phone)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        // Verify the code
        if (!$student->verifyCode($request->code)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code'
            ], 400);
        }

        // Clear the verification code
        $student->clearVerificationCode();

        // Create token for the student (using Sanctum)
        $token = $student->createToken('StudentToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Code verified and logged in successfully',
            'student' => $student,
            'token' => $token
        ]);
    }

    /**
     * Change password (requires authentication)
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $student = $request->user();

        if (!Hash::check($request->current_password, $student->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        $student->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Reset password after verification (doesn't require current password)
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $student = $request->user();

        $student->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully'
        ]);
    }

    /**
     * Logout student
     */
    public function logout(Request $request)
    {
        // Delete current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
