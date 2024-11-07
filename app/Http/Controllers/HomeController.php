<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Contact;

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
            'email' => 'required|email|exists:users,email',
        ]);

        $contact = User::where('email', $request->email)->first();

        if ($contact->id === Auth::id()) {
            return response()->json(['message' => 'You cannot add yourself as a contact.'], 400);
        }

        $exists = Contact::where('user_id', Auth::id())
            ->where('contact_id', $contact->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Contact already exists.'], 400);
        }

        Contact::create([
            'user_id'    => Auth::id(),
            'contact_id' => $contact->id,
        ]);

        return response()->json([
            'message' => 'Contact added successfully!',
            'contact' => [
                'id'          => $contact->id,
                'name'        => $contact->name,
                'profile_pic' => $contact->profile_pic ? asset('storage/' . $contact->profile_pic) : 'https://via.placeholder.com/40',
            ],
        ]);
    }

    public function getContacts()
    {
        $user     = Auth::user();
        $contacts = $user->contactUsers()->select('users.id as user_id', 'users.name', 'users.profile_pic')->orderBy('contacts.created_at', 'desc')->get();

        $formattedContacts = $contacts->map(function ($contact) {
            return [
                'id'          => $contact->id,
                'name'        => $contact->name,
                'profile_pic' => $contact->profile_pic ? asset('storage/' . $contact->profile_pic) : 'https://via.placeholder.com/40',
            ];
        });

        return response()->json($formattedContacts);
    }

}
