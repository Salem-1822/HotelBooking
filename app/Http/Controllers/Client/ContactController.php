<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Show the contact form.
     */
    public function index()
    {
        return view('client.contact');
    }

    /**
     * Store a newly created contact message in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'string', 'email:rfc,filter', 'max:150'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        try {
            ContactMessage::create([
                'name'    => $validated['name'],
                'email'   => $validated['email'],
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'status'  => 'unread',
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to persist contact message: ' . $e->getMessage(), [
                'exception' => $e,
                'email'     => $validated['email'],
            ]);

            return back()
                ->withInput()
                ->with('error', __('contact.send_error'));
        }

        return back()->with('success', __('contact.send_success'));
    }
}
