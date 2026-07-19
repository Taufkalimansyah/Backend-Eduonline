<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** Dashboard Admin -> "Manajemen Pengguna (User CRUD)" */
class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', '!=', 'admin');

        if ($search = $request->query('q')) {
            $query->where(fn ($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%"));
        }

        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:dosen,mahasiswa',
            'nim' => 'nullable|string|max:50|unique:users,nim',
            'bidang' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            ...$data,
            'password' => Hash::make('welcome123'), // password default, wajib diganti saat login pertama
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'nim' => 'sometimes|string|max:50|unique:users,nim,' . $user->id,
            'role' => 'required|in:dosen,mahasiswa',
            'bidang' => 'nullable|string|max:255',
        ]);

        $user->update($data);

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User dihapus']);
    }
}