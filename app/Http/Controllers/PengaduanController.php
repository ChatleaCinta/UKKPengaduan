<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use JWTAuth;


class PengaduanController extends Controller
{
    public function index()
    {
        try {
            $pengaduan = Pengaduan::with('user')->paginate(10);
            return response()->json([
                'status' => 'success',
                'data'=> $pengaduan
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message'=> 'server error'
            ], 500);
        }
    }
    public function myIndex()
    {
        $user = JWTAuth::parseToken()->authenticate();
        try {
            $pengaduan = Pengaduan::with('user', 'tanggapan.user')->where('nik',$user->nik)->paginate(10);
            return response()->json([
                'status' => 'success',
                'data'=> $pengaduan
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message'=> 'server error'
            ], 500);
        }
    }
    
    public function show($id)
    {
        try {
            $pengaduan = Pengaduan::with('user','tanggapan.user')->find($id);
            return response()->json([
                'status' => 'success',
                'data'=> $pengaduan
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message'=> 'server error'
            ], 500);
        }
    }
    public function pdfview(Request $request)
    {
        $items = Pengaduan::with('tanggapan','user')->get();
        view()->share('items',$items);
        if($request->has('download')){
        $pdf = PDF::loadView('pdfview');
        return $pdf->download('pdfview.pdf');
        }
        return view('pdfview');
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'foto' => 'required|file',
                'laporan' => 'required',
            ]);
    
            if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }
            $photo = $request->file('foto');
            $tujuan_upload = 'image/pengaduan';
            $photo_name = Str::random(2).'_'.date("h-i-sa").'_'.date("Y-m-d").'_'.Str::random(3).'.'.$photo->getClientOriginalExtension();
            
            $pengaduan = Pengaduan::create([
                'laporan' => $request->get('laporan'),
                'foto' => $photo_name,
                'nik'  => $request->get('user')->nik
            ]);
            if($pengaduan->save()){
                $photo->move($tujuan_upload,$photo_name);
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
                'foto' => 'file|image',
                'laporan' => '',
            ]);
    
            if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }
            $pengaduan = Pengaduan::find($id);
            if($request->get('user')->nik !== $pengaduan->nik){
                return response()->json([
                    'status' => 'failed',
                    'message'=> 'Anda bukan pembuat laporan'
                ], 400); 
            }
            if($request->hasFile('foto')){
                $photo = $request->file('foto');
                $tujuan_upload = 'image/pengaduan';
                $photo_name = Str::random(2).'_'.date("h-i-sa").'_'.date("Y-m-d").'_'.Str::random(3).'.'.$photo->getClientOriginalExtension();
                $pengaduan->foto = $photo_name;
            }
            if($request->input('laporan')){
                $pengaduan->laporan = $request->get('laporan');
            }
            if($pengaduan->save()){
                if($request->hasFile('foto')){
                    $photo->move($tujuan_upload,$photo_name);
                }
                return response()->json([
                    'status' => 'success',
                    'message'=> 'berhasil di edit'
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
    public function delete($id)
    {
        try {
            Pengaduan::findOrFail($id)->delete();

            return response([
            	"status"	=> 'success',
                "message"   => "Aduan berhasil di hapus."
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message'=> 'server error'
            ], 500);
        }
    }
}
