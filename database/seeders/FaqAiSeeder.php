<?php

namespace Database\Seeders;

use App\Models\FaqEmbed;
use Illuminate\Database\Seeder;

class FaqAiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $client = \OpenAI::client(env('OPENAI_API_KEY'));
        $faqs = [
            [
                'question' => 'apa audisi umum dikenakan biaya?',
                'answer' => 'Pendaftaran audisi umum TIDAK DIKENAKAN BIAYA apa pun...',
            ],
            [
                'question' => 'registrasi ulang',
                'answer' => 'registrasi ulang secara OFFLINE dilakukan pada 9 September 2024...',
            ],
            // tambahkan lainnya...
        ];

        foreach ($faqs as $faq) {
            $res = $client->embeddings()->create([
                'model' => 'text-embedding-3-small',
                'input' => $faq['question'],
            ]);

            FaqEmbed::create([
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'embedding' => json_encode($res->embeddings[0]['embedding']),
            ]);
        }
    }
}
