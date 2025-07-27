<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;

class userManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
            })
            ->get(); 
    
        $roles = ['admin', 'karyawan', 'tamu'];
    
        return view('laravel-examples.user-management', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|string|in:admin,karyawan,tamu',
        ]);

        try {
            User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ]);

            return redirect()->route('user-management.index')->with('user_store_success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('user_store_fail', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string',
        ]);

        try {
            $user->update(['role' => $request->role]);
            return redirect()->back()->with('user_update_role_success', 'Role pengguna berhasil diubah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('user_update_role_fail', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $deleted = User::destroy($id);

            if ($deleted) {
                return redirect()->back()->with('user_delete_success', 'Data berhasil dihapus.');
            } else {
                return redirect()->back()->with('user_delete_fail', 'Gagal menghapus data.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('user_delete_fail', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}