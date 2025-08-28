<!DOCTYPE html>
<html>
<head>
    <title>Analisis Sentimen Teks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">🔍activity anti-bullying</h2>

    <form action="{{ route('ai.action') }}" method="POST">
        @csrf
        <div class="mb-3">
            <textarea name="text" class="form-control" rows="4" placeholder="Masukkan teks di sini...">{{ old('text', $text ?? '') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Translate</button>
    </form>

    @if(isset($analysis))
        <div class="alert alert-info mt-4">
            <h5>Hasil Analisis:</h5>
            <pre>{{ $analysis }}</pre>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mt-3">
            {{ $errors->first() }}
        </div>
    @endif
</div>
</body>
</html>