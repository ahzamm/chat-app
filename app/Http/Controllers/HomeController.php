<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function getUserDetails()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return response()->json($user);
        }

        return response()->json(['error' => 'User not authenticated'], 401);
    }

    public function addContact(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email', // Ensures the email exists in the users table
        ]);

        $contact = User::where('email', $request->email)->first();

        // Ensure the contact is not the same as the current user and is not already in the contact list
        if ($contact->id === Auth::id()) {
            return response()->json(['message' => 'You cannot add yourself as a contact.'], 400);
        }

        // Check if the contact already exists
        $exists = DB::table('contacts')
            ->where('user_id', Auth::id())
            ->where('contact_id', $contact->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Contact already exists.'], 400);
        }

        // Add the contact relationship
        DB::table('contacts')->insert([
            'user_id' => Auth::id(),
            'contact_id' => $contact->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Contact added successfully!',
            'contact' => [
                'id' => $contact->id,
                'name' => $contact->name,
                'profile_pic' => $contact->profile_pic ? asset('storage/' . $contact->profile_pic) : 'https://via.placeholder.com/40',
            ],
        ]);
    }
}
