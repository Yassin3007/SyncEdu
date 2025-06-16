<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\GuardianResource;
use App\Http\Resources\StudentResource;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class GuardianAuthController extends Controller
{
    /**
     * Login student by phone/password or national_id/password
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|unique:guardians,phone',
            'student_id' => 'required|exists:students,national_id',
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
        }while(Guardian::where('national_id',$random_id)->exists());

        // Create the student
        $guardian = Guardian::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'national_id' => $random_id,
            'student_id' => Student::where('national_id',$request->student_id)->first()->id ,
            'password' => Hash::make($request->password),
            'image' => $request->file('image'),
        ]);

        // Create token for auto-login
        $token = $guardian->createToken('GuardianToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Guardian registered and logged in successfully',
            'guardian' => new GuardianResource($guardian),
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
        $guardian = Guardian::where('phone', $identifier)
            ->orWhere('national_id', $identifier)
            ->first();

        if (!$guardian || !Hash::check($password, $guardian->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Create token for the student (using Sanctum)
        $token = $guardian->createToken('GuardianToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'guardian' => new  GuardianResource($guardian),
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
        $guardian = Guardian::where('national_id', $request->national_id)
            ->where('phone', $request->phone)
            ->first();

        if (!$guardian) {
            return response()->json([
                'success' => false,
                'message' => 'No Guardian found with these credentials'
            ], 404);
        }

        // Generate verification code
        $code = $guardian->generateVerificationCode();

        // Here you would typically send the code via SMS
        // For now, we'll return it in the response (remove this in production)
        return response()->json([
            'success' => true,
            'message' => 'Verification code generated successfully',
            'code' => $code, // Remove this line in production
//            'expires_at' => $guardian->verification_code_expires_at
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
        $guardian = Guardian::where('national_id', $request->national_id)
            ->where('phone', $request->phone)
            ->first();

        if (!$guardian) {
            return response()->json([
                'success' => false,
                'message' => 'Guardian not found'
            ], 404);
        }

        // Verify the code
        if (!$guardian->verifyCode($request->code)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code'
            ], 400);
        }

        // Clear the verification code
        $guardian->clearVerificationCode();

        // Create token for the student (using Sanctum)
        $token = $guardian->createToken('GuardianToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Code verified and logged in successfully',
            'guardian' => $guardian,
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

        $guardian = $request->user();

        $guardian->update([
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
