<?php

namespace App\Http\Controllers;

use App\Services\AiAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Shelfwood\LMStudio\LMStudioFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AiController extends Controller
{
    public function index()
    {
        return view('ai');
    }

    public function generate(Request $request, AiAgent $agent)
    {
        try {
            $text = $request->input('text');
        $cacheKey = 'openai_'.md5(json_encode($text));

        // Cek cache selama 1 jam
        // $analysis = Cache::remember($cacheKey, now()->addHour(), function () use ($text) {
        // $client = \OpenAI::client(env('OPENAI_API_KEY'));
        // $response = $client->chat()->create([
        //     'model' => 'gpt-4o-mini',
        //     'messages' => [
        //         // ['role' => 'system', 'content' => 'jika yang di input tidak ada hubungan yang menanyakan tentang surveyor  maka output nya bahwa inputan anda tidak dapat di deteksi'],
        //         // ['role' => 'assistant',    'content' => 'yang menjawab Siva'],
        //         // ['role' => 'developer', 'content' => 'otomatis translate ke bahasa inggris'],
        //         ['role' => 'system', 'content' => 'Kamu adalah AI Agent Katalis'],
        //         // ['role' => 'system', 'content' => 'You are Aria, a smart and polite AI assistant. Always respond in English, even if the question is in another language.'],
        //         ['role' => 'user', 'content' => $text],
        //     ],
        //     'max_tokens' => 200,
        // ]);
        // // dd($response);
        // $response->choices[0]->message->content;

        // $analysis = $response->choices[0]->message->content ?? ' tidak dapat di deteksi';

        // });

        // dd($analysis);
        // return response()->json($response->json());
        // dd($data);

        $query = DB::table('ai_training_data')->select('*')->limit(2)->get();

        $context = "";

        foreach ($query as $data) {
            $context .= "Title: " . $data->title . "\nDescription: " . $data->description . "\n\n";
        }

        $analysis = $agent->askWithData($text, $context);

        return view('ai', compact('text', 'analysis'));
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function generatelms(Request $request)
    {
        $ch = curl_init('http://127.0.0.1:1234/v1/chat/completions');
        $prompt = $request->input('text');
        // dd($prompt);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'model' => 'gemma-2b-it', // contoh model ringan
            'messages' => [
                ['role' => 'system', 'content' => "Kamu adalah asisten yang bertugas mendeteksi komentar bullying atau ketidaksukaan terhadap 'atlet'. 
                Jika input termasuk bullying / tidak suka terhadap atlet, ubah kalimat bullying menjadi lebih sopan dimana hasil nya sebgai Kami Badminton Lovers. 
                Jika tidak ada indikasi, output hanya dengan: 'Inputan anda tidak dapat di deteksi'."],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 200,
            'temperature' => 0.2,
            'stream' => false, // 🔴 ini kunci, matikan streaming
        ]));

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        $output = $data['choices'][0]['message']['content'] ?? 'Error';

        return view('lms', compact('output', 'prompt'));
    }

    public function generatelmsstream(Request $request)
    {
        $url = 'http://127.0.0.1:1234/v1/chat/completions';
        $text = $request->input('prompt');

        return new StreamedResponse(function () use ($url, $text) {
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'model' => 'gemma-3-4b-it-qat', // model sesuai LM Studio
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "Kamu adalah asisten yang menjelaskan yang berkaitan dengan karantina pertaninan.
                        Jika tidak ada berkaitan dengan karantina pertaninan, jawab hanya dengan: 'Inputan anda tidak dapat di deteksi'.",
                    ],
                    ['role' => 'user', 'content' => $text],
                ],
                'stream' => true,
                'max_tokens' => 150, // batasi agar cepat
                'temperature' => 0.2, // lebih deterministik
                // 'max_tokens' => 200,
            ]));
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) {
                echo $data;
                @ob_flush();
                flush();

                return strlen($data);
            });

            curl_exec($ch);
            curl_close($ch);
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function generatelmsaltel(Request $request)
    {
        $url = 'http://127.0.0.1:1234/v1/chat/completions';
        $text = $request->input('prompt');

        return new StreamedResponse(function () use ($url, $text) {
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'model' => 'gemma-3-4b-it-qat', // model sesuai LM Studio
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "Kamu adalah asisten yang bertugas mendeteksi komentar bullying atau ketidaksukaan terhadap 'atlet'. 
                        Jika input termasuk bullying / tidak suka terhadap atlet, ubah kalimat bullying menjadi lebih sopan dimana hasil nya sebgai Kami Badminton Lovers. 
                        Jika tidak ada indikasi, jawab hanya dengan: 'Inputan anda tidak dapat di deteksi'.",
                    ],
                    ['role' => 'user', 'content' => $text],
                ],
                'stream' => true,
                // 'max_tokens' => 200,
            ]));
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) {
                echo $data;
                @ob_flush();
                flush();

                return strlen($data);
            });

            curl_exec($ch);
            curl_close($ch);
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function generatelms2(Request $request)
    {
        $text = $request->input('text');
        $cacheKey = 'openai_'.md5(json_encode($text));

        // inisialisasi client
        $factory = new LMStudioFactory([
            'base_uri' => env('LMSTUDIO_API_URL').'/v1',
        ]);

        $conversation = $factory->createStreamingConversation('qwen/qwen3-8b');

        $conversation->addSystemMessage(
            "Anda adalah asisten pintar. Gunakan pengetahuan berikut sebagai referensi:\n\n hanya yang berkaitan dengan Katalis dan prospera  respon nya di awalai dengan hello saya iaskills bantu menjawab diluar dari  Katalis dan prospera  respon nya bahwa inputan anda tidak dapat di deteksi"
        );
        $conversation->addUserMessage($text);

        // 3. Return streaming response ke client
        $response = new StreamedResponse(function () use ($conversation) {
            $handler = $conversation->streamingHandler;

            if ($handler) {
                $handler->on('stream_content', fn (string $content) => print ($content));
                $handler->on('stream_end', fn () => print ("\n"));
            }

            // jalankan streaming
            $conversation->handleStreamingTurn();
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('X-Accel-Buffering', 'no');
        dd($response);

        return $response;
    }

    public function generate2(Request $request)
    {
        $text = $request->input('text');
        $cacheKey = 'openai_'.md5(json_encode($text));

        // Cek cache selama 1 jam
        $analysis = Cache::remember($cacheKey, now()->addHour(), function () use ($text) {
            $client = \OpenAI::client(env('OPENAI_API_KEY'));
            $response = $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'jika yang di input bukan kalimat bullying / tidak suka terhadap atlet maka output nya bahwa inputan anda tidak dapat di deteksi'],
                    ['role' => 'system', 'content' => 'jika yang di input ada unsur pertanyaan maka output nya bahwa inputan anda tidak dapat di deteksi'],
                    ['role' => 'user', 'content' => $text.', rapikan penulisan ini lebih sopan dimana hasil nya sebgai Kami Badminton Lovers'],
                ],
            ]);
            // dd($response);
            $response->choices[0]->message->content;

            return $response->choices[0]->message->content ?? ' tidak dapat di deteksi';
        });

        // dd($analysis);
        // return response()->json($response->json());
        // dd($data);

        return view('ai', compact('text', 'analysis'));
    }

    public function lms()
    {
        return view('lms');
    }
}
