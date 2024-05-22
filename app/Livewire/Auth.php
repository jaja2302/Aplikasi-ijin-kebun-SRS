<?php

namespace App\Livewire;

use App\Models\Pengguna;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth as authenticate;

class Auth extends Component
{

    public $email;
    public $password;

    public function login()
    {
        $this->validate([
            'email' => 'required',
            'password' => 'required',
        ]);


        // Retrieve the user based on the email
        $pengguna = Pengguna::where('email', $this->email)->first();

        // dd($pengguna);
        // Check if the user exists and the password is correct
        if (!$pengguna || $this->password != $pengguna->password) {
            return back()->with('error', 'Email atau Password Salah');
        }

        // Store user details in session
        session([
            'user_id' => $pengguna->user_id,
            'user_name' => $pengguna->nama_lengkap,
            'departemen' => $pengguna->departemen,
            'lok' => $pengguna->lokasi_kerja,
            'jabatan' => $pengguna->jabatan,
        ]);

        // Login the user using their email
        auth()->login($pengguna);

        // Check if the user is authorized to access the 'rekap' route
        if (!auth()->check()) {
            // Redirect the user back with an error message if not authorized
            return back()->with('error', 'Unauthorized access');
        }

        // Redirect the user to the intended route after successful login
        return redirect()->intended(route('dashboard'));
        // return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth');
    }
}
