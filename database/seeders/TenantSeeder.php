<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\TenantNivel;
use App\Models\ConfiguracionAsistencia;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Instituciones Educativas reales de la UGEL Huacaybamba.
     */
    public function run(): void
    {
        $instituciones = [
            [
                'id' => 'ie-huacaybamba',
                'nombre' => 'I.E. Huacaybamba Integrada',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295352'],
                    ['nivel' => 'Secundaria', 'codigo_modular' => '0576058'],
                ],
                'color' => '#1e3a8a',
                'hora' => '07:45:00',
                'tolerancia' => 15,
            ],
            [
                'id' => 'ie-canchabamba',
                'nombre' => 'I.E. Canchabamba',
                'niveles' => [
                    ['nivel' => 'Secundaria', 'codigo_modular' => '0576066'],
                ],
                'color' => '#b91c1c',
                'hora' => '08:00:00',
                'tolerancia' => 10,
            ],
            [
                'id' => 'ie-cochabamba',
                'nombre' => 'I.E. Cochabamba',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295360'],
                    ['nivel' => 'Secundaria', 'codigo_modular' => '0661421'],
                ],
                'color' => '#047857',
                'hora' => '07:30:00',
                'tolerancia' => 15,
            ],
            [
                'id' => 'ie-pinra',
                'nombre' => 'I.E. Pinra',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295378'],
                    ['nivel' => 'Secundaria', 'codigo_modular' => '0661439'],
                ],
                'color' => '#7c3aed',
                'hora' => '08:00:00',
                'tolerancia' => 10,
            ],
            [
                'id' => 'ie-san-jose-de-pucara',
                'nombre' => 'I.E. San José de Pucará',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295386'],
                ],
                'color' => '#c2410c',
                'hora' => '08:00:00',
                'tolerancia' => 15,
            ],
            [
                'id' => 'ie-quiprán',
                'nombre' => 'I.E. Quiprán',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295394'],
                    ['nivel' => 'Secundaria', 'codigo_modular' => '1348283'],
                ],
                'color' => '#0369a1',
                'hora' => '07:45:00',
                'tolerancia' => 10,
            ],
            [
                'id' => 'ie-rondobamba',
                'nombre' => 'I.E. Rondobamba',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295402'],
                ],
                'color' => '#a16207',
                'hora' => '08:00:00',
                'tolerancia' => 15,
            ],
            [
                'id' => 'ie-huaracillo',
                'nombre' => 'I.E. Huaracillo',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295410'],
                    ['nivel' => 'Secundaria', 'codigo_modular' => '1348291'],
                ],
                'color' => '#be185d',
                'hora' => '07:30:00',
                'tolerancia' => 10,
            ],
            [
                'id' => 'ie-arancay',
                'nombre' => 'I.E. Arancay',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295428'],
                    ['nivel' => 'Secundaria', 'codigo_modular' => '0661447'],
                ],
                'color' => '#059669',
                'hora' => '07:45:00',
                'tolerancia' => 15,
            ],
            [
                'id' => 'ie-crespo-castillo',
                'nombre' => 'I.E. Crespo y Castillo',
                'niveles' => [
                    ['nivel' => 'Secundaria', 'codigo_modular' => '0661454'],
                ],
                'color' => '#4338ca',
                'hora' => '08:00:00',
                'tolerancia' => 10,
            ],
            [
                'id' => 'ie-yanas',
                'nombre' => 'I.E. Yanas',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295436'],
                    ['nivel' => 'Secundaria', 'codigo_modular' => '1348309'],
                ],
                'color' => '#dc2626',
                'hora' => '07:30:00',
                'tolerancia' => 15,
            ],
            [
                'id' => 'ie-huanacaure',
                'nombre' => 'I.E. Huanacaure',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295444'],
                ],
                'color' => '#0891b2',
                'hora' => '08:00:00',
                'tolerancia' => 10,
            ],
            [
                'id' => 'ie-santa-rosa-de-rayan',
                'nombre' => 'I.E. Santa Rosa de Rayán',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295451'],
                    ['nivel' => 'Secundaria', 'codigo_modular' => '1348317'],
                ],
                'color' => '#65a30d',
                'hora' => '07:45:00',
                'tolerancia' => 15,
            ],
            [
                'id' => 'ie-jircacancha',
                'nombre' => 'I.E. Jircacancha',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295469'],
                ],
                'color' => '#9333ea',
                'hora' => '08:00:00',
                'tolerancia' => 10,
            ],
            [
                'id' => 'ie-chocobamba',
                'nombre' => 'I.E. Chocobamba',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295477'],
                    ['nivel' => 'Secundaria', 'codigo_modular' => '1348325'],
                ],
                'color' => '#e11d48',
                'hora' => '07:30:00',
                'tolerancia' => 15,
            ],
            [
                'id' => 'ie-sacag',
                'nombre' => 'I.E. Sacag',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295485'],
                ],
                'color' => '#0d9488',
                'hora' => '08:00:00',
                'tolerancia' => 10,
            ],
            [
                'id' => 'ie-huayush',
                'nombre' => 'I.E. Huayush',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295493'],
                ],
                'color' => '#ca8a04',
                'hora' => '07:45:00',
                'tolerancia' => 15,
            ],
            [
                'id' => 'ie-pucayacu',
                'nombre' => 'I.E. Pucayacu',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295501'],
                ],
                'color' => '#2563eb',
                'hora' => '08:00:00',
                'tolerancia' => 10,
            ],
            [
                'id' => 'ie-san-pedro-de-chonta',
                'nombre' => 'I.E. San Pedro de Chonta',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295519'],
                    ['nivel' => 'Secundaria', 'codigo_modular' => '1348333'],
                ],
                'color' => '#db2777',
                'hora' => '07:30:00',
                'tolerancia' => 15,
            ],
            [
                'id' => 'ie-mitoquera',
                'nombre' => 'I.E. Mitoquera',
                'niveles' => [
                    ['nivel' => 'Primaria', 'codigo_modular' => '0295527'],
                ],
                'color' => '#16a34a',
                'hora' => '08:00:00',
                'tolerancia' => 10,
            ],
        ];

        foreach ($instituciones as $ie) {
            $tenant = Tenant::firstOrCreate(['id' => $ie['id']], [
                'nombre' => $ie['nombre'],
                'ugel' => 'HUACAYBAMBA',
                'config' => ['primary_color' => $ie['color']],
            ]);

            foreach ($ie['niveles'] as $nivel) {
                TenantNivel::firstOrCreate(
                    ['tenant_id' => $tenant->id, 'nivel' => $nivel['nivel']],
                    ['codigo_modular' => $nivel['codigo_modular']]
                );
            }

            ConfiguracionAsistencia::updateOrCreate(['tenant_id' => $tenant->id], [
                'hora_entrada_regular' => $ie['hora'],
                'minutos_tolerancia' => $ie['tolerancia'],
            ]);
        }
    }
}
