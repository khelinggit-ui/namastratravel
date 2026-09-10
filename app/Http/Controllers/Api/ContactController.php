<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:4000',
        ]);

        $contact = Contact::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'message' => $data['message'],
            'status' => 'baru',
        ]);

        return response()->json(['message' => 'Pesan Anda telah terkirim.', 'contact' => $contact], 201);
    }

    // ---- Admin ----

    public function index(Request $request)
    {
        $query = Contact::query();
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        return response()->json($query->orderByDesc('created_at')->paginate($request->input('per_page', 20)));
    }

    public function update(Request $request, Contact $contact)
    {
        $data = $request->validate(['status' => 'required|in:baru,dibaca']);
        $contact->update($data);
        return response()->json($contact);
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return response()->json(['message' => 'Pesan dihapus.'], 200);
    }
}