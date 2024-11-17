<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Contact;
use App\Models\Message;
use App\Models\Group;
use App\Models\GroupMember;
use App\Events\MessageSent;
use App\Events\GroupCreate;
use App\Models\Notification;

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
                'id'          => $contact->user_id,
                'name'        => $contact->name,
                'profile_pic' => $contact->profile_pic ? asset('storage/' . $contact->profile_pic) : 'https://via.placeholder.com/40',
            ];
        });

        return response()->json($formattedContacts);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message'     => 'required|string',
        ]);

        $message = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
        ]);

        broadcast(new MessageSent($message));

        return response()->json(['message' => 'Message sent successfully!', 'data' => $message]);
    }

    public function getMessages(Request $request)
    {
        $request->validate([
            'contact_id' => 'required|exists:users,id',
        ]);

        $userId = Auth::id();
        $contactId = $request->contact_id;

        $messages = Message::where(function ($query) use ($userId, $contactId) {
            $query->where('sender_id', $userId)->where('receiver_id', $contactId);
        })
        ->orWhere(function ($query) use ($userId, $contactId) {
            $query->where('sender_id', $contactId)->where('receiver_id', $userId);
        })
        ->orderBy('created_at', 'asc')
        ->get();

        return response()->json($messages);
    }

    public function createGroup(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'members'   => 'required|array',
            'members.*' => 'exists:users,id',
        ]);

        $group = Group::create([
            'name'       => $request->name,
            'created_by' => Auth::id(),
        ]);

        GroupMember::create([
            'group_id' => $group->id,
            'user_id'  => Auth::id(),
        ]);

        foreach ($request->members as $memberId) {
            GroupMember::create([
                'group_id' => $group->id,
                'user_id'  => $memberId,
            ]);

            Notification::create([
                'user_id' => $memberId,
                'text' => Auth::id() . ' Added you in group ' . $group->name
            ]);

            broadcast(new GroupCreate(Group::with('members')->find($group->id)));
        }

        return response()->json(['message' => 'Group created successfully!']);
    }

    public function getContactsAndGroups()
    {
        $user = Auth::user();

        $contacts = $user->contactUsers()
            ->select('users.id as user_id', 'users.name', 'users.profile_pic')
            ->orderBy('contacts.created_at', 'desc')
            ->get()
            ->map(function ($contact) {
                return [
                    'id'          => $contact->user_id,
                    'name'        => $contact->name,
                    'profile_pic' => $contact->profile_pic ? asset('storage/' . $contact->profile_pic) : 'https://via.placeholder.com/40',
                ];
            });

        $groups = GroupMember::where('user_id', $user->id)
            ->with('group')
            ->get()
            ->map(function ($groupMember) {
                return [
                    'id'   => $groupMember->group->id,
                    'name' => $groupMember->group->name,
                ];
            });

        return response()->json([
            'contacts' => $contacts,
            'groups' => $groups,
        ]);
    }

    public function getGroupMessages(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
        ]);

        $messages = Message::where('group_id', $request->group_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }
}
