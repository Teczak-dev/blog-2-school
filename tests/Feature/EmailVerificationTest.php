<?php

use App\Mail\VerifyEmailMail;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

it('sends verification email when user registers', function () {
    Mail::fake();

    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // Sprawdź czy email został wysłany
    Mail::assertSent(VerifyEmailMail::class, function ($mail) {
        return $mail->hasTo('test@example.com');
    });
});

it('user can verify email with valid link', function () {
    Event::fake();
    
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    Event::assertDispatched(Verified::class);
});

it('user cannot verify email with invalid hash', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
    $response->assertStatus(403);
});

it('shows verification status in profile for unverified user', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $response = $this->actingAs($user)->get('/profile');

    $response->assertSuccessful();
    $response->assertSee('Niezweryfikowany');
    $response->assertSee('Wyślij ponownie email weryfikacyjny');
    $response->assertSee('Wymagana weryfikacja email');
});

it('shows verification status in profile for verified user', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get('/profile');

    $response->assertSuccessful();
    $response->assertSee('Zweryfikowany');
    $response->assertSee('Email zweryfikowany');
    $response->assertDontSee('Wyślij ponownie email weryfikacyjny');
});

it('can resend verification email from profile', function () {
    Mail::fake();
    
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $response = $this->actingAs($user)->post('/email/verification-notification');

    $response->assertRedirect();
    $response->assertSessionHas('status', 'verification-link-sent');
    
    Mail::assertSent(VerifyEmailMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

it('cannot resend verification email if already verified', function () {
    Mail::fake();
    
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->post('/email/verification-notification');

    $response->assertRedirect('/dashboard');
    Mail::assertNotSent(VerifyEmailMail::class);
});

it('verification email contains correct data', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $mail = new VerifyEmailMail($verificationUrl, $user->name);

    expect($mail->verificationUrl)->toBe($verificationUrl);
    expect($mail->userName)->toBe('John Doe');
    expect($mail->envelope()->subject)->toBe('Zweryfikuj swój adres email');
});

it('shows success message after resending verification email', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($user)->post('/email/verification-notification');

    $response = $this->get('/profile');

    $response->assertSee('Nowy link weryfikacyjny został wysłany');
    $response->assertSee('Email wysłany!');
});
