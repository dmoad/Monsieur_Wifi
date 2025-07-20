<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
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
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::guard('api')->login($user);
        
        return $this->respondWithToken($token);
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
            return $user->only(['id', 'name', 'email', 'profile_picture']);
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
        $user = User::find($id);
        if(!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user_info = $user->only(['id', 'name', 'email']);
        if($request->has('password') && $request->password !== '' && $request->password !== null) {
            if($request->password !== $request->confirm_password) {
                return response()->json(['error' => 'Passwords do not match'], 400);
            }
            $user_info['password'] = Hash::make($request->password);
        }
        if($request->has('name')) {
            $user_info['name'] = $request->name;
        }
        if($request->has('email')) {
            $user_info['email'] = $request->email;
        }
        $user->update($user_info);
        return response()->json(['message' => 'User updated successfully'], 200);
    }

    public function deleteUser(Request $request, $id)
    
    {
        $user = Auth::guard('api')->user();
        if(!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = User::find($id);
        if(!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
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
}
