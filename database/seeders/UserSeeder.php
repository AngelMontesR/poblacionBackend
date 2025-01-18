<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Creacion de usuarios ejemplo
     */
    public function run(): void
    {
        // Admin
        $admin                      = new User();
        $admin->name                = "Administrador";
        $admin->email               = "email1@gmail.com";
        $admin->password            = Hash::make('password');
        $admin->save();

        // Asignar rol
        $admin->assignRole('admin');

        // Consultor
        $consultor                      = new User();
        $consultor->name                = "Consultor";
        $consultor->email               = "email2@gmail.com";
        $consultor->password            = Hash::make('password');
        $consultor->save();

        // Asignar rol
        $consultor->assignRole('consultor');

    }
}
