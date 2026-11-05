const mysql = require('mysql2/promise');

// Ajuste user/password conforme o seu MySQL local
// (os mesmos dados que você usa hoje na sua URL JDBC: jdbc:mysql://localhost:3306/quiz_app)
const pool = mysql.createPool({
  host: 'localhost',
  user: 'root',
  password: 'alunolab',
  database: 'quiz_app',
  waitForConnections: true,
  connectionLimit: 10
});

module.exports = pool;
