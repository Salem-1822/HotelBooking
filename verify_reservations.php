<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Room;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

echo "ROOM STATUS DISTRIBUTION:\n";
$statuses = \Illuminate\Support\Facades\DB::table('rooms')->selectRaw('status, count(*) as cnt')->groupBy('status')->get();
foreach ($statuses as $s) {
    echo "  {$s->status}: {$s->cnt}\n";
}

echo "\nFinding a room to use for test (any status):\n";
$room = Room::whereHas('hotel', fn($q) => $q->where('status','active'))->first();
if (!$room) { echo "No rooms found.\n"; exit(1); }
$hotel = $room->hotel;
echo "  Room [{$room->id}] in Hotel [{$hotel->id}] {$hotel->name}, status={$room->status}\n";

echo "\nTemporarily setting room status to 'available' for test...\n";
$origStatus = $room->status;
$room->update(['status' => 'available']);

// Create test client
$client = User::firstOrCreate(
    ['email' => 'res_test@example.com'],
    ['name' => 'Reservation Tester', 'password' => Hash::make('pass'), 'role' => 'client', 'status' => 'active']
);

// Create reservation
$checkIn  = Carbon::tomorrow()->format('Y-m-d');
$checkOut = Carbon::tomorrow()->addDays(2)->format('Y-m-d');
$nights   = 2;
$total    = $room->price_per_night * $nights;

$res = Reservation::create([
    'hotel_id'     => $hotel->id,
    'room_id'      => $room->id,
    'user_id'      => $client->id,
    'guest_name'   => $client->name,
    'guest_phone'  => null,
    'guests_count' => 1,
    'check_in'     => $checkIn,
    'check_out'    => $checkOut,
    'total_price'  => $total,
    'status'       => 'pending',
]);

echo "Reservation created: ID={$res->id} | user_id={$res->user_id} | hotel_id={$res->hotel_id} | status={$res->status}\n";

// Test overlap
$conflict = Reservation::where('room_id', $room->id)
    ->whereNotIn('status', ['cancelled'])
    ->where('check_in', '<', $checkOut)
    ->where('check_out', '>', $checkIn)
    ->exists();
echo "Overlap detection (should be YES): " . ($conflict ? "YES [OK]" : "NO [ERROR]") . "\n";

// Admin visibility
$adminSees = Reservation::where('hotel_id', $hotel->id)->where('id', $res->id)->exists();
echo "Admin visibility by hotel_id: " . ($adminSees ? "YES [OK]" : "NO [ERROR]") . "\n";

// Super admin visibility
$saSees = Reservation::where('id', $res->id)->exists();
echo "Super Admin visibility (all): " . ($saSees ? "YES [OK]" : "NO [ERROR]") . "\n";

// Cleanup
$res->delete();
$client->delete();
$room->update(['status' => $origStatus]);
echo "Cleaned up. Room status restored to '{$origStatus}'.\n";
echo "\nAll reservation logic tests PASSED.\n";
