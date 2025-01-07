<?php

namespace Didix16\LaraPrime\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'laraprime:admin')]
class AdminCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'laraprime:admin {name?} {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user for LaraPrime';

    public function handle(): void
    {
        $this->comment('Detecting User Model...');
        $userModel = config('auth.providers.users.model', 'App\Models\User');
        if (class_exists($userModel)) {
            $this->comment('User Model detected: '.$userModel);
            if (is_callable([$userModel, 'create'])) {

                $user = $this->createAdmin(
                    $userModel,
                    $this->argument('name') ?? $this->ask('Enter your name: ', 'Admin'),
                    $this->argument('email') ?? $this->ask('Enter your email: ', 'admin@admin.com'),
                    $this->argument('password') ?? $this->secret('Enter your password: ')
                );

                $this->comment(sprintf('Admin user [%s] created successfully!', $user->email));
            } else {
                $this->error('User Model does not have a create method!');

                return;
            }
        } else {
            $this->error('User Model not found!');

            return;
        }
    }

    /**
     * @throws \Throwable
     */
    protected function createAdmin(string $userModel, string $name, string $email, string $password): Model
    {
        throw_if($userModel::where('email', $email)->exists(), 'User exists');

        return $userModel::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }
}
