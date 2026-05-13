<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGII | Módulo de Acceso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white text-center py-3">
                        <h4 class="mb-0">SGII - Autenticación</h4>
                    </div>
                    <div class="card-body p-4">
                        <form id="formularioLogin">
                            <div class="mb-3">
                                <label for="correo" class="form-label fw-bold">Correo Electrónico</label>
                                <input type="email" class="form-control" id="correo" placeholder="Ej: admin@elsol.com" required>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold">Clave de Seguridad</label>
                                <input type="password" class="form-control" id="password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Ingresar al Sistema</button>
                            </div>
                            <div class="mt-3 text-center">
                            <a href="register.php" class="text-decoration-none">¿No tiene cuenta? Solicitar Alta de Operador</a>
                            </div>
                        </form>
                        
                        <div id="panelAlertas" class="mt-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 1. Escucha del evento 'submit' para interceptar el envío del formulario
        document.getElementById('formularioLogin').addEventListener('submit', async function(evento) {
            
            // Prevención del comportamiento por defecto (evita la recarga síncrona de la página)
            evento.preventDefault(); 

            // 2. Extracción de los valores del Document Object Model (DOM)
            const inputCorreo = document.getElementById('correo').value;
            const inputPassword = document.getElementById('password').value;
            const panelAlertas = document.getElementById('panelAlertas');

            // 3. Estructuración del Payload (Objeto de datos a transmitir)
            const payload = {
                action: 'login',
                correo: inputCorreo,
                password: inputPassword
            };

            try {
                // 4. Apertura del túnel HTTP asíncrono hacia nuestro endpoint PHP
                const respuestaServidor = await fetch('../servidor/UsuariosAPI.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    // Serialización del objeto JavaScript a una cadena JSON estandarizada
                    body: JSON.stringify(payload) 
                });

                // 5. Resolución de la promesa y parseo del buffer entrante a formato JSON
                const datos = await respuestaServidor.json();

                // 6. Lógica de renderizado condicional basado en la bandera 'status' del backend
                if (datos.status === 'success') {
                    // Renderizado de estado óptimo (HTTP 200 OK Lógico)
                    panelAlertas.innerHTML = `
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <div>
                                <strong>¡Acesso Concedido!</strong><br>
                                ${datos.message}.<br>
                                Operador: ${datos.usuario}
                            </div>
                        </div>`;
                } else {
                    // Renderizado de excepción controlada
                    panelAlertas.innerHTML = `
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <div>
                                <strong>Fallo de Autenticación:</strong> ${datos.message}
                            </div>
                        </div>`;
                }

            } catch (errorCritico) {
                // Captura de fallos de infraestructura (ej. servidor caído, error 500)
                console.error('Anomalía detectada en el flujo de red:', errorCritico);
                panelAlertas.innerHTML = `
                    <div class="alert alert-warning" role="alert">
                        Error crítico de comunicación con el servicio web. Verifique la consola.
                    </div>`;
            }
        });
    </script>
</body>
</html>
