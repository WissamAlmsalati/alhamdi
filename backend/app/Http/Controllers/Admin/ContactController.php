<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter');
        $search = trim((string) $request->query('q'));

        $contacts = Contact::query()
            ->when($filter === 'unread', fn ($query) => $query->where('is_read', false))
            ->when($filter === 'read', fn ($query) => $query->where('is_read', true))
            ->when($search, fn ($query) => $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'all' => Contact::count(),
            'unread' => Contact::where('is_read', false)->count(),
            'read' => Contact::where('is_read', true)->count(),
        ];

        return view('admin.contacts.index', compact('contacts', 'counts', 'filter', 'search'));
    }

    public function show(Contact $contact)
    {
        if (! $contact->is_read) {
            $contact->update(['is_read' => true]);
        }

        return view('admin.contacts.show', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $contact->update(['is_read' => $request->boolean('is_read')]);

        // Opening a message marks it read, so an "unread" action returns to the list instead of the message.
        if (! $contact->is_read) {
            return redirect()->route('admin.contacts.index')->with('success', __('تم تعيين الرسالة كغير مقروءة'));
        }

        return redirect()->back()->with('success', __('تم تعيين الرسالة كمقروءة'));
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('admin.contacts.index')->with('success', __('تم حذف الرسالة'));
    }
}
