<?php

use App\Enums\DonationStatus;
use App\Enums\DonationType;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use App\Services\PaymentService;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Laravel\Cashier\Payment;
use Mockery\MockInterface;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->campaign = Campaign::factory()->create();
});

it('creates a donation and returns the resource with a null clientSecret on success', function () {
    $fakePaymentIntent = (object) ['id' => 'pi_test_123'];
    $fakePayment = Mockery::mock(Payment::class, function (MockInterface $mock) use ($fakePaymentIntent) {
        $mock->shouldReceive('asStripePaymentIntent')->andReturn($fakePaymentIntent);
    });

    $this->mock(PaymentService::class, function (MockInterface $mock) use ($fakePayment) {
        $mock->shouldReceive('createPaymentIntent')->once()->andReturn($fakePayment);
    });

    $response = $this->actingAs($this->user)
        ->postJson("/api/campaigns/{$this->campaign->id}/donate", [
            'type' => DonationType::OneTime->value,
            'amount' => 1000,
            'payment_method_id' => 'pm_test_abc',
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.campaignId', $this->campaign->id)
        ->assertJsonPath('data.amount', 1000)
        ->assertJsonPath('data.status', DonationStatus::Pending->value)
        ->assertJsonPath('clientSecret', null);

    expect(Donation::where('campaign_id', $this->campaign->id)->exists())->toBeTrue();
});

it('returns a clientSecret when payment requires action (3DS)', function () {
    $fakePaymentIntent = (object) ['id' => 'pi_test_3ds', 'client_secret' => 'pi_test_3ds_secret_xyz'];

    $incompletePayment = Mockery::mock(Payment::class, function (MockInterface $mock) use ($fakePaymentIntent) {
        $mock->shouldReceive('asStripePaymentIntent')->andReturn($fakePaymentIntent);
    });

    $this->mock(PaymentService::class, function (MockInterface $mock) use ($incompletePayment) {
        $mock->shouldReceive('createPaymentIntent')->once()
            ->andThrow(new IncompletePayment($incompletePayment));
    });

    $response = $this->actingAs($this->user)
        ->postJson("/api/campaigns/{$this->campaign->id}/donate", [
            'type' => DonationType::OneTime->value,
            'amount' => 2500,
            'payment_method_id' => 'pm_test_3ds',
        ]);

    $response->assertCreated()
        ->assertJsonPath('clientSecret', 'pi_test_3ds_secret_xyz');
});

it('rejects donations to closed campaigns', function () {
    $closed = Campaign::factory()->closed()->create();

    $this->mock(PaymentService::class);

    $this->actingAs($this->user)
        ->postJson("/api/campaigns/{$closed->id}/donate", [
            'type' => DonationType::OneTime->value,
            'amount' => 500,
            'payment_method_id' => 'pm_test_abc',
        ])
        ->assertForbidden();
});

it('requires authentication', function () {
    $this->postJson("/api/campaigns/{$this->campaign->id}/donate", [
        'type' => DonationType::OneTime->value,
        'amount' => 500,
    ])->assertUnauthorized();
});
