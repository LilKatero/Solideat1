import csv
import json

# Nom des fichiers CSV et JSON
csv_file = 'restos.csv'
json_file = 'restaurants.json'

# Liste qui va contenir tous les restaurants sous forme de dictionnaires
data = []

with open(csv_file, newline='', encoding='utf-8') as csvfile:
    # On crée un lecteur CSV avec le bon délimiteur (ici ;)
    reader = csv.DictReader(csvfile, delimiter=';')
    
    # Pour chaque ligne, on récupère un dictionnaire
    for row in reader:
        data.append(row)

# Écriture dans le fichier JSON, avec indentation pour lisibilité
with open(json_file, 'w', encoding='utf-8') as jsonfile:
    json.dump(data, jsonfile, ensure_ascii=False, indent=2)

print(f"Conversion terminée. {len(data)} entrées écrites dans {json_file}.")
