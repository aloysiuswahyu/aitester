<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Chat LMS Rapi</title>
  <script>
   async function startChat(e) {
      e.preventDefault();
      document.getElementById("output").textContent = "";
      const prompt = document.getElementById("prompt").value;

      const response = await fetch("/generate-lms", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ prompt })
      });

      const reader = response.body.getReader();
      const decoder = new TextDecoder("utf-8");
      let buffer = "";
      let output = "";

      while (true) {
        const { done, value } = await reader.read();
        if (done) break;
        
        buffer += decoder.decode(value, { stream: true });
        let lines = buffer.split("\n");
        buffer = lines.pop();

        for (const line of lines) {
          if (line.startsWith("data: ")) {
            const jsonStr = line.replace("data: ", "").trim();
            if (jsonStr === "[DONE]") return;
            try {
              const data = JSON.parse(jsonStr);
              const delta = data?.choices?.[0]?.delta?.content;
              if (delta) {
                output += delta;
                document.getElementById("output").textContent = output;
              }
            } catch {}
          }
        }
      }
    }
  </script>
</head>
<body style="font-family: sans-serif; padding:20px;">
  <h2>Chat dengan LM Studio</h2>
  <form onsubmit="startChat(event)">
    <textarea id="prompt" rows="3" style="width:100%; padding:10px;" placeholder="Tulis pertanyaanmu di sini..."></textarea>
    <button type="submit" style="margin-top:10px; padding:8px 16px;">Kirim</button>
  </form>
  <div id="output" style="margin-top:20px; background:#f9f9f9; padding:15px; border-radius:8px; white-space: pre-wrap;"></div>
</body>
</html>