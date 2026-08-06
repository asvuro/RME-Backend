<?php

namespace Modules\SystemTteDocument\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\SystemTteDocument\Models\TteDocument;

class TteDocumentFactory extends Factory
{
    protected $model = TteDocument::class;

    public function definition(): array
    {
        return [
            'ref_type' => 'medical_record_resumes',
            'ref_id' => fake()->numberBetween(1, 999),
            'status' => TteDocument::STATUS_DRAFT,
            'content' => ['title' => fake()->sentence(), 'body' => fake()->paragraph()],
            'document_hash' => null,
            'signed_by' => null,
            'signed_at' => null,
        ];
    }

    public function pendingSign(): static
    {
        return $this->state(fn () => ['status' => TteDocument::STATUS_PENDING_SIGN]);
    }

    public function signed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TteDocument::STATUS_SIGNED,
            'document_hash' => hash('sha256', json_encode($attributes['content'] ?? [])),
            'signed_at' => now(),
        ]);
    }

    public function locked(): static
    {
        return $this->state(fn () => ['status' => TteDocument::STATUS_LOCKED]);
    }
}
