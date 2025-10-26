<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

class SendVerificationCode extends Command
{
    protected $signature = 'auth:send-verification {email}';
    protected $description = 'Enviar código de verificación por correo al usuario especificado';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Usuario no encontrado: {$email}");
            return 1;
        }

        // Generar OTP aleatorio
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        try {
            Mail::to($user->email)->send(new VerificationCodeMail($user, $code));
            $user->forceFill([
                'verification_code' => $code,
                'verification_code_sent_at' => now(),
                'email_verified_at' => null,
            ])->save();

            $this->info("Código enviado a {$user->email}: {$code}");
            return 0;
        } catch (\Throwable $e) {
            $this->error('Error enviando correo: ' . $e->getMessage());
            return 1;
        }
    }
}