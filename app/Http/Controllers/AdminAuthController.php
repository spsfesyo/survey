<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminAuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function showLogin()
    {
        return view('Admin.auth-admin');
    }

    public function showDashboard()
    {
        return view('Admin.admin-dashboard');
    }

    public function login(Request $request)
    {
        // $request->validate([
        //     'username' => 'required',
        //     'password' => 'required',
        // ]);

        // $user = User::where('username', $request->username)->first();

        // if ($user && Hash::check($request->password, $user->password)) {
        //     Auth::guard('survey')->login($user);

        //     return view ('Admin.admin-dashboard'); // atau route khusus berdasarkan role
        // }

        // return back()->withErrors(['login' => 'Username atau password salah.']);

        // $request->validate([
        //     'username' => 'required',
        //     'password' => 'required',
        // ]);

        // $credentials = $request->only('username', 'password');

        // // Login menggunakan guard default (web)
        // if (Auth::attempt($credentials)) {
        //     $request->session()->regenerate(); // regenerasi session untuk keamanan

        //     $user = Auth::user(); // gunakan Auth::user() karena sudah pakai guard default

        //     // Redirect berdasarkan role_id
        //     switch ($user->role_id) {
        //         case 1:
        //             return redirect()->route('admin-dashboard');
        //         default:
        //             Auth::logout();
        //             return redirect()->route('auth-admin-survey')
        //                 ->withErrors(['login' => 'Role tidak dikenali.']);
        //     }
        // }

        // return back()->withErrors(['login' => 'Username atau password salah.']);

         $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('username', 'password'))) {
            $request->session()->regenerate();
            return redirect()->route('admin-dashboard'); // Redirect ke dashboard admin
        }

        return back()->withErrors(['login' => 'Username atau password salah.']);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function logout(Request $request)
    {
       Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // public function dashboard()
    // {
    //     return view('dashboard');
    // }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
