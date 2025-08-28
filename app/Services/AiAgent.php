<?php

namespace App\Services;

// use OpenAI\Laravel\Facades\OpenAI;

class AiAgent
{
    public function askWithData(string $question, string $context = ""): string
    {
        $client = \OpenAI::client(env('OPENAI_API_KEY'));
        $result = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => "Kamu adalah AI Agent Katalis. Jawab pertanyaan berdasarkan data berikut:\n\n" . $context],
                ['role' => 'user', 'content' => $question],
            ],
        ]);

        return $result->choices[0]->message->content ?? 'Maaf, saya tidak menemukan jawabannya.';
    }
}
