<?php

namespace Database\Seeders;

use App\Models\chatpublic;
use App\Models\Conversation;
use App\Models\ConversationUser;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create user
        $user = User::factory()->create([
            'name' => 'ouafa',
            'email' => 'developerouafa@gmail.com',
            'password' => Hash::make('12345678'), // 🔑 لازم تشفر password
        ]);

        // Create Role
        $role = Role::create(['name' => 'user', 'guard_name' => 'web']);

        // Permissions with guard_name
        $permissions = [
            'view-users',
            'edit-users',
            'delete-users'
        ];

        foreach($permissions as $perm){
            Permission::create([
                'name' => $perm,
                'guard_name' => 'web' // ⚡ مهم
            ]);
        }

        // Attach all web permissions to role
        $role->syncPermissions(Permission::where('guard_name','web')->get());

        // Assign role to user
        $user->assignRole($role);

        User::factory()->create([
            'name' => 'Ouafa1',
            'email' => 'Ouafa1@example.com',
            'password' => bcrypt('12345678'),
        ]);
        User::factory()->create([
            'name' => 'Ouafa2',
            'email' => 'Ouafa2@example.com',
            'password' => bcrypt('12345678'),
        ]);
        User::factory()->create([
            'name' => 'Ouafa3',
            'email' => 'Ouafa3@example.com',
            'password' => bcrypt('12345678'),
        ]);
        User::factory()->create([
            'name' => 'Ouafa4',
            'email' => 'Ouafa4@example.com',
            'password' => bcrypt('12345678'),
        ]);
        User::factory()->create([
            'name' => 'Ouafa5',
            'email' => 'Ouafa5@example.com',
            'password' => bcrypt('12345678'),
        ]);
        User::factory()->create([
            'name' => 'Ouafa6',
            'email' => 'Ouafa6@example.com',
            'password' => bcrypt('12345678'),
        ]);
        User::factory()->create([
            'name' => 'Ouafa7',
            'email' => 'Ouafa7@example.com',
            'password' => bcrypt('12345678'),
        ]);
        User::factory()->create([
            'name' => 'Ouafa8',
            'email' => 'Ouafa8@example.com',
            'password' => bcrypt('12345678'),
        ]);


           // رسائل تجريبية
        chatpublic::create([
            'user_id' => '1',
            'content' => 'السلام عليكم'
        ]);

        chatpublic::create([
            'user_id' => '2',
            'content' => 'مرحبا أحمد!'
        ]);

        chatpublic::create([
            'user_id' => '3',
            'content' => 'كيف حال الجميع؟'
        ]);



        Conversation::create([
            'id' => '1',
        ]);
        Conversation::create([
            'id' => '2',
        ]);

        // إنشاء رسائل
        Message::create([
            'conversation_id' => '1',
            'user_id' => '1',
            'content' => 'السلام، كيف داير؟'
        ]);

        Message::create([
            'conversation_id' => '1',
            'user_id' => '2',
            'content' => 'الحمد لله، ونتا؟'
        ]);
    }
}
