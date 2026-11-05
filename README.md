# 🧠 Quiz de Desenvolvimento de Sistemas

Aplicação web de quizzes sobre desenvolvimento de sistemas, com três níveis de dificuldade, pontuação em tempo real e **ranking geral** salvo em MySQL.

O jogador escolhe um quiz, responde 5 perguntas de múltipla escolha, recebe feedback a cada resposta e, no final, salva a pontuação com um nick para entrar no ranking.

---

## ✨ Funcionalidades

- 3 quizzes (Básico, Intermediário e Avançado) com 5 perguntas cada
- Feedback imediato de acerto/erro, mostrando a resposta correta
- Placar atualizado a cada pergunta
- Salvamento da pontuação por **nick** (sem necessidade de login)
- Ranking geral com pontuação por quiz e total
- Página de ranking dedicada, com atualização automática a cada 10 segundos
- Para cada jogador e quiz, o banco guarda **apenas a melhor pontuação**
- Validações no back-end (tamanho e caracteres do nick, quiz permitido, pontuação entre 0 e 5)
- Layout responsivo

---

## 🛠️ Tecnologias

| Camada | Tecnologia |
|---|---|
| Front-end | HTML5, CSS3 e JavaScript puro |
| Back-end | PHP 8+ (PDO) |
| Banco de dados | MySQL / MariaDB |
| Servidor local | Apache (XAMPP, Laragon ou similar) |

---

## 📋 Requisitos

- PHP 8 ou superior com a extensão `pdo_mysql` habilitada
- MySQL ou MariaDB
- Apache (XAMPP, Laragon ou equivalente)

---

## 🚀 Como rodar

### 1. Crie o banco de dados

Execute o arquivo `schema.sql` no MySQL (pelo phpMyAdmin ou pelo terminal):

```bash
mysql -u root -p < schema.sql
```

Isso cria o banco `quiz_db` com as tabelas `jogadores` e `pontuacoes`.

### 2. Configure a conexão

Abra o `conexao.php` e ajuste os dados do seu MySQL local:

```php
$host    = "localhost";
$banco   = "quiz_db";
$usuario = "root";
$senha   = "sua_senha_aqui";
```

> ⚠️ Não envie sua senha real para o GitHub. Se for publicar o projeto, deixe um valor de exemplo no repositório.

### 3. Coloque o projeto no Apache

Copie a pasta do projeto para o `htdocs` (XAMPP) ou `www` (Laragon) **com o nome `clone`**:

```
htdocs/
└── clone/
    ├── index.html
    ├── quiz.html
    ├── ...
```

> O nome da pasta importa: o `quiz.js` envia a pontuação para `/clone/salvar_pontuacao.php`. Se quiser usar outro nome de pasta, altere essa URL no `quiz.js` (função `finalizarQuiz`).

### 4. Acesse no navegador

```
http://localhost/clone/index.html
```

---

## 📁 Estrutura do projeto

```
├── index.html            # Tela inicial: escolha do quiz
├── index.css             # Estilos da tela inicial
├── index.js              # Redireciona para quiz.html?tipo=...
├── quiz.html             # Tela do quiz (usada pelos 3 níveis)
├── quiz.css              # Estilos do quiz
├── quiz.js               # Banco de perguntas, lógica do quiz e chamadas à API
├── pagina_Ranking.php    # Página HTML do ranking (renderizada em PHP)
├── ranking.php           # Endpoint JSON do ranking (usado no modal do quiz)
├── salvar_pontuacao.php  # Endpoint que grava jogador e pontuação
├── conexao.php           # Conexão PDO com o MySQL
└── schema.sql            # Criação do banco e das tabelas
```

---

## 🔄 Como funciona

1. Em `index.html`, cada botão leva para `quiz.html?tipo=java|bd|dw`.
2. O `quiz.js` converte o parâmetro para o nível do quiz:

   | Parâmetro `tipo` | Quiz |
   |---|---|
   | `java` | `basico` |
   | `bd` | `intermediario` |
   | `dw` | `avancado` |

3. Ao terminar as 5 perguntas, o jogador informa um nick e o front-end faz um `POST` em `salvar_pontuacao.php`.
4. O PHP valida os dados, cria o jogador (se não existir) e grava a pontuação, mantendo a maior já registrada.
5. O quiz busca `ranking.php` e exibe a tabela de classificação no próprio modal.

---

## 🗄️ Banco de dados

**`jogadores`**

| Coluna | Tipo | Observação |
|---|---|---|
| `id` | INT | PK, auto incremento |
| `nick` | VARCHAR(20) | Único |
| `criado_em` | TIMESTAMP | Data de criação |

**`pontuacoes`**

| Coluna | Tipo | Observação |
|---|---|---|
| `id` | INT | PK, auto incremento |
| `jogador_id` | INT | FK para `jogadores` (`ON DELETE CASCADE`) |
| `quiz` | ENUM | `basico`, `intermediario`, `avancado` |
| `pontos` | INT | Pontuação do quiz |
| `atualizado_em` | TIMESTAMP | Atualizado automaticamente |

Existe uma chave única `(jogador_id, quiz)`: cada jogador tem **uma linha por quiz**, e o `salvar_pontuacao.php` usa `GREATEST(pontos, VALUES(pontos))` para manter só a melhor nota.

---

## 🔌 Endpoints

### `POST /salvar_pontuacao.php`

Corpo (JSON):

```json
{ "nick": "Kryzn", "quiz": "basico", "pontos": 4 }
```

Resposta de sucesso:

```json
{ "sucesso": true, "mensagem": "Pontuação salva com sucesso!" }
```

Regras: nick de 3 a 20 caracteres (letras, números, espaço, `_` e `-`), quiz entre `basico`, `intermediario` e `avancado`, e pontos de 0 a 5.

### `GET /ranking.php`

Resposta:

```json
{
  "sucesso": true,
  "ranking": [
    { "nick": "Kryzn", "basico": "4", "intermediario": "5", "avancado": "3", "total": "12" }
  ]
}
```

---

## 🗂️ Arquivos legados (não usados pelo quiz atual)

Estes arquivos fazem parte de versões anteriores do projeto e **não são chamados** pelo fluxo atual:

- **`api.php`**: API antiga com login, cadastro e pontuação por usuário. Depende de uma tabela `usuarios` que não existe no `schema.sql` atual, e de uma coluna `usuario_id` em `pontuacoes` (hoje a coluna é `jogador_id`).
- **`conexao.js`, `pontuacaoDao.js` e `package.json`**: tentativa de backend em Node/Express com `mysql2`. Estão incompletos (não há `server.js`), apontam para o banco `quiz_app` e o `pontuacaoDao.js` importa `./db`, que não existe (o arquivo se chama `conexao.js`).

Se não for usá-los, pode removê-los do repositório para evitar confusão.

---

## 🔒 Observações de segurança

- Todas as consultas usam *prepared statements* (PDO), e os nicks são escapados com `htmlspecialchars` na página de ranking.
- O `salvar_pontuacao.php` devolve a mensagem de erro do PHP na resposta (útil em desenvolvimento). Em produção, troque por uma mensagem genérica.
- Não há autenticação: qualquer pessoa pode salvar uma pontuação com qualquer nick. A pontuação é enviada pelo navegador, então ela pode ser manipulada.

---

## 👨‍💻 Autor

Desenvolvido por [Kryzn](https://github.com/Schutz-br).
