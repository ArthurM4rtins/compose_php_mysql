<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testador de API RESTful</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9; }
        h1 { color: #333; }
        .card { background: white; border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; border-radius: 8px; box-shadow: 2px 2px 5px rgba(0,0,0,0.1);}
        input, button { padding: 8px; margin: 5px 0; border: 1px solid #aaa; border-radius: 4px;}
        button { background-color: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .terminal { background: #1e1e1e; color: #00ff00; padding: 15px; border-radius: 5px; font-family: monospace; overflow-x: auto; white-space: pre-wrap;}
    </style>
</head>
<body>

    <h1>Painel de Teste - API de Usuários</h1>

    <div class="card">
        <h3>📥 1. GET (Listar Usuários)</h3>
        <button onclick="getUsers()">Buscar Todos os Usuários</button>
    </div>

    <div class="card">
        <h3>➕ 2. POST (Criar Usuário)</h3>
        <input type="text" id="nomePost" placeholder="Nome do usuário">
        <input type="email" id="emailPost" placeholder="Email do usuário">
        <button onclick="createUser()">Criar</button>
    </div>

    <div class="card">
        <h3>✏️ 3. PUT (Atualizar Usuário)</h3>
        <input type="number" id="idPut" placeholder="ID (ex: 1)">
        <input type="text" id="nomePut" placeholder="Novo Nome">
        <input type="email" id="emailPut" placeholder="Novo Email">
        <button onclick="updateUser()">Atualizar</button>
    </div>

    <div class="card">
        <h3>❌ 4. DELETE (Deletar Usuário)</h3>
        <input type="number" id="idDelete" placeholder="ID (ex: 1)">
        <button style="background-color: #dc3545;" onclick="deleteUser()">Deletar</button>
    </div>

    <h2>Resposta da API:</h2>
    <div class="terminal" id="output">Faça uma requisição para ver o resultado aqui...</div>

    <script>
        // Ajuste a URL caso sua API esteja em uma pasta específica
        const apiUrl = 'restfull_api.php/users'; 

        // Mostra o JSON na tela
        function renderOutput(data) {
            document.getElementById('output').textContent = JSON.stringify(data, null, 4);
        }

        // Método GET
        async function getUsers() {
            try {
                const res = await fetch(apiUrl);
                const data = await res.json();
                renderOutput(data);
            } catch (error) { renderOutput({error: "Falha na comunicação."}); }
        }

        // Método POST
        async function createUser() {
            const nome = document.getElementById('nomePost').value;
            const email = document.getElementById('emailPost').value;
            try {
                const res = await fetch(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nome, email })
                });
                const data = await res.json();
                renderOutput(data);
            } catch (error) { renderOutput({error: "Falha na comunicação."}); }
        }

        // Método PUT
        async function updateUser() {
            const id = document.getElementById('idPut').value;
            const nome = document.getElementById('nomePut').value;
            const email = document.getElementById('emailPut').value;
            if(!id) return alert("Digite o ID do usuário para atualizar.");
            
            try {
                const res = await fetch(`${apiUrl}/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nome, email })
                });
                const data = await res.json();
                renderOutput(data);
            } catch (error) { renderOutput({error: "Falha na comunicação."}); }
        }

        // Método DELETE
        async function deleteUser() {
            const id = document.getElementById('idDelete').value;
            if(!id) return alert("Digite o ID do usuário para deletar.");

            try {
                const res = await fetch(`${apiUrl}/${id}`, {
                    method: 'DELETE'
                });
                const data = await res.json();
                renderOutput(data);
            } catch (error) { renderOutput({error: "Falha na comunicação."}); }
        }
    </script>
</body>
</html>