const pool = require('./db');

async function salvarPontuacao(usuarioId, quiz, pontos) {
  const [result] = await pool.query(
    'INSERT INTO pontuacoes (usuario_id, quiz, pontos) VALUES (?, ?, ?)',
    [usuarioId, quiz, pontos]
  );
  return result.insertId;
}

async function buscarPontuacoesPorUsuario(usuarioId) {
  const [rows] = await pool.query(
    'SELECT * FROM pontuacoes WHERE usuario_id = ? ORDER BY data DESC',
    [usuarioId]
  );
  return rows;
}

module.exports = { salvarPontuacao, buscarPontuacoesPorUsuario };
