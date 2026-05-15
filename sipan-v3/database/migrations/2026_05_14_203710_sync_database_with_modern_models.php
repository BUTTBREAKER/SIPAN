<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insumos
        Schema::table('insumos', function (Blueprint $table) {
            if (!Schema::hasColumn('insumos', 'codigo')) {
                $table->string('codigo', 50)->nullable()->after('nombre');
            }
            if (!Schema::hasColumn('insumos', 'activo')) {
                $table->boolean('activo')->default(true)->after('precio_unitario');
            }
            if (!Schema::hasColumn('insumos', 'created_at')) {
                $table->timestamps();
            }
        });

        // Productos
        Schema::table('productos', function (Blueprint $table) {
            if (!Schema::hasColumn('productos', 'codigo')) {
                $table->string('codigo', 50)->nullable()->after('nombre');
            }
            if (!Schema::hasColumn('productos', 'categoria')) {
                $table->string('categoria', 100)->nullable()->after('codigo');
            }
            if (!Schema::hasColumn('productos', 'precio_venta')) {
                $table->decimal('precio_venta', 10, 2)->default(0)->after('stock_minimo');
            }
            if (!Schema::hasColumn('productos', 'precio_costo')) {
                $table->decimal('precio_costo', 10, 2)->default(0)->after('precio_venta');
            }
            if (!Schema::hasColumn('productos', 'activo')) {
                $table->boolean('activo')->default(true)->after('precio_costo');
            }
            if (!Schema::hasColumn('productos', 'created_at')) {
                $table->timestamps();
            }
        });

        // Ventas
        Schema::table('ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('ventas', 'numero_venta')) {
                $table->string('numero_venta', 50)->nullable()->after('id');
            }
            if (!Schema::hasColumn('ventas', 'total_usd')) {
                $table->decimal('total_usd', 10, 2)->default(0)->after('total');
            }
            if (!Schema::hasColumn('ventas', 'total_ves')) {
                $table->decimal('total_ves', 10, 2)->default(0)->after('total_usd');
            }
            if (!Schema::hasColumn('ventas', 'tasa_bcv')) {
                $table->decimal('tasa_bcv', 10, 4)->default(1)->after('total_ves');
            }
            if (!Schema::hasColumn('ventas', 'notas')) {
                $table->text('notas')->nullable()->after('estado');
            }
            if (!Schema::hasColumn('ventas', 'created_at')) {
                $table->timestamps();
            }
        });

        // Venta Productos (Detalles)
        Schema::table('venta_productos', function (Blueprint $table) {
            if (!Schema::hasColumn('venta_productos', 'precio_unitario_usd')) {
                $table->decimal('precio_unitario_usd', 10, 2)->default(0)->after('precio_unitario');
            }
            if (!Schema::hasColumn('venta_productos', 'precio_unitario_ves')) {
                $table->decimal('precio_unitario_ves', 10, 2)->default(0)->after('precio_unitario_usd');
            }
            if (!Schema::hasColumn('venta_productos', 'subtotal_usd')) {
                $table->decimal('subtotal_usd', 10, 2)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('venta_productos', 'subtotal_ves')) {
                $table->decimal('subtotal_ves', 10, 2)->default(0)->after('subtotal_usd');
            }
        });

        // Compras
        Schema::table('compras', function (Blueprint $table) {
            if (!Schema::hasColumn('compras', 'numero_factura')) {
                $table->string('numero_factura', 50)->nullable()->after('numero_comprobante');
            }
            if (!Schema::hasColumn('compras', 'notas')) {
                $table->text('notas')->nullable()->after('estado');
            }
            if (!Schema::hasColumn('compras', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
        
        // Sucursales
        Schema::table('sucursales', function (Blueprint $table) {
            if (!Schema::hasColumn('sucursales', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration for safety as we are modifying existing legacy tables
    }
};
