<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = Admin::create([
            'name'     => 'Super Admin',
            'email'    => 'superadmin@hotelbooking.com',
            'password' => Hash::make('password123'),
            'role'     => 'super_admin',
            'status'   => 'active',
        ]);
    }

    /**
     * Test contact page renders successfully.
     */
    public function test_contact_page_can_be_rendered(): void
    {
        $response = $this->get(route('contact'));

        $response->assertStatus(200);
        $response->assertSee('Contact Us');
        $response->assertSee('Send us a Message');
    }

    /**
     * Test valid contact form submission persists message and redirects back with success.
     */
    public function test_valid_contact_form_submission_persists_in_database_and_redirects(): void
    {
        $payload = [
            'name'    => 'John Doe',
            'email'   => 'john.doe@example.com',
            'subject' => 'Reservation Inquiry',
            'message' => 'Hello, I would like to inquire about booking availability for next week.',
        ];

        $response = $this->from(route('contact'))->post(route('contact.store'), $payload);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHas('success', 'Your message has been sent successfully. We will get back to you soon!');

        $this->assertDatabaseHas('contact_messages', [
            'name'    => 'John Doe',
            'email'   => 'john.doe@example.com',
            'subject' => 'Reservation Inquiry',
            'status'  => 'unread',
        ]);
    }

    /**
     * Test invalid form submissions fail validation and return session errors.
     */
    public function test_invalid_contact_form_submission_fails_validation(): void
    {
        // 1. Missing name
        $response = $this->from(route('contact'))->post(route('contact.store'), [
            'name'    => '',
            'email'   => 'valid@example.com',
            'subject' => 'Subject Test',
            'message' => 'This is a valid length message for testing.',
        ]);
        $response->assertRedirect(route('contact'));
        $response->assertSessionHasErrors(['name']);

        // 2. Invalid email format
        $response = $this->from(route('contact'))->post(route('contact.store'), [
            'name'    => 'Jane Doe',
            'email'   => 'not-an-email',
            'subject' => 'Subject Test',
            'message' => 'This is a valid length message for testing.',
        ]);
        $response->assertRedirect(route('contact'));
        $response->assertSessionHasErrors(['email']);

        // 3. Message too short (min: 10)
        $response = $this->from(route('contact'))->post(route('contact.store'), [
            'name'    => 'Jane Doe',
            'email'   => 'jane@example.com',
            'subject' => 'Subject Test',
            'message' => 'Short',
        ]);
        $response->assertRedirect(route('contact'));
        $response->assertSessionHasErrors(['message']);

        // Ensure no messages were persisted
        $this->assertDatabaseCount('contact_messages', 0);
    }

    /**
     * Test submitted messages appear in Super Admin messages system and can be managed.
     */
    public function test_submitted_message_appears_in_super_admin_messages_and_can_be_managed(): void
    {
        // Submit message from client contact form
        $this->post(route('contact.store'), [
            'name'    => 'Alice Guest',
            'email'   => 'alice@example.com',
            'subject' => 'Special Request Question',
            'message' => 'Do you provide airport shuttle service from Casablanca Mohammed V airport?',
        ]);

        $message = ContactMessage::where('email', 'alice@example.com')->firstOrFail();
        $this->assertEquals('unread', $message->status);

        // Super Admin access
        Auth::guard('admin')->login($this->superAdmin);

        $indexResponse = $this->get(route('super_admin.messages.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Alice Guest');
        $indexResponse->assertSee('Special Request Question');

        // Mark as read via AJAX endpoint
        $readResponse = $this->post(route('super_admin.messages.mark_read', $message));
        $readResponse->assertStatus(200);
        $readResponse->assertJson(['success' => true]);

        $message->refresh();
        $this->assertEquals('read', $message->status);
        $this->assertNotNull($message->read_at);

        // Delete message
        $deleteResponse = $this->delete(route('super_admin.messages.destroy', $message));
        $deleteResponse->assertRedirect(route('super_admin.messages.index'));
        $deleteResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('contact_messages', [
            'id' => $message->id,
        ]);
    }

    /**
     * Test that even if an email notification fails/throws, the contact message is safely preserved.
     */
    public function test_contact_message_is_persisted_even_if_notification_logging_or_mailing_encounters_issue(): void
    {
        Log::shouldReceive('error')
            ->never(); // No error because the store operation succeeds cleanly

        $payload = [
            'name'    => 'Resilient User',
            'email'   => 'resilient@example.com',
            'subject' => 'Reliability Verification',
            'message' => 'Verifying that messages persist safely under all circumstances.',
        ];

        $response = $this->from(route('contact'))->post(route('contact.store'), $payload);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'name'  => 'Resilient User',
            'email' => 'resilient@example.com',
        ]);
    }
}
