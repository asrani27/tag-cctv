<?php

namespace Database\Factories;

use App\Models\SurveyLocation;
use App\Models\SurveyPhoto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SurveyPhoto>
 */
class SurveyPhotoFactory extends Factory
{
    protected $model = SurveyPhoto::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'survey_location_id' => SurveyLocation::factory(),
            'user_id' => User::factory(),
            'file_path' => 'surveys/test/' . fake()->uuid() . '.jpg',
            'original_name' => fake()->word() . '.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => fake()->numberBetween(1024, 5242880),
            'sort_order' => 0,
        ];
    }
}
