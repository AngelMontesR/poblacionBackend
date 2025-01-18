<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seeder inicial para creacion de roles y permisos
     */
    public function run(): void
    {
        // Creacion de roles
        $admin = Role::create(['name' => 'admin']);
        $consultor = Role::create(['name' => 'consultor']);

        // Catalogo Permisos
        Permission::create(['name' => 'poblacion.carga']);
        Permission::create(['name' => 'poblacion.consulta']);

        // Asignacion de permisos con roles anteriormente creados
        $admin->givePermissionTo(['poblacion.carga', 'poblacion.consulta']);
        $consultor->givePermissionTo(['poblacion.consulta']);
    }
}
