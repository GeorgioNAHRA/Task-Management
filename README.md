# sprint-dev-2024


- Cahier des charges (facultatif)
- Devis (tous ce que on vas facturer) (combeien par action)
- GANTT (le diagrame) (donc prendre des notes durant le projet)
- BDD (mcd mld shema)
- doc utilisateur (la notice pour quelqu'un qui n'y connais rien)
- doc techenique (si uqelqu'un doit reprendre le code)



Utiliser Tailwind CSS dans votre projet 🚀
Introduction
Tailwind CSS est un framework CSS utilitaire qui permet de créer rapidement des interfaces modernes et responsives. Ce guide vous explique comment configurer et utiliser Tailwind CSS dans votre projet.

Installation
Prérequis
Assurez-vous d'avoir Node.js et npm ou yarn installés sur votre machine.

Initialisez votre projet :

bash
Copier le code
npm init -y
ou

bash
Copier le code
yarn init -y
Installez Tailwind CSS via npm ou yarn :

bash
Copier le code
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init
ou

bash
Copier le code
yarn add -D tailwindcss postcss autoprefixer
npx tailwindcss init
Configuration
Fichier tailwind.config.js : Le fichier tailwind.config.js est généré automatiquement. Configurez les chemins de vos fichiers où Tailwind doit scanner les classes CSS :

javascript
Copier le code
module.exports = {
  content: [
    "./src/**/*.{html,js,ts,jsx,tsx}", // Adaptez les chemins à votre projet
  ],
  theme: {
    extend: {},
  },
  plugins: [],
};
Ajoutez les directives Tailwind à votre fichier CSS : Créez ou modifiez un fichier CSS (par exemple src/styles.css) et ajoutez les directives suivantes :

css
Copier le code
@tailwind base;
@tailwind components;
@tailwind utilities;
Utilisation
Avec un projet HTML simple :
Ajoutez votre fichier CSS généré dans votre projet HTML :
html
Copier le code
<link href="styles.css" rel="stylesheet">
Utilisez les classes Tailwind dans vos fichiers HTML :
html
Copier le code
<div class="bg-blue-500 text-white p-4 rounded">
  Bienvenue dans Tailwind CSS !
</div>
Avec un projet JavaScript (React, Vue, etc.) :
Importez le fichier CSS dans votre point d'entrée (par exemple, src/index.js) :
javascript
Copier le code
import './styles.css';
Utilisez les classes Tailwind directement dans vos composants JSX :
jsx
Copier le code
const App = () => (
  <button className="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
    Cliquez-moi !
  </button>
);

export default App;
Compilation CSS
Pour générer une version optimisée de votre fichier CSS en production, ajoutez ce script dans votre fichier package.json :

json
Copier le code
"scripts": {
  "build:css": "npx tailwindcss -i ./src/styles.css -o ./dist/styles.css --minify"
}
Ensuite, exécutez la commande :

bash
Copier le code
npm run build:css
Documentation
Pour plus d'informations, consultez la documentation officielle de Tailwind CSS.


}

- Add the @tailwind directives for each of Tailwind’s layers to your main CSS file.
  @tailwind base;
  @tailwind components;
  @tailwind utilities;




    npx tailwindcss -i ./src/input.css -o ./src/output.css --watch
