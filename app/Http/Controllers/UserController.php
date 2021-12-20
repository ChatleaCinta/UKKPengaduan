<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function index()
    {
        try {
            $user = User::paginate(10);
            return response()->json([
                'status' => 'success',
                'data'=> $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message'=> 'server error'
            ], 500);
        }
    }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'telepon' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'username' => $request->get('username'),
            'telepon' => $request->get('telepon'),
            'nik' => $request->get('nik'),
            'email' => $request->get('email'),
            'jenis_kelamin' => $request->get('jenis_kelamin'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }
    public function changeStatus(Request $request,$id){
        try {
            $user = User::findOrFail($id);
            if($request->input('role')){
                if($request->get('user')->role !== 'admin'){
                    return response()->json([
                        'status' => 'failed',
                        'message'=> 'Hanya Admin Yang Dapat Mengaksesnya'
                    ], 400); 
                }
                $user->role = $request->get('role');
            }
            if($request->input('status')){
                $user->status = $request->get('status');            
            }
            if($user->save()){
                return response()->json([
                    'status' => 'success',
                    'message'=> 'Berhasil mengedit'
                ], 200); 
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message'=> 'server error'
            ], 505); 
        }

    }
    public function registerByAdmin(Request $request)
    {
        if($request->get('user')->role !== 'admin'){
            return response()->json([
                'status' => 'failed',
                'message'=> 'Hanya Admin Yang Dapat Mengaksesnya'
            ], 400); 
        }
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'telepon' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:user,staff,admin',
            'status' => 'required|in:verified,unverified',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'username' => $request->get('username'),
            'telepon' => $request->get('telepon'),
            'nik' => $request->get('nik'),
            'email' => $request->get('email'),
            'role' => $request->get('role'),
            'status' => $request->get('status'),
            'password' => Hash::make($request->get('password')),
        ]);

        // $token = JWTAuth::fromUser($user);
        return response()->json([
            'status' => 'success',
            'message'=> 'user berhasil di tambahkan'
        ], 200);
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('user'));
    }

    public function logout(Request $request)
    {

        if(JWTAuth::invalidate(JWTAuth::getToken())) {
            return response()->json([
                "logged"    => false,
                "message"   => 'Logout berhasil'
            ], 201);
        } else {
            return response()->json([
                "logged"    => true,
                "message"   => 'Logout gagal'
            ], 201);
        }

    }
    public function delete($id)
    {
        try {
            User::findOrFail($id)->delete();
            return response([
            	"status"	=> 'success',
                "message"   => "User berhasil di hapus."
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message'=> 'server error'
            ], 500);
        }
    }
}