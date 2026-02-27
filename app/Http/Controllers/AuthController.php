<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\TempCaptivePortalDesign;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Mail\PasswordResetMail;
use App\Mail\VerifyEmailMail;
use Carbon\Carbon;
use Log;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Remove the middleware line from here
    }

    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register_with_design(Request $request)
    {
        Log::info('Register request received', ['request' => $request->all()]);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'design_id' => 'required|exists:temp_captive_portal_designs,id',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()]);
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        // Transfer temporary design to user if design_id is provided
        if ($request->has('design_id') && $request->design_id) {
            try {
                $tempDesign = TempCaptivePortalDesign::find($request->design_id);
                if ($tempDesign) {
                    $delete_status = $tempDesign->transferToUser($user->id);
                    if($delete_status) {
                        Log::info('Temporary design transferred to user', [
                            'user_id' => $user->id,
                            'design_id' => $request->design_id
                        ]);
                    } else {
                        Log::error('Failed to transfer temporary design', [
                            'user_id' => $user->id,
                            'design_id' => $request->design_id,
                            'error' => 'Failed to transfer temporary design'
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to transfer temporary design', [
                    'user_id' => $user->id,
                    'design_id' => $request->design_id,
                    'error' => $e->getMessage()
                ]);
                // Continue with registration even if design transfer fails
            }
        }

        // Send verification email with has_design=1 so verify-email redirects to captive-portals
        $this->sendVerificationEmail($user, true);

        return response()->json([
            'message' => 'Registration successful! Please check your email to verify your account.',
            'requires_verification' => true,
            'email' => $user->email
        ], 201);
    }

    public function register(Request $request)
    {
        Log::info('Register request received', ['request' => $request->all()]);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'design_id' => 'nullable|exists:temp_captive_portal_designs,id',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()]);
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        // Transfer temporary design to user if design_id is provided
        if ($request->has('design_id') && $request->design_id) {
            try {
                $tempDesign = TempCaptivePortalDesign::find($request->design_id);
                if ($tempDesign) {
                    $tempDesign->transferToUser($user->id);
                    Log::info('Temporary design transferred to user', [
                        'user_id' => $user->id,
                        'design_id' => $request->design_id
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to transfer temporary design', [
                    'user_id' => $user->id,
                    'design_id' => $request->design_id,
                    'error' => $e->getMessage()
                ]);
                // Continue with registration even if design transfer fails
            }
        }

        // Send verification email with design flag only if a design was actually transferred
        $hasDesign = $request->has('design_id') && $request->design_id;
        $this->sendVerificationEmail($user, $hasDesign);

        return response()->json([
            'message' => 'Registration successful! Please check your email to verify your account.',
            'requires_verification' => true,
            'email' => $user->email
        ], 201);
    }

    public function update(Request $request)
    {
        $user = Auth::guard('api')->user();
        Log::info($user);
        Log::info($request->all());
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
            Log::info($user->name);
        }

        if ($request->has('email')) {
            $user->email = $request->email;
            Log::info($user->email);
        }

        if ($request->has('role') && $user->role == 'admin') {
            $user->role = $request->role;
            Log::info($user->role);
        }

        if ($request->has('password') && $request->password !== '' && $request->password !== null) {
            if ($request->password !== $request->confirm_password) {
                return response()->json(['error' => 'Passwords do not match'], 400);
            }
            $user->password = Hash::make($request->password);
            Log::info($user->password);
        }

        $user->save();
        return response()->json(['message' => 'User updated successfully'], 200);
    }

    public function uploadProfilePicture(Request $request)
    {
        // If user_id is provided, admin is uploading for another user
        if ($request->has('user_id')) {
            $targetUser = User::find($request->user_id);
            if (!$targetUser) {
                return response()->json(['error' => 'Target user not found'], 404);
            }
            $user = $targetUser;
        } else {
            // Regular user uploading their own profile picture
            $user = Auth::guard('api')->user();
            if(!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            if($extension !== 'jpg' && $extension !== 'jpeg' && $extension !== 'png' && $extension !== 'JPG' && $extension !== 'JPEG' && $extension !== 'PNG') {
                return response()->json(['error' => 'Invalid file type'], 400);
            }
            if($file->getSize() > 2 * 1024 * 1024) {
                return response()->json(['error' => 'File size must be less than 2MB'], 400);
            }
            $filename = time() . '.' . $extension;
            $file->move(public_path('uploads/profile_pictures'), $filename);
            $user->profile_picture = $filename;
            $user->save();
            
            return response()->json(['message' => 'Profile picture uploaded successfully', 'filename' => $filename], 200);
        }
        
        return response()->json(['error' => 'No file provided'], 400);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        Log::info($request->all());
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Get the user by email
        $user = User::where('email', $request->email)->first();
        Log::info($user);

        // If user doesn't exist or password doesn't match
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Check if email is verified
        if (!$user->email_verified_at) {
            return response()->json([
                'error' => 'Please verify your email address before logging in.',
                'requires_verification' => true,
                'email' => $user->email
            ], 403);
        }

        // Generate token
        $token = Auth::guard('api')->login($user);

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::guard('api')->refresh());
    }

    public function getUsers()
    {
        $user = Auth::guard('api')->user();
        if(!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $users = User::all();
        $users = $users->map(function($user) {
            return $user->only(['id', 'name', 'email', 'role', 'profile_picture']);
        });
        return response()->json([
            'users' => $users,
            'total' => $users->count(),
            'status' => 'success'
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = Auth::guard('api')->user();
        if(!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $targetUser = User::find($id);
        if(!$targetUser) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        // Only superadmin can edit superadmin accounts
        if($targetUser->role === 'superadmin' && $user->role !== 'superadmin') {
            return response()->json(['error' => 'Only superadmin can edit superadmin accounts'], 403);
        }
        
        // Update the target user's fields
        if($request->has('name')) {
            $targetUser->name = $request->name;
        }
        if($request->has('email')) {
            $targetUser->email = $request->email;
        }
        if($request->has('role') && ($user->role == 'admin' || $user->role == 'superadmin')) {
            // Only superadmin can assign superadmin role
            if($request->role === 'superadmin' && $user->role !== 'superadmin') {
                return response()->json(['error' => 'Only superadmin can assign superadmin role'], 403);
            }
            $targetUser->role = $request->role;
        }
        if($request->has('password') && $request->password !== '' && $request->password !== null) {
            // Only superadmin can change superadmin passwords
            if($targetUser->role === 'superadmin' && $user->role !== 'superadmin') {
                return response()->json(['error' => 'Only superadmin can change superadmin passwords'], 403);
            }
            if($request->password !== $request->confirm_password) {
                return response()->json(['error' => 'Passwords do not match'], 400);
            }
            $targetUser->password = Hash::make($request->password);
        }
        
        $targetUser->save();
        return response()->json(['message' => 'User updated successfully'], 200);
    }

    public function createUser(Request $request)
    {
        $user = Auth::guard('api')->user();
        if(!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate role based on current user permissions
        $allowedRoles = ['user', 'admin'];
        if ($user->role === 'superadmin') {
            $allowedRoles[] = 'superadmin';
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:' . implode(',', $allowedRoles),
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Additional check: only superadmin can create superadmin accounts
        if ($request->role === 'superadmin' && $user->role !== 'superadmin') {
            return response()->json(['error' => 'Only superadmin can create superadmin accounts'], 403);
        }

        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified_at' => now(), // Auto-verify admin-created accounts
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $newUser->only(['id', 'name', 'email', 'role'])
        ], 201);
    }

    public function deleteUser(Request $request, $id)
    
    {
        $currentUser = Auth::guard('api')->user();
        if(!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $targetUser = User::find($id);
        if(!$targetUser) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        // Only superadmin can delete superadmin accounts
        if($targetUser->role === 'superadmin' && $currentUser->role !== 'superadmin') {
            return response()->json(['error' => 'Only superadmin can delete superadmin accounts'], 403);
        }
        
        // Prevent users from deleting themselves
        if($targetUser->id === $currentUser->id) {
            return response()->json(['error' => 'You cannot delete your own account'], 400);
        }
        
        $targetUser->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    /**
     * Send a password reset link to the user's email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendPasswordResetLink(Request $request)
    {
        Log::info('=== Password Reset Request Started ===');
        Log::info('Request Email: ' . $request->email);
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            Log::warning('Password reset validation failed', ['errors' => $validator->errors()]);
            return response()->json($validator->errors(), 422);
        }

        // Check if user exists
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            Log::warning('Password reset requested for non-existent email: ' . $request->email);
            // For security, we still return success even if user doesn't exist
            // This prevents email enumeration attacks
            return response()->json([
                'message' => 'If an account exists with this email, a password reset link has been sent.'
            ], 200);
        }

        Log::info('User found for password reset', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email
        ]);

        // Delete any existing reset tokens for this user
        $deletedCount = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();
        
        if ($deletedCount > 0) {
            Log::info("Deleted {$deletedCount} existing password reset token(s) for: " . $request->email);
        }

        // Generate a new token
        $token = Str::random(64);
        Log::info('Generated new password reset token', [
            'token_length' => strlen($token),
            'token_preview' => substr($token, 0, 10) . '...'
        ]);

        // Store the token in the database
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);
        Log::info('Password reset token stored in database for: ' . $request->email);

        // Create the reset URL
        $resetUrl = url('/reset-password?token=' . $token . '&email=' . urlencode($request->email));
        Log::info('Generated reset URL', ['url_length' => strlen($resetUrl)]);

        // Check mail configuration
        Log::info('Mail Configuration', [
            'mailer' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name')
        ]);

        // Send the email
        try {
            Log::info('Attempting to send password reset email to: ' . $user->email);
            
            Mail::to($user->email)->send(new PasswordResetMail($resetUrl, $user->name, 60));
            
            Log::info('✅ Password reset email sent successfully!', [
                'recipient' => $user->email,
                'recipient_name' => $user->name,
                'reset_url' => $resetUrl
            ]);
            
            return response()->json([
                'message' => 'If an account exists with this email, a password reset link has been sent.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('❌ Failed to send password reset email', [
                'recipient' => $user->email,
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to send password reset email. Please try again later.'
            ], 500);
        } finally {
            Log::info('=== Password Reset Request Completed ===');
        }
    }

    /**
     * Reset the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        Log::info('=== Password Reset Submission Started ===');
        Log::info('Reset Request', [
            'email' => $request->email,
            'has_token' => !empty($request->token),
            'token_length' => strlen($request->token ?? ''),
            'has_password' => !empty($request->password)
        ]);
        
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            Log::warning('Password reset validation failed', ['errors' => $validator->errors()]);
            return response()->json($validator->errors(), 422);
        }

        // Find the reset token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            Log::warning('No password reset token found for email: ' . $request->email);
            return response()->json([
                'error' => 'Invalid or expired reset token.'
            ], 400);
        }

        Log::info('Password reset token found in database', [
            'email' => $request->email,
            'token_created_at' => $resetRecord->created_at
        ]);

        // Check if token is valid (not expired - 60 minutes)
        $tokenCreatedAt = \Carbon\Carbon::parse($resetRecord->created_at);
        $expiresAt = $tokenCreatedAt->copy()->addMinutes(60);
        $isExpired = $expiresAt->isPast();
        
        Log::info('Token expiration check', [
            'created_at' => $tokenCreatedAt->toDateTimeString(),
            'expires_at' => $expiresAt->toDateTimeString(),
            'current_time' => now()->toDateTimeString(),
            'is_expired' => $isExpired,
            'minutes_since_creation' => $tokenCreatedAt->diffInMinutes(now())
        ]);
        
        if ($isExpired) {
            Log::warning('Password reset token has expired for: ' . $request->email);
            // Delete expired token
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();
            Log::info('Expired token deleted from database');
                
            return response()->json([
                'error' => 'Reset token has expired. Please request a new one.'
            ], 400);
        }

        // Verify the token matches
        $tokenMatches = Hash::check($request->token, $resetRecord->token);
        Log::info('Token verification', [
            'token_matches' => $tokenMatches,
            'provided_token_preview' => substr($request->token, 0, 10) . '...'
        ]);
        
        if (!$tokenMatches) {
            Log::warning('Password reset token does not match for: ' . $request->email);
            return response()->json([
                'error' => 'Invalid reset token.'
            ], 400);
        }

        // Find the user
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            Log::error('User not found for password reset: ' . $request->email);
            return response()->json([
                'error' => 'User not found.'
            ], 404);
        }

        Log::info('User found, updating password', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name
        ]);

        // Update the password
        $user->password = Hash::make($request->password);
        // if email is not verified, verify it set email_verified_at to now
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
        }
        $user->save();
        Log::info('Password updated in database for user: ' . $user->email);

        // Delete the used token
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();
        Log::info('Used password reset token deleted from database');

        Log::info('✅ Password successfully reset for user: ' . $user->email);
        Log::info('=== Password Reset Submission Completed Successfully ===');

        return response()->json([
            'message' => 'Password has been reset successfully. You can now login with your new password.'
        ], 200);
    }
    
    /**
     * Send verification email to user.
     *
     * @param  User  $user
     * @return void
     */
    protected function sendVerificationEmail(User $user, bool $hasDesign = false)
    {
        // Delete any existing tokens
        DB::table('email_verification_tokens')
            ->where('user_id', $user->id)
            ->delete();

        // Generate new token
        $token = Str::random(64);
        $expiresAt = Carbon::now()->addMinutes(60);

        // Store token
        DB::table('email_verification_tokens')->insert([
            'user_id' => $user->id,
            'token' => Hash::make($token),
            'expires_at' => $expiresAt,
            'created_at' => now()
        ]);

        // Create verification URL
        $verificationUrl = url('/verify-email?token=' . $token . '&email=' . urlencode($user->email));
        if ($hasDesign) {
            $verificationUrl .= '&has_design=1';
        }

        // Send email
        try {
            Mail::to($user->email)->send(new VerifyEmailMail($verificationUrl, $user->name, 60));
            Log::info('Verification email sent to: ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Failed to send verification email', [
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Verify user email address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(Request $request)
    {
        Log::info('=== Email Verification Request ===');

        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find user
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        // Check if already verified
        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email already verified. You can now login.',
                'already_verified' => true
            ], 200);
        }

        // Find verification token
        $tokenRecord = DB::table('email_verification_tokens')
            ->where('user_id', $user->id)
            ->first();

        if (!$tokenRecord) {
            return response()->json([
                'error' => 'Invalid or expired verification link. Please request a new one.'
            ], 400);
        }

        // Check expiration
        if (Carbon::parse($tokenRecord->expires_at)->isPast()) {
            DB::table('email_verification_tokens')
                ->where('user_id', $user->id)
                ->delete();

            return response()->json([
                'error' => 'Verification link has expired. Please request a new one.',
                'expired' => true
            ], 400);
        }

        // Verify token
        if (!Hash::check($request->token, $tokenRecord->token)) {
            return response()->json([
                'error' => 'Invalid verification link.'
            ], 400);
        }

        // Mark email as verified
        $user->email_verified_at = now();
        $user->save();

        // Delete used token
        DB::table('email_verification_tokens')
            ->where('user_id', $user->id)
            ->delete();

        Log::info('Email verified for user: ' . $user->email);

        // Auto login after verification
        $token = Auth::guard('api')->login($user);

        return response()->json([
            'message' => 'Email verified successfully!',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'user' => $user
        ], 200);
    }

    /**
     * Resend verification email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerificationEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // For security, don't reveal if user exists
            return response()->json([
                'message' => 'If an account exists with this email, a verification link has been sent.'
            ], 200);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email is already verified. You can login.',
                'already_verified' => true
            ], 200);
        }

        // Check rate limiting (max 1 email per 2 minutes)
        $lastToken = DB::table('email_verification_tokens')
            ->where('user_id', $user->id)
            ->first();

        if ($lastToken && Carbon::parse($lastToken->created_at)->addMinutes(2)->isFuture()) {
            $waitSeconds = Carbon::parse($lastToken->created_at)->addMinutes(2)->diffInSeconds(now());
            return response()->json([
                'error' => 'Please wait before requesting another verification email.',
                'wait_seconds' => $waitSeconds
            ], 429);
        }

        $this->sendVerificationEmail($user);

        return response()->json([
            'message' => 'Verification email sent! Please check your inbox.'
        ], 200);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'user' => Auth::guard('api')->user()
        ]);
    }

    protected function respondWithToken_and_design_url($token, $url)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'user' => Auth::guard('api')->user(),
            'url' => $url
        ]);
    }
}
