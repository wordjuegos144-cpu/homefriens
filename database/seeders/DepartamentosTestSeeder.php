<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmpresaAdministradora;
use App\Models\Propietario;
use App\Models\Departamento;

class DepartamentosTestSeeder extends Seeder
{
    public function run()
    {
        // Crear una empresa administradora
        $empresa = EmpresaAdministradora::create([
            'nombre' => 'Inmobiliaria Central',
            'direccion' => 'Av. Principal 123',
            'telefono' => '123456789'
        ]);

        // Crear propietarios
        $propietario1 = Propietario::create([
            'nombre' => 'Juan Pérez',
            'telefono' => '987654321',
            'email' => 'juan@example.com'
        ]);

        $propietario2 = Propietario::create([
            'nombre' => 'María González',
            'telefono' => '987654322',
            'email' => 'maria@example.com'
        ]);

        // Crear departamentos de prueba
        $departamentos = [
            [
                'idEmpresaAdministradora' => $empresa->id,
                'idPropietario' => $propietario1->id,
                'nombreEdificio' => 'Los Tajibos',
                'direccion' => 'Av. Los Tajibos #123',
                'descripcion' => 'Hermoso departamento con vista a la ciudad, totalmente amoblado y equipado',
                'piso' => 5,
                'numero' => 501,
                'capacidadNormal' => 4,
                'capacidadExtra' => 2,
                'cuartos' => 3,
                'banos' => 2,
                'servicios' => json_encode([
                    'wifi',
                    'aire acondicionado',
                    'TV cable',
                    'piscina',
                    'gimnasio',
                    'estacionamiento'
                ]),
                'imagenes' => json_encode([
                    'https://via.placeholder.com/800x600?text=Los+Tajibos+1',
                    'https://via.placeholder.com/800x600?text=Los+Tajibos+2',
                    'https://via.placeholder.com/800x600?text=Los+Tajibos+3'
                ])
            ],
            [
                'idEmpresaAdministradora' => $empresa->id,
                'idPropietario' => $propietario2->id,
                'nombreEdificio' => 'Torres del Sol',
                'direccion' => 'Calle del Sol #456',
                'descripcion' => 'Moderno departamento en zona exclusiva, excelente ubicación',
                'piso' => 8,
                'numero' => 802,
                'capacidadNormal' => 2,
                'capacidadExtra' => 1,
                'cuartos' => 1,
                'banos' => 1,
                'servicios' => json_encode([
                    'wifi',
                    'aire acondicionado',
                    'TV cable',
                    'lavandería',
                    'seguridad 24/7'
                ]),
                'imagenes' => json_encode([
                    'https://via.placeholder.com/800x600?text=Torres+del+Sol+1',
                    'https://via.placeholder.com/800x600?text=Torres+del+Sol+2'
                ])
            ],
            [
                'idEmpresaAdministradora' => $empresa->id,
                'idPropietario' => $propietario1->id,
                'nombreEdificio' => 'Residencial Las Palmas',
                'direccion' => 'Av. Las Palmas #789',
                'descripcion' => 'Amplio departamento familiar con todas las comodidades',
                'piso' => 3,
                'numero' => 304,
                'capacidadNormal' => 6,
                'capacidadExtra' => 2,
                'cuartos' => 4,
                'banos' => 3,
                'servicios' => json_encode([
                    'wifi',
                    'aire acondicionado',
                    'TV cable',
                    'piscina',
                    'área de juegos',
                    'gimnasio',
                    'estacionamiento doble'
                ]),
                'imagenes' => json_encode([
                    'https://via.placeholder.com/800x600?text=Las+Palmas+1',
                    'https://via.placeholder.com/800x600?text=Las+Palmas+2',
                    'https://via.placeholder.com/800x600?text=Las+Palmas+3',
                    'https://via.placeholder.com/800x600?text=Las+Palmas+4'
                ])
            ]
        ];

        foreach ($departamentos as $depto) {
            Departamento::create($depto);
        }
    }
}