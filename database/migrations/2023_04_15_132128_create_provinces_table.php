<?php

use App\Models\ProvinceModel;
use App\Traits\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use MigrationTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create($this->getTable(new ProvinceModel()), function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $this->timestamps($table);
            $this->softDeletes($table);
        });

        foreach (
            [
                "Bali",
                "Bangka Belitung",
                "Banten",
                "Bengkulu",
                "DI Yogyakarta",
                "DKI Jakarta",
                "Gorontalo",
                "Jambi",
                "Jawa Barat",
                "Jawa Tengah",
                "Jawa Timur",
                "Kalimantan Barat",
                "Kalimantan Selatan",
                "Kalimantan Tengah",
                "Kalimantan Timur",
                "Kalimantan Utara",
                "Kepulauan Riau",
                "Lampung",
                "Maluku",
                "Maluku Utara",
                "Nanggroe Aceh Darussalam (NAD)",
                "Nusa Tenggara Barat (NTB)",
                "Nusa Tenggara Timur (NTT)",
                "Papua",
                "Papua Barat",
                "Papua Barat Daya",
                "Papua Pegunungan",
                "Papua Selatan",
                "Papua Tengah",
                "Riau",
                "Sulawesi Barat",
                "Sulawesi Selatan",
                "Sulawesi Tengah",
                "Sulawesi Tenggara",
                "Sulawesi Utara",
                "Sumatera Barat",
                "Sumatera Selatan",
                "Sumatera Utara"
            ] as $data
        ) {
            ProvinceModel::create([
                "name" => $data
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists($this->getTable(new ProvinceModel()));
    }
};
