<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::query();
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        return view('admin.contacts.index', ['contacts' => $query->latest()->paginate(15)->withQueryString()]);
    }

    public function update(Request $request, Contact $contact)
    {
        $data = $request->validate(['status' => 'required|in:baru,dibaca']);
        $contact->update($data);
        return redirect()->route('admin.contacts.index')->with('success', 'Status pesan diperbarui.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('admin.contacts.index')->with('success', 'Pesan dihapus.');
    }
}