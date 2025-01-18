<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AuthController extends Controller
{
	use AuthorizesRequests;

	public function showLogin()
	{
		return view('auth.login');
	}

	public function login(Request $request)
	{
		$validated = $request->validate([
			'email' => 'required|email',
			'password' => 'required|string',
		]);

		if (Auth::attempt($validated)) {
			$request->session()->regenerate();

			return redirect()->route('show.account');
		}

		throw ValidationException::withMessages([
			'credentials' => 'Email or Password is incorrect',
		]);
	}

	public function showCreateUser()
	{
		$authUser = Auth::user();
		$this->authorize('createUser', $authUser);
		return view('auth.createUser');
	}

	public function createUser(Request $request)
	{
		$authUser = Auth::user();
		$this->authorize('createUser', $authUser);

		$validated = $request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|email|unique:users',
			'role' => 'required|in:admin,editor,author',
			'password' => 'required|string|min:8|confirmed',
		]);
		$user = User::create($validated);

		return redirect()->route('show.showUsers')
				->with('success', 'User created successfully!');
	}

	public function showUsers()
	{
		$authUser = Auth::user();
		$this->authorize('createUser', $authUser);

		$users = User::all();
		return view('auth.showUser', compact('users'));		
	}

	public function logout(Request $request)
	{
		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();

		return redirect()->route('show.login');
	}
}
