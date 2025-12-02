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
        Schema::create('body_site_markings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('body_structure_id')->constrained('body_structures')->onDelete('cascade');
            $table->foreignId('skin_lesion_id')->nullable()->constrained('skin_lesions')->onDelete('cascade');

            // Visual positioning on body diagram
            $table->enum('body_view', ['anterior', 'posterior', 'left_lateral', 'right_lateral'])->comment('Body diagram view');
            $table->decimal('position_x', 8, 4)->comment('X coordinate as percentage (0-100)');
            $table->decimal('position_y', 8, 4)->comment('Y coordinate as percentage (0-100)');

            // Visual representation
            $table->string('marker_color')->default('#FF0000')->comment('Hex color for marker');
            $table->enum('marker_shape', ['circle', 'square', 'triangle', 'star'])->default('circle');
            $table->integer('marker_size')->default(10)->comment('Size in pixels');
            $table->string('label')->nullable()->comment('Optional label for the marking');

            // Metadata
            $table->text('notes')->nullable();
            $table->boolean('is_visible')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('body_structure_id');
            $table->index(['body_view', 'is_visible']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('body_site_markings');
    }
};
