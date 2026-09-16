<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('umrah_packages', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
        });

        // Generate slugs for existing records
        DB::table('umrah_packages')->orderBy('id')->each(function (object $pkg) {
            $base = Str::slug($pkg->title);
            $slug = $base;
            $i = 1;

            while (DB::table('umrah_packages')->where('slug', $slug)->where('id', '!=', $pkg->id)->exists()) {
                $slug = "{$base}-{$i}";
                $i++;
            }

            DB::table('umrah_packages')->where('id', $pkg->id)->update(['slug' => $slug]);
        });

        Schema::table('umrah_packages', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('umrah_packages', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
