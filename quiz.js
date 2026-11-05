
// Identifica o tipo de quiz pela URL
const tipoURL = new URLSearchParams(location.search).get("tipo") || "dw";

const mapaTipos = {
  java: "basico",
  bd: "intermediario",
  dw: "avancado"
};

const tipo = mapaTipos[tipoURL] || tipoURL;

// Elementos HTML
const titulo = document.getElementById("titulo");
const placar = document.getElementById("placar");
const cabecalho = document.getElementById("cabecalho");

const modal = document.getElementById("modal");
const resultado = document.getElementById("resultado");

const modalFeedback = document.getElementById("modalFeedback");
const feedbackTitulo = document.getElementById("feedbackTitulo");
const feedbackTexto = document.getElementById("feedbackTexto");
const btnContinuar = document.getElementById("btnContinuar");

const botoes = [1, 2, 3, 4].map(n =>
  document.getElementById("r" + n)
);

let perguntaAtual = 0;
let pontos = 0;
let finalizado = false;
let respondida = false;

// Banco de perguntas
const BANCOS = {

  basico: {
    nome: "Quiz Básico",
    perguntas: [
      {
        enunciado: "Como a Inteligência Artificial pode ajudar um desenvolvedor de sistemas?",
        opcoes: [
          "Aumentando a produtividade e auxiliando na criação de códigos.",
          "Substituindo completamente todos os desenvolvedores.",
          "Impedindo a criação de programas.",
          "Eliminando a necessidade de aprender programação."
        ],
        correta: 0
      },
      {
        enunciado: "Qual é a diferença entre front-end e back-end?",
        opcoes: [
          "Os dois são responsáveis somente pelo banco de dados.",
          "O front-end gerencia o servidor e o back-end cuida das cores.",
          "O front-end cria o banco de dados e o back-end cria imagens.",
          "O front-end cuida da interface e o back-end da lógica e funcionalidades do sistema."
        ],
        correta: 3
      },
      {
        enunciado: "Qual é a função do HTML e do CSS?",
        opcoes: [
          "HTML cria bancos de dados e CSS programa servidores.",
          "HTML e CSS são utilizados exclusivamente para programação Java.",
          "HTML estrutura o conteúdo e CSS define a aparência das páginas.",
          "HTML e CSS substituem todas as linguagens de programação."
        ],
        correta: 2
      },
      {
        enunciado: "Qual é o principal objetivo de um desenvolvedor de sistemas?",
        opcoes: [
          "Criar soluções tecnológicas para resolver problemas e atender necessidades.",
          "Trabalhar exclusivamente com publicidade.",
          "Gerenciar somente as finanças de uma empresa.",
          "Substituir todos os profissionais de TI."
        ],
        correta: 0
      },
      {
        enunciado: "Qual é a saída do código Java? for (int i = 0; i < 3; i++) { System.out.print(i + \" \"); }",
        opcoes: [
          "0 1 2",
          "1 2 3",
          "0 1 2 3",
          "Erro de compilação"
        ],
        correta: 0
      }
    ]
  },

  intermediario: {
    nome: "Quiz Intermediário",
    perguntas: [
      {
        enunciado: "Em JavaScript, qual método adiciona um elemento ao final de um array?",
        opcoes: ["pop()", "push()", "shift()", "remove()"],
        correta: 1
      },
      {
        enunciado: "Qual comando SQL atualiza registros existentes?",
        opcoes: ["INSERT", "SELECT", "UPDATE", "CREATE"],
        correta: 2
      },
      {
        enunciado: "Em HTML, qual atributo torna um campo obrigatório?",
        opcoes: ["required", "placeholder", "readonly", "disabled"],
        correta: 0
      },
      {
        enunciado: "Qual propriedade CSS organiza elementos em um layout flexível?",
        opcoes: ["position", "display: flex", "font-size", "text-align"],
        correta: 1
      },
      {
        enunciado: "Qual método transforma um objeto em uma string JSON?",
        opcoes: ["JSON.parse()", "JSON.convert()", "JSON.stringify()", "JSON.object()"],
        correta: 2
      }
    ]
  },

  avancado: {
    nome: "Quiz Avançado",
    perguntas: [
      {
        enunciado: "Qual é a função de uma API?",
        opcoes: [
          "Criar imagens automaticamente",
          "Substituir o banco de dados",
          "Formatar páginas HTML",
          "Permitir a comunicação entre sistemas e aplicações"
        ],
        correta: 3
      },
      {
        enunciado: "Qual é a principal finalidade de um índice (INDEX) no banco de dados?",
        opcoes: [
          "Excluir registros duplicados automaticamente.",
          "Ajudar a acelerar determinadas consultas.",
          "Substituir todas as chaves primárias.",
          "Impedir qualquer alteração na tabela."
        ],
        correta: 1
      },
      {
        enunciado: "O que significa atomicidade em uma transação?",
        opcoes: [
          "As operações podem ser executadas parcialmente.",
          "Os dados são sempre armazenados em arquivos separados.",
          "A transação acontece por completo ou não acontece.",
          "A tabela não pode receber novos registros."
        ],
        correta: 2
      },
      {
        enunciado: "O que é uma transação em um banco de dados?",
        opcoes: [
          "Uma alteração no CSS",
          "Um conjunto de operações tratado como uma unidade",
          "Uma consulta que não retorna resultados",
          "Uma tabela temporária"
        ],
        correta: 1
      },
      {
        enunciado: "Qual é a principal função de uma API REST?",
        opcoes: [
          "Criar automaticamente o design de páginas.",
          "Substituir o sistema operacional.",
          "Permitir a comunicação entre aplicações por requisições HTTP.",
          "Impedir a troca de informações entre sistemas."
        ],
        correta: 2
      }
    ]
  }
};

// Seleciona o quiz
const banco = BANCOS[tipo];

// Verifica os elementos e o banco
if (!banco) {
  alert("Tipo de quiz inválido!");
  location.href = "index.html";

} else if (
  !titulo || !placar || !modal || !resultado ||
  !modalFeedback || !feedbackTitulo || !feedbackTexto ||
  !btnContinuar || botoes.some(botao => !botao)
) {
  console.error("Erro: elementos HTML não encontrados.");
  alert("Verifique os elementos do quiz.html.");

} else {

  // Cabeçalho
  cabecalho.textContent = banco.nome;

  // Botão voltar
  let btnVoltar = document.getElementById("btnVoltar");

  if (!btnVoltar) {
    btnVoltar = document.createElement("button");
    btnVoltar.id = "btnVoltar";
    btnVoltar.type = "button";
    btnVoltar.textContent = "Voltar ao menu";
    cabecalho.parentElement.insertBefore(btnVoltar, cabecalho);
  }

  btnVoltar.onclick = () => {
    location.href = "index.html";
  };

  // Botões do resultado
  const caixaModal = modal.querySelector(".modal-box") || modal;

  function criarBotao(id, texto) {
    let botao = document.getElementById(id);

    if (!botao) {
      botao = document.createElement("button");
      botao.id = id;
      botao.type = "button";
      botao.textContent = texto;
      caixaModal.appendChild(botao);
    }

    return botao;
  }

  const btnMenu = criarBotao("btnMenu", "Voltar ao menu");
  const btnFechar = criarBotao("btnFechar", "Fechar quiz");

  btnMenu.onclick = () => {
    location.href = "index.html";
  };

  btnFechar.onclick = () => {
    window.close();
    location.href = "index.html";
  };

  // Placar
  function atualizarPlacar() {
    placar.textContent = `${pontos} pontos`;
  }

  // Mostrar pergunta
  function mostrarPergunta() {

    if (perguntaAtual >= banco.perguntas.length) {
      finalizarQuiz();
      return;
    }

    respondida = false;

    const pergunta = banco.perguntas[perguntaAtual];

    titulo.textContent =
      `Pergunta ${perguntaAtual + 1} de ${banco.perguntas.length}: ${pergunta.enunciado}`;

    botoes.forEach((botao, indice) => {

      botao.classList.remove("correta", "incorreta");
      botao.disabled = false;
      botao.onclick = null;

      if (indice < pergunta.opcoes.length) {

        botao.textContent = pergunta.opcoes[indice];
        botao.style.display = "block";

        botao.onclick = () => responder(indice);

      } else {

        botao.style.display = "none";

      }
    });

    atualizarPlacar();
  }

  // RESPONDER À PERGUNTA
  function responder(indice) {

    if (finalizado || respondida) return;

    const pergunta = banco.perguntas[perguntaAtual];

    if (indice < 0 || indice >= pergunta.opcoes.length) return;

    respondida = true;

    botoes.forEach(botao => {
      botao.disabled = true;
    });

    const acertou = indice === pergunta.correta;

    if (acertou) {

      pontos++;

      feedbackTitulo.textContent = "🎉 Resposta correta!";
      feedbackTitulo.className = "feedback-correto";

      feedbackTexto.textContent =
        "Parabéns! Você acertou a pergunta.";

      botoes[indice].classList.add("correta");

    } else {

      feedbackTitulo.textContent = "❌ Resposta incorreta!";
      feedbackTitulo.className = "feedback-errado";

      feedbackTexto.textContent =
        "A resposta correta era: " +
        pergunta.opcoes[pergunta.correta];

      botoes[indice].classList.add("incorreta");
      botoes[pergunta.correta].classList.add("correta");
    }

    atualizarPlacar();

    modalFeedback.classList.remove("hidden");
    modalFeedback.setAttribute("aria-hidden", "false");
  }

  // Botão continuar
  btnContinuar.onclick = () => {

    if (!respondida) return;

    modalFeedback.classList.add("hidden");
    modalFeedback.setAttribute("aria-hidden", "true");

    perguntaAtual++;

    mostrarPergunta();
  };

  // Finalizar quiz
  function finalizarQuiz() {
    finalizado = true;

    titulo.textContent = "Quiz concluído!";

    botoes.forEach(botao => {
      botao.style.display = "none";
    });

    // Abre o modal de resultado
    modal.classList.remove("hidden");
    modal.setAttribute("aria-hidden", "false");

    resultado.replaceChildren();

    const mensagem = document.createElement("h2");
    mensagem.textContent = `Você fez ${pontos} de 5 pontos!`;

    const explicacao = document.createElement("p");
    explicacao.textContent =
      "Digite seu nick para salvar sua pontuação no ranking geral.";

    const formulario = document.createElement("form");

    const campoNick = document.createElement("input");
    campoNick.type = "text";
    campoNick.placeholder = "Digite seu nick";
    campoNick.minLength = 3;
    campoNick.maxLength = 20;
    campoNick.required = true;
    campoNick.autocomplete = "nickname";

    const botaoSalvar = document.createElement("button");
    botaoSalvar.type = "submit";
    botaoSalvar.textContent = "Salvar pontuação";

    const aviso = document.createElement("p");
    aviso.setAttribute("aria-live", "polite");

    formulario.appendChild(campoNick);
    formulario.appendChild(botaoSalvar);

    resultado.appendChild(mensagem);
    resultado.appendChild(explicacao);
    resultado.appendChild(formulario);
    resultado.appendChild(aviso);

    // Ao enviar o nick, salva a pontuação no PHP
    formulario.addEventListener("submit", async function (event) {
      event.preventDefault();

      const nick = campoNick.value.trim();

      if (nick.length < 3 || nick.length > 20) {
        aviso.textContent = "O nick precisa ter entre 3 e 20 caracteres.";
        return;
      }

      botaoSalvar.disabled = true;
      campoNick.disabled = true;
      aviso.textContent = "Salvando sua pontuação...";

      try {
        const resposta = await fetch("/clone/salvar_pontuacao.php", {
          method: "POST",

          headers: {
            "Content-Type": "application/json"
          },

          body: JSON.stringify({
            nick: nick,
            quiz: tipo,
            pontos: pontos
          })
        });

        // Lê primeiro como texto para identificar erros do PHP
        const texto = await resposta.text();

        console.log("STATUS HTTP:", resposta.status);
        console.log("RESPOSTA COMPLETA DO PHP:", texto);

        let dados;

        try {
          dados = JSON.parse(texto);
        } catch (erro) {
          throw new Error(
            "Resposta inválida do PHP: " + texto.substring(0, 300)
          );
        }

        if (!resposta.ok || !dados.sucesso) {
          throw new Error(
            dados.mensagem || "Não foi possível salvar."
          );
        }

        formulario.remove();

        aviso.textContent =
          "✅ Pontuação salva! Confira sua posição no ranking.";

        await mostrarRanking();

      } catch (erro) {
        console.error("Erro ao salvar pontuação:", erro);

        aviso.textContent =
          erro.message || "Erro ao salvar. Tente novamente.";

        botaoSalvar.disabled = false;
        campoNick.disabled = false;
      }
    });

    if (typeof btnVoltar !== "undefined" && btnVoltar) {
      btnVoltar.style.display = "none";
    }

    campoNick.focus();
  }

  // Busca e exibe o ranking salvo no MySQL
  async function mostrarRanking() {
    const tituloRanking = document.createElement("h2");
    tituloRanking.textContent = "🏆 Ranking geral";

    resultado.appendChild(tituloRanking);

    const carregando = document.createElement("p");
    carregando.textContent = "Carregando ranking...";
    resultado.appendChild(carregando);

    try {
      const resposta = await fetch("ranking.php");
      const dados = await resposta.json();

      if (!resposta.ok || !dados.sucesso) {
        throw new Error(dados.mensagem || "Erro ao carregar ranking.");
      }

      carregando.remove();

      const tabela = document.createElement("table");
      tabela.className = "tabela-ranking";

      tabela.innerHTML = `
            <thead>
                <tr>
                    <th>Posição</th>
                    <th>Nick</th>
                    <th>Básico</th>
                    <th>Intermediário</th>
                    <th>Avançado</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody></tbody>
        `;

      const corpo = tabela.querySelector("tbody");

      if (dados.ranking.length === 0) {
        const linha = document.createElement("tr");
        const celula = document.createElement("td");

        celula.colSpan = 6;
        celula.textContent = "Nenhum jogador cadastrado ainda.";

        linha.appendChild(celula);
        corpo.appendChild(linha);
      }

      dados.ranking.forEach((jogador, indice) => {
        const linha = document.createElement("tr");

        const valores = [
          `${indice + 1}º`,
          jogador.nick,
          jogador.basico,
          jogador.intermediario,
          jogador.avancado,
          jogador.total
        ];

        valores.forEach(valor => {
          const celula = document.createElement("td");
          celula.textContent = valor;
          linha.appendChild(celula);
        });

        corpo.appendChild(linha);
      });

      resultado.appendChild(tabela);

    } catch (erro) {
      carregando.textContent =
        "Não foi possível carregar o ranking. Verifique o servidor.";
      console.error(erro);
    }
  }

  // Iniciar
  atualizarPlacar();
  mostrarPergunta();
}
