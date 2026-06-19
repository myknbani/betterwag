<?php

namespace App\Jobs;

use App\Enums\DonationStatus;
use App\Models\Donation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class HandleSuccessfulPayment implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Donation $donation) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->donation->status === DonationStatus::Paid) {
            return;
        }

        $this->donation->update([
            'status' => DonationStatus::Paid,
            'paid_at' => now(),
        ]);
    }
}
