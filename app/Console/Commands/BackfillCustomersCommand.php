<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Models\Customer;

class BackfillCustomersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfills existing reservations by creating or linking Customers based on guest_phone';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting customer backfill...');

        $reservations = Reservation::whereNull('customer_id')->get();
        $totalReservations = Reservation::count();
        $unlinkedInitially = $reservations->count();
        $linkedInitially = $totalReservations - $unlinkedInitially;
        
        $this->info("Total reservations: {$totalReservations}");
        $this->info("Already linked: {$linkedInitially}");
        $this->info("To process: {$unlinkedInitially}");

        $customersCreated = 0;
        $linked = 0;
        $unlinked = 0;

        foreach ($reservations as $reservation) {
            if (empty($reservation->guest_phone)) {
                $unlinked++;
                continue;
            }

            $normalizedPhone = Customer::normalizePhone($reservation->guest_phone);
            
            if (empty($normalizedPhone)) {
                $unlinked++;
                continue;
            }

            $customer = Customer::where('hotel_id', $reservation->hotel_id)
                ->where('phone', $normalizedPhone)
                ->first();

            if (!$customer) {
                $customer = Customer::create([
                    'hotel_id' => $reservation->hotel_id,
                    'phone'    => $normalizedPhone,
                    'name'     => $reservation->guest_name,
                ]);
                $customersCreated++;
            }

            $reservation->update(['customer_id' => $customer->id]);
            $linked++;
        }

        $this->info('Backfill complete!');
        $this->info("Total reservations: {$totalReservations}");
        $this->info("Successfully linked now: {$linked}");
        $this->info("Unlinked (no valid phone): {$unlinked}");
        $this->info("Customers created: {$customersCreated}");
    }
}
