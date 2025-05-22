const express = require('express');
const mysql = require('mysql2/promise');
const cors = require('cors');
const bodyParser = require('body-parser');

const app = express();
app.use(cors());
app.use(bodyParser.json());

const dbConfig = {
    host: '127.0.0.1',
    user: 'root',
    password: '',
    port: 3306,
    database: 'prynceindiv'
};

// Get all candidates
app.get('/api/candidates', async (req, res) => {
    try {
        const conn = await mysql.createConnection(dbConfig);
        const [rows] = await conn.execute('SELECT * FROM candidates');
        await conn.end();
        res.json(rows);
    } catch (err) {
        console.error(err);
        res.status(500).json({ message: 'Database error!' });
    }
});

// Add candidate
app.post('/api/candidates', async (req, res) => {
    const { name, position } = req.body;
    try {
        const conn = await mysql.createConnection(dbConfig);
        await conn.execute(
            'INSERT INTO candidates (name, position) VALUES (?, ?)',
            [name, position]
        );
        await conn.end();
        res.json({ message: 'Candidate added!' });
    } catch (err) {
        console.error(err);
        res.status(500).json({ message: 'Database error!' });
    }
});

// Delete candidate
app.delete('/api/candidates/:id', async (req, res) => {
    const { id } = req.params;
    try {
        const conn = await mysql.createConnection(dbConfig);
        await conn.execute('DELETE FROM candidates WHERE id = ?', [id]);
        await conn.end();
        res.json({ message: 'Candidate deleted!' });
    } catch (err) {
        console.error(err);
        res.status(500).json({ message: 'Database error!' });
    }
});

app.listen(3002, () => {
    console.log('Candidate server running on http://127.0.0.1:3002');
});