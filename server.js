
const express = require('express');
const fs = require('fs');
const path = require('path');
const csv = require('csv-parser');
const app = express();
const PORT = process.env.PORT || 3000;

app.use(express.static(path.join(__dirname, 'public')));

app.get('/api/restos', (req, res) => {
  const results = [];
  fs.createReadStream(path.join(__dirname, 'restos.csv'))
    .pipe(csv({ separator: ';' }))
    .on('data', (data) => {
      const [lat, lon] = (data.geo || '').split(',').map(x => parseFloat(x.trim()));
      results.push({
        nom: data["Nom de l'établissement"],
        adresse: data["Adresse de l'établissement"],
        commune: data["Commune"],
        lat, lon
      });
    })
    .on('end', () => {
      res.json(results);
    });
});

app.listen(PORT, () => console.log(`Serveur lancé sur http://localhost:${PORT}`));
