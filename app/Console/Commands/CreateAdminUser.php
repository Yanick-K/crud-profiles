<?php

namespace App\Console\Commands;
use App\Models\User;
use Illuminate\Console\Command;
use function Laravel\Prompts\text;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

class CreateAdminUser extends Command
{
    /**
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * @var string
     */
    protected $description = 'Create admin user';

    public function handle(): int
    {
        $email = text('What is the email?', validate: ['email' => 'email|max:255|unique:users']);
        $name = text('What is the name (optional)?', validate: ['name' => 'string|max:255']);
        $password = text('What is the password?', validate: ['password' => 'required|string|max:255']);

        try {
            if ($name === "") {
                $name = explode('@', $email)[0];
            }
            User::create(['name' => $name, 'email' => $email, 'password' => bcrypt($password)]);
            info('Admin user created');
            table(['Name', 'Email'], [[$name, $email]]);
            return self::SUCCESS;
        }
        catch (\Exception $e) {
            error($e->getMessage());
            return self::FAILURE;
        }
    }
}
