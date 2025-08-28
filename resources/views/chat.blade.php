<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat LMS</title>
    <script>
        async function startChat() {
            const response = await fetch("/generate-lms");
            const reader = response.body.getReader();
            const decoder = new TextDecoder("utf-8");
            let text = "";

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;
                text += decoder.decode(value, { stream: true });
                document.getElementById("output").textContent = text;
            }
        }
    </script>
</head>
<body style="font-family: sans-serif; padding:20px;">
    <h2>Chat dengan LM Studio</h2>
    <button onclick="startChat()">Mulai Tanya</button>
    <pre id="output" style="margin-top:20px; background:#f1f1f1; padding:10px; border-radius:8px; white-space: pre-wrap;"></pre>
</body>
</html>