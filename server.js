const express = require('express');
const fs = require('fs');
const csv = require('csv-parser');
const fetch = require('node-fetch');
const app = express();
const PORT = 3000;

app.use(express.static('public'));

const restos = [];

function geocode(address) {
  const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json&limit=1`;
  return fetch(url, { headers: { 'User-Agent': 'restomap/1.0' } })
    .then(res => res.json())
    .then(data => {
      if (data.length > 0) {
        return { lat: parseFloat(data[0].lat), lon: parseFloat(data[0].lon) };
      }
      return null;
    })
    .catch(err => {
      console.error('Erreur de géocodage :', err);
      return null;
    });
}

async function loadRestos() {
  return new Promise((resolve) => {
    fs.createReadStream('restos.csv')
      .pipe(csv({ separator: ';' }))
      .on('data', (row) => restos.push(row))
      .on('end', async () => {
        for (let r of restos) {
          const address = `${r["Adresse de l'établissement"]}, ${r["Code postal"]} ${r["Commune"]}`;
          const coords = await geocode(address);
          if (coords) {
            r.lat = coords.lat;
            r.lon = coords.lon;
          }
        }
        console.log('Restaurants chargés et géocodés.');
        resolve();
      });
  });
}

app.get('/api/restos', (req, res) => {
  res.json(restos.filter(r => r.lat && r.lon));
});

loadRestos().then(() => {
  app.listen(PORT, () => {
    console.log(`Serveur lancé sur http://localhost:${PORT}`);
  });
});
