<?php

namespace Database\Seeders;

use App\Models\ConfiguracionGeneral;
use Illuminate\Database\Seeder;

class ConfiguracionGeneralSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            // ═══════════════════════════ GENERAL ═══════════════════════════
            [
                'grupo' => 'general',
                'clave' => 'sistema.nombre',
                'valor' => 'SATA-QR',
                'tipo' => 'string',
                'etiqueta' => 'Nombre del Sistema',
                'descripcion' => 'Nombre que se muestra en el encabezado y reportes.',
                'orden' => 1,
            ],
            [
                'grupo' => 'general',
                'clave' => 'sistema.ugel',
                'valor' => 'UGEL Huacaybamba',
                'tipo' => 'string',
                'etiqueta' => 'UGEL',
                'descripcion' => 'Unidad de Gestión Educativa Local administradora.',
                'orden' => 2,
            ],
            [
                'grupo' => 'general',
                'clave' => 'sistema.region',
                'valor' => 'Huánuco',
                'tipo' => 'string',
                'etiqueta' => 'Región',
                'descripcion' => 'Región o departamento del Perú.',
                'orden' => 3,
            ],
            [
                'grupo' => 'general',
                'clave' => 'sistema.email_soporte',
                'valor' => 'soporte@ugelhuacaybamba.gob.pe',
                'tipo' => 'string',
                'etiqueta' => 'Email de Soporte',
                'descripcion' => 'Correo electrónico para asistencia técnica.',
                'orden' => 4,
            ],
            [
                'grupo' => 'general',
                'clave' => 'sistema.telefono_soporte',
                'valor' => '',
                'tipo' => 'string',
                'etiqueta' => 'Teléfono de Soporte',
                'descripcion' => 'Número de contacto para emergencias del sistema.',
                'orden' => 5,
            ],
            [
                'grupo' => 'general',
                'clave' => 'sistema.siglas',
                'valor' => 'SATA-QR',
                'tipo' => 'string',
                'etiqueta' => 'Siglas del Sistema',
                'descripcion' => 'Abreviatura que se muestra en el sidebar junto al logo.',
                'orden' => 6,
            ],
            [
                'grupo' => 'general',
                'clave' => 'sistema.subtitulo_sidebar',
                'valor' => 'Huacaybamba',
                'tipo' => 'string',
                'etiqueta' => 'Subtítulo del Sidebar',
                'descripcion' => 'Texto debajo de las siglas en el menú lateral (ej: nombre de UGEL).',
                'orden' => 7,
            ],
            [
                'grupo' => 'general',
                'clave' => 'sistema.descripcion_seo',
                'valor' => 'Sistema de Alerta Temprana y Control de Asistencia mediante códigos QR para la prevención de la deserción escolar.',
                'tipo' => 'string',
                'etiqueta' => 'Descripción SEO',
                'descripcion' => 'Meta descripción para buscadores y redes sociales.',
                'orden' => 8,
            ],
            [
                'grupo' => 'general',
                'clave' => 'sistema.palabras_clave',
                'valor' => 'asistencia escolar, QR, deserción, UGEL, alerta temprana, Perú',
                'tipo' => 'string',
                'etiqueta' => 'Palabras Clave SEO',
                'descripcion' => 'Keywords separadas por coma para meta tags de buscadores.',
                'orden' => 9,
            ],

            // ═══════════════════════════ ASISTENCIA ═══════════════════════════
            [
                'grupo' => 'asistencia',
                'clave' => 'asistencia.hora_inicio_escaneo',
                'valor' => '06:30',
                'tipo' => 'string',
                'etiqueta' => 'Hora Inicio de Escaneo',
                'descripcion' => 'Hora desde la cual se acepta escaneo QR.',
                'orden' => 1,
            ],
            [
                'grupo' => 'asistencia',
                'clave' => 'asistencia.hora_fin_escaneo',
                'valor' => '09:00',
                'tipo' => 'string',
                'etiqueta' => 'Hora Fin de Escaneo',
                'descripcion' => 'Hora límite para registrar asistencia.',
                'orden' => 2,
            ],
            [
                'grupo' => 'asistencia',
                'clave' => 'asistencia.minutos_tolerancia_global',
                'valor' => '15',
                'tipo' => 'integer',
                'etiqueta' => 'Tolerancia Global (minutos)',
                'descripcion' => 'Minutos de tolerancia predeterminado para todas las IE.',
                'orden' => 3,
            ],
            [
                'grupo' => 'asistencia',
                'clave' => 'asistencia.permitir_registro_manual',
                'valor' => '1',
                'tipo' => 'boolean',
                'etiqueta' => 'Permitir Registro Manual',
                'descripcion' => 'Los docentes pueden registrar asistencia sin QR.',
                'orden' => 4,
            ],
            [
                'grupo' => 'asistencia',
                'clave' => 'asistencia.cierre_automatico',
                'valor' => '1',
                'tipo' => 'boolean',
                'etiqueta' => 'Cierre Automático Diario',
                'descripcion' => 'Cerrar asistencia automáticamente al fin de la jornada.',
                'orden' => 5,
            ],
            [
                'grupo' => 'asistencia',
                'clave' => 'asistencia.hora_cierre_automatico',
                'valor' => '14:00',
                'tipo' => 'string',
                'etiqueta' => 'Hora de Cierre Automático',
                'descripcion' => 'Hora en que se ejecuta el cierre automático.',
                'orden' => 6,
            ],

            // ═══════════════════════════ ALERTAS ═══════════════════════════
            [
                'grupo' => 'alertas',
                'clave' => 'alertas.dias_inasistencia_leve',
                'valor' => '3',
                'tipo' => 'integer',
                'etiqueta' => 'Días para Alerta Leve',
                'descripcion' => 'Inasistencias consecutivas para generar alerta leve.',
                'orden' => 1,
            ],
            [
                'grupo' => 'alertas',
                'clave' => 'alertas.dias_inasistencia_moderado',
                'valor' => '5',
                'tipo' => 'integer',
                'etiqueta' => 'Días para Alerta Moderada',
                'descripcion' => 'Inasistencias consecutivas para alerta moderada.',
                'orden' => 2,
            ],
            [
                'grupo' => 'alertas',
                'clave' => 'alertas.dias_inasistencia_critico',
                'valor' => '10',
                'tipo' => 'integer',
                'etiqueta' => 'Días para Alerta Crítica',
                'descripcion' => 'Inasistencias consecutivas para alerta crítica (riesgo deserción).',
                'orden' => 3,
            ],
            [
                'grupo' => 'alertas',
                'clave' => 'alertas.porcentaje_tardanzas_alerta',
                'valor' => '30',
                'tipo' => 'integer',
                'etiqueta' => '% Tardanzas para Alerta',
                'descripcion' => 'Porcentaje de tardanzas sobre total de días para generar alerta.',
                'orden' => 4,
            ],
            [
                'grupo' => 'alertas',
                'clave' => 'alertas.generacion_automatica',
                'valor' => '1',
                'tipo' => 'boolean',
                'etiqueta' => 'Generación Automática',
                'descripcion' => 'Generar alertas automáticamente al cerrar la jornada.',
                'orden' => 5,
            ],
            [
                'grupo' => 'alertas',
                'clave' => 'alertas.notificar_director',
                'valor' => '1',
                'tipo' => 'boolean',
                'etiqueta' => 'Notificar al Director',
                'descripcion' => 'Enviar notificación al director cuando se genera una alerta crítica.',
                'orden' => 6,
            ],

            // ═══════════════════════════ SEGURIDAD ═══════════════════════════
            [
                'grupo' => 'seguridad',
                'clave' => 'seguridad.max_intentos_login',
                'valor' => '5',
                'tipo' => 'integer',
                'etiqueta' => 'Máx. Intentos de Login',
                'descripcion' => 'Intentos fallidos antes de bloquear temporalmente.',
                'orden' => 1,
            ],
            [
                'grupo' => 'seguridad',
                'clave' => 'seguridad.minutos_bloqueo',
                'valor' => '15',
                'tipo' => 'integer',
                'etiqueta' => 'Minutos de Bloqueo',
                'descripcion' => 'Tiempo de bloqueo después de superar intentos máximos.',
                'orden' => 2,
            ],
            [
                'grupo' => 'seguridad',
                'clave' => 'seguridad.duracion_sesion_minutos',
                'valor' => '120',
                'tipo' => 'integer',
                'etiqueta' => 'Duración de Sesión (min)',
                'descripcion' => 'Tiempo de inactividad antes de cerrar sesión automáticamente.',
                'orden' => 3,
            ],
            [
                'grupo' => 'seguridad',
                'clave' => 'seguridad.forzar_cambio_clave',
                'valor' => '0',
                'tipo' => 'boolean',
                'etiqueta' => 'Forzar Cambio de Clave',
                'descripcion' => 'Pedir a usuarios nuevos que cambien su contraseña al primer login.',
                'orden' => 4,
            ],
            [
                'grupo' => 'seguridad',
                'clave' => 'seguridad.longitud_minima_clave',
                'valor' => '8',
                'tipo' => 'integer',
                'etiqueta' => 'Longitud Mínima de Clave',
                'descripcion' => 'Caracteres mínimos requeridos para contraseñas.',
                'orden' => 5,
            ],

            // ═══════════════════════════ APARIENCIA ═══════════════════════════
            [
                'grupo' => 'apariencia',
                'clave' => 'apariencia.color_primario',
                'valor' => '#4f46e5',
                'tipo' => 'string',
                'etiqueta' => 'Color Primario',
                'descripcion' => 'Color principal de la interfaz (formato hexadecimal).',
                'orden' => 1,
            ],
            [
                'grupo' => 'apariencia',
                'clave' => 'apariencia.mostrar_logo_ugel',
                'valor' => '1',
                'tipo' => 'boolean',
                'etiqueta' => 'Mostrar Logo UGEL',
                'descripcion' => 'Mostrar el logo de la UGEL en el menú lateral.',
                'orden' => 2,
            ],
            [
                'grupo' => 'apariencia',
                'clave' => 'apariencia.pie_pagina',
                'valor' => '© 2026 UGEL Huacaybamba — Sistema de Alerta Temprana',
                'tipo' => 'string',
                'etiqueta' => 'Pie de Página',
                'descripcion' => 'Texto que se muestra en el footer de todas las páginas.',
                'orden' => 3,
            ],
            [
                'grupo' => 'apariencia',
                'clave' => 'apariencia.mostrar_referencia_demo',
                'valor' => '1',
                'tipo' => 'boolean',
                'etiqueta' => 'Mostrar Referencia Visual',
                'descripcion' => 'Mostrar el enlace a la biblioteca demo de Tailwick en el menú.',
                'orden' => 4,
            ],
        ];

        foreach ($configs as $config) {
            ConfiguracionGeneral::firstOrCreate(
                ['clave' => $config['clave']],
                $config
            );
        }
    }
}
