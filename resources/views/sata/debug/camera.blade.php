<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SATA-QR: Diagnóstico de Cámara</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 20px; background: #111; color: white; }
        .box { border: 2px solid #333; padding: 20px; border-radius: 10px; margin: 10px; }
        #log { font-family: monospace; text-align: left; background: #000; color: #0f0; padding: 15px; height: 200px; overflow-y: auto; }
        .secure-ok { color: #2ecc71; }
        .secure-err { color: #e74c3c; font-weight: bold; }
        button { padding: 15px; font-size: 16px; background: #3498db; color: white; border: none; cursor: pointer; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>SATA-QR: PANEL DE DIAGNÓSTICO</h1>
    
    <div class="box">
        <h3>1. Verificación de Seguridad</h3>
        <p id="secure-status">Detectando...</p>
    </div>

    <div class="box">
        <h3>2. Verificación de Dispositivos</h3>
        <button onclick="detectar()">DETECTAR CÁMARAS</button>
        <div id="log">> Esperando interacción...</div>
    </div>

    <div class="box">
        <video id="v" style="width: 300px; background: #000; display: none;" autoplay playsinline></video>
    </div>

    <script>
        const logBox = document.getElementById('log');
        const secureStatus = document.getElementById('secure-status');

        function print(msg) { logBox.innerHTML += `<br>> ${msg}`; logBox.scrollTop = logBox.scrollHeight; }

        // CHEQUEO DE SEGURIDAD
        if (window.isSecureContext) {
            secureStatus.innerHTML = "✅ ENTORNO SEGURO DETECTADO (HTTPS o Localhost)";
            secureStatus.className = "secure-ok";
        } else {
            secureStatus.innerHTML = "❌ ENTORNO NO SEGURO. EL NAVEGADOR BLOQUEARÁ LA CÁMARA.";
            secureStatus.className = "secure-err";
        }

        async function detectar() {
            print("Solicitando permisos al navegador...");
            
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                print("FALLO: navigator.mediaDevices no existe en este navegador.");
                return;
            }

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                print("✅ PERMISO CONCEDIDO.");
                
                const devices = await navigator.mediaDevices.enumerateDevices();
                const cameras = devices.filter(d => d.kind === 'videoinput');
                print(`Cámaras detectadas: ${cameras.length}`);
                
                cameras.forEach(c => print(`- ${c.label || 'Cámara sin nombre'}`));

                const v = document.getElementById('v');
                v.style.display = "inline-block";
                v.srcObject = stream;
                print("✅ VIDEO INICIADO.");

            } catch (err) {
                print(`❌ ERROR: ${err.name} - ${err.message}`);
                console.error(err);
            }
        }
    </script>
</body>
</html>
