<?php

namespace App\Http\Controllers;

use App\Models\Tanggapan;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class TanggapanController extends Controller
{
    public function index(Type $var = null)
    {
        try {
            $tanggapan = Tanggapan::with('user')->paginate(10);
            return response()->json([
                'status' => 'success',
                'data'=> $tanggapan
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message'=> 'server error'
            ], 500);
        }
    }
    public function store(Request $request,$id_pengaduan)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tanggapan' => 'required',
            ]);
    
            if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }
            $peng = Pengaduan::findOrFail($id_pengaduan);
            $peng->status='ditanggapi';
            $peng->save();
            $tanggapan = tanggapan::create([
                'id_pengaduan' => $id_pengaduan,
                'nik'          => $request->get('user')->nik,
                'tanggapan'    => $request->get('tanggapan')
            ]);
            if($tanggapan->save()){
                return response()->json([
                    'status' => 'success',
                    'message'=> 'berhasil ditambahkan'
                ], 200);
            }else{
                return response()->json([
                    'status' => 'failed',
                    'message'=> 'tidak berhasil ditambahkan'
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message'=> 'server error'
            ], 500);
        }
    }
    public function edit(Request $request,$id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tanggapan' => 'required',
            ]);
    
            if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }
            $tanggapan = Tanggapan::findOrFail($id);
            if($request->get('user')->nik !== $tanggapan->nik){
                return response()->json([
                    'status' => 'failed',
                    'message'=> 'Anda bukan pembuat tanggapan'
                ], 400); 
            }
            $tanggapan = Tanggapan::findOrFail($id);
            $tanggapan->tanggapan = $request->get('tanggapan');

            if($tanggapan->save()){
                return response()->json([
                    'status' => 'success',
                    'message'=> 'tanggapan berhasil di edit'
                ], 200);
            }else{
                return response()->json([
                    'status' => 'failed',
                    'message'=> 'tanggapan tidak berhasil di edit'
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message'=> 'server error'
            ], 500);
        }
    }
    public function delete($id)
    {
        try {
            Tanggapan::findOrFail($id)->delete();

            return response([
            	"status"	=> 'success',
                "message"   => "Tanggapan berhasil di hapus."
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message'=> 'server error'
            ], 500);
        }
    }
}
