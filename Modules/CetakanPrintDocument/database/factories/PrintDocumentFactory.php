<?php

namespace Modules\CetakanPrintDocument\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Auth\Models\User;
use Modules\CetakanPrintDocument\Models\PrintDocument;

class PrintDocumentFactory extends Factory
{
    protected $model = PrintDocument::class;

    public function definition(): array
    {
        $type = fake()->randomElement(PrintDocument::TYPES);

        return [
            'document_type' => $type,
            'ref_type' => 'registrations',
            'ref_id' => fake()->numberBetween(1, 999),
            'document_number' => PrintDocument::PREFIXES[$type].'-'.fake()->unique()->numerify('######'),
            'payload' => null,
            'issued_by' => User::factory(),
            'issued_at' => now(),
        ];
    }
}
