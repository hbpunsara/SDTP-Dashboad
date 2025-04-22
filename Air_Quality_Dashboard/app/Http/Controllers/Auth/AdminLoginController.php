<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminLoginController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }
    
    /**
     * Handle a login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|email',
            'password' => 'required',
        ]);
        
        // Find user by email (username field is actually email)
        $user = \App\Models\User::where('email', $request->username)->first();
        
        // Check if user exists and is an admin with correct password
        if ($user && $user->is_admin && Hash::check($request->password, $user->password)) {
            // Store admin status and ID in session
            session([
                'is_admin' => true,
                'admin_id' => $user->id,
                'admin_name' => $user->name
            ]);
            
            return redirect()->intended('/admin/dashboard');
        }
        
        // Fallback for legacy admin login (can be removed later)
        if ($request->username === 'admin' && $request->password === 'admin123') {
            // Store admin status in session
            session([
                'is_admin' => true,
                'admin_name' => 'Admin User'
            ]);
            
            return redirect()->intended('/admin/dashboard');
        }
        
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records or you do not have administrator privileges.',
        ])->withInput($request->only('username'));
    }
    
    /**
     * Log the user out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Clear admin session data
        session()->forget(['is_admin', 'admin_name', 'admin_id']);
        
        return redirect('/');
    }
}
