<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Deteksi Bullying - LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-lg p-6 bg-white rounded-xl shadow-lg">
        <h1 class="text-2xl font-bold mb-4 text-center">Deteksi Bullying terhadap Atlet</h1>

        <form action="{{ route('ai.actionlms') }}" method="POST" class="space-y-4">
            @csrf
            <textarea 
                name="text" 
                rows="4" 
                class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:ring-blue-300"
                placeholder="Masukkan teks di sini...">{{ $prompt ?? '' }}</textarea>

            <button 
                type="submit" 
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                Analisis
            </button>
        </form>

        @isset($output)
            <div class="mt-6 p-4 bg-gray-50 border rounded-lg">
                <h2 class="font-semibold mb-2">Hasil Analisis:</h2>
                <p>{{ $output }}</p>
            </div>
        @endisset
    </div>
</body>
</html>