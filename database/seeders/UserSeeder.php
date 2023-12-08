<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener roles existentes desde la base de datos
        $adminRole = Role::find(1);
        $vendedorRole = Role::find(2);
        $consultaRole = Role::find(3);

        // Crear y asignar roles a usuarios específicos
        $adminUser = new User();
        $adminUser->name= "Usuario administrador";
        $adminUser->email= "admin@example.com";
        $adminUser->password= bcrypt("123456");
        $adminUser->save();
        $adminUser->assignRole($adminRole);

        $vendedorUser = new User();
        $vendedorUser->name= "Usuario vendedor";
        $vendedorUser->email= "vendedor@example.com";
        $vendedorUser->password= bcrypt("789459");
        $vendedorUser->save();
        $vendedorUser->assignRole($vendedorRole);

        $consultaUser = new User();
        $consultaUser->name= "Usuario consulta";
        $consultaUser->email= "consulta@example.com";
        $consultaUser->password= bcrypt("654856");
        $consultaUser->save();
        $consultaUser->assignRole($consultaRole);
    }
}
