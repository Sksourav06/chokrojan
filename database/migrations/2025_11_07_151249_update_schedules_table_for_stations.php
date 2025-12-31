<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Step 1: Add new columns safely
        Schema::table('schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('schedules', 'route_tagline')) {
                $table->string('route_tagline')->nullable()->after('name');
            }

            // Ensure unsignedBigInteger for foreign key columns
            if (!Schema::hasColumn('schedules', 'bus_id')) {
                $table->unsignedBigInteger('bus_id')->nullable()->after('route_tagline');
            }

            if (!Schema::hasColumn('schedules', 'start_station_id')) {
                $table->unsignedBigInteger('start_station_id')->nullable()->after('route_id');
            }

            if (!Schema::hasColumn('schedules', 'end_station_id')) {
                $table->unsignedBigInteger('end_station_id')->nullable()->after('start_station_id');
            }
        });

        // Step 2: Rename departure_time to start_time safely
        if (Schema::hasColumn('schedules', 'departure_time') && !Schema::hasColumn('schedules', 'start_time')) {
            // Requires doctrine/dbal
            Schema::table('schedules', function (Blueprint $table) {
                $table->renameColumn('departure_time', 'start_time');
            });
        } elseif (!Schema::hasColumn('schedules', 'start_time')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->time('start_time')->nullable()->after('end_station_id');
            });
        }

        // Step 3: Add end_time and start_time_nextday safely
        Schema::table('schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('schedules', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
            if (!Schema::hasColumn('schedules', 'start_time_nextday')) {
                $table->boolean('start_time_nextday')->default(false)->after('end_time');
            }
        });

        // Step 4: Add foreign keys safely (after column types are correct)
        Schema::table('schedules', function (Blueprint $table) {
            // Only add foreign keys if they don't exist yet
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableForeignKeys('schedules');

            $foreignNames = array_map(fn($fk) => $fk->getName(), $indexes);

            if (Schema::hasColumn('schedules', 'bus_id') && !in_array('schedules_bus_id_foreign', $foreignNames)) {
                $table->foreign('bus_id')->references('id')->on('buses')->onDelete('set null');
            }

            if (Schema::hasColumn('schedules', 'start_station_id') && !in_array('schedules_start_station_id_foreign', $foreignNames)) {
                $table->foreign('start_station_id')->references('id')->on('stations')->onDelete('restrict');
            }

            if (Schema::hasColumn('schedules', 'end_station_id') && !in_array('schedules_end_station_id_foreign', $foreignNames)) {
                $table->foreign('end_station_id')->references('id')->on('stations')->onDelete('restrict');
            }
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            if (Schema::hasColumn('schedules', 'start_station_id')) {
                $table->dropForeign(['start_station_id']);
            }
            if (Schema::hasColumn('schedules', 'end_station_id')) {
                $table->dropForeign(['end_station_id']);
            }
            if (Schema::hasColumn('schedules', 'bus_id')) {
                $table->dropForeign(['bus_id']);
            }

            $columnsToDrop = ['route_tagline', 'bus_id', 'start_station_id', 'end_station_id', 'end_time', 'start_time_nextday'];
            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn('schedules', $col)) {
                    $table->dropColumn($col);
                }
            }

            if (Schema::hasColumn('schedules', 'start_time') && !Schema::hasColumn('schedules', 'departure_time')) {
                $table->renameColumn('start_time', 'departure_time');
            }
        });
    }
};
