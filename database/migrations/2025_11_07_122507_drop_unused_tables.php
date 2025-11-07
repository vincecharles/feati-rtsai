<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('event_attendees');
        Schema::dropIfExists('events');
        Schema::dropIfExists('application_documents');
        Schema::dropIfExists('applications');
        
        Schema::dropIfExists('lc_countries_coordinates');
        Schema::dropIfExists('lc_countries_extras');
        Schema::dropIfExists('lc_countries_geographical');
        Schema::dropIfExists('lc_countries_translations');
        Schema::dropIfExists('lc_countries');
        Schema::dropIfExists('lc_region_translations');
        Schema::dropIfExists('lc_regions');
    }

    public function down(): void
    {
    }
};
