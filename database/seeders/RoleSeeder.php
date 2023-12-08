<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Crear roles con identificadores y nombres específicos
         $adminRol = Role::create(["id" => 1, "name" => "admin"]);
         $vendedorRol = Role::create(["id" => 2, "name" => "vendedor"]);
         $consultaRol = Role::create(["id" => 3, "name" => "consulta"]);
 
         // Asignar permisos específicos a los otros roles si es necesario
         Permission::create(["name" => "vender"])->assignRole($vendedorRol);
         Permission::create(["name" => "consultar"])->assignRole($consultaRol);

         //Permiso admin
         Permission::create(["name" => "administrar"]);
 
         // Obtener todos los permisos
         $todosLosPermisos = Permission::all();
 
         // Asignar todos los permisos al rol de administrador
         $adminRol->syncPermissions($todosLosPermisos);
    }
}
