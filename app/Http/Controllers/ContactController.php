<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;



class ContactController extends Controller
{
    // Create a new contact message
    public function store(Request $request)
    {
        try{
        $request->validate([
            'sender' => 'required|integer|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $contact = Contact::create([
            'sender' => $request->input('sender'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone_number' => $request->input('phone_number'),
            'email' => $request->input('email'),
            'message' => $request->input('message'),
            'status' => 'new',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $contact,
        ], 201);

        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

