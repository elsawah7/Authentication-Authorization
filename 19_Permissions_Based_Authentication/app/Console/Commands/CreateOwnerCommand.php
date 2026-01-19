<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class CreateOwnerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:owner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command creates an owner user';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        try {
            if (!Role::where('name', 'owner')->first()) {
                Artisan::call('db.seed', ['--class' => 'RoleAndPermissionSeeder']);
            }

            $name = $this->ask('What is the owner name?');
            $email = $this->ask('What is the owner email?');
            $password = $this->ask('What is the owner password?');

            $validated = Validator::make([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ], [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8',
            ])->validate();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'account_verified_at' => now(),
            ]);

            $ownerRole = Role::where('name', 'owner')->first();
            $user->roles()->attach($ownerRole->id);
            $this->info('Owner ' . $user->name . ' created successfully.');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->error($message);
                }
            }
        }
    }
}
