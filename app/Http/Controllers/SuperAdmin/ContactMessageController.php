<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of the contact messages.
     */
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('super_admin.messages.index', compact('messages'));
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(ContactMessage $message)
    {
        if ($message->status === 'unread') {
            $message->update([
                'status' => 'read',
                'read_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified message from storage.
     */
    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()->route('super_admin.messages.index')
            ->with('success', 'Message deleted successfully.');
    }
}
