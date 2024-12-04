# sprint-dev-2024


- Cahier des charges (facultatif)
- Devis (tous ce que on vas facturer) (combeien par action)
- GANTT (le diagrame) (donc prendre des notes durant le projet)
- BDD (mcd mld shema)
- doc utilisateur (la notice pour quelqu'un qui n'y connais rien)
- doc techenique (si uqelqu'un doit reprendre le code)

Voici un exemple de **README.md** pour expliquer comment utiliser **Tailwind CSS** dans un projet. Vous pouvez personnaliser les parties spécifiques en fonction de votre projet. 

---

# Utiliser Tailwind CSS dans votre projet 🚀

## Introduction

[Tailwind CSS](https://tailwindcss.com/) est un framework CSS utilitaire qui permet de créer rapidement des interfaces modernes et responsives. Ce guide vous explique comment configurer et utiliser Tailwind CSS dans votre projet.

---

## Installation

### Prérequis
Assurez-vous d'avoir **Node.js** et **npm** ou **yarn** installés sur votre machine.

1. **Initialisez votre projet :**
   ```bash
   npm init -y
   ```
   ou 
   ```bash
   yarn init -y
   ```

2. **Installez Tailwind CSS via npm ou yarn :**
   ```bash
   npm install -D tailwindcss postcss autoprefixer
   npx tailwindcss init
   ```

   ou

   ```bash
   yarn add -D tailwindcss postcss autoprefixer
   npx tailwindcss init
   ```

---

## Configuration

1. **Fichier `tailwind.config.js` :**
   Le fichier `tailwind.config.js` est généré automatiquement. Configurez les chemins de vos fichiers où Tailwind doit scanner les classes CSS :

   ```javascript
   module.exports = {
     content: [
       "./src/**/*.{html,js,ts,jsx,tsx}", // Adaptez les chemins à votre projet
     ],
     theme: {
       extend: {},
     },
     plugins: [],
   };
   ```

2. **Ajoutez les directives Tailwind à votre fichier CSS :**
   Créez ou modifiez un fichier CSS (par exemple `src/styles.css`) et ajoutez les directives suivantes :

   ```css
   @tailwind base;
   @tailwind components;
   @tailwind utilities;
   ```

---

## Utilisation

### Avec un projet HTML simple :
1. Ajoutez votre fichier CSS généré dans votre projet HTML :
   ```html
   <link href="styles.css" rel="stylesheet">
   ```
2. Utilisez les classes Tailwind dans vos fichiers HTML :
   ```html
   <div class="bg-blue-500 text-white p-4 rounded">
     Bienvenue dans Tailwind CSS !
   </div>
   ```

### Avec un projet JavaScript (React, Vue, etc.) :
1. Importez le fichier CSS dans votre point d'entrée (par exemple, `src/index.js`) :
   ```javascript
   import './styles.css';
   ```
2. Utilisez les classes Tailwind directement dans vos composants JSX :
   ```jsx
   const App = () => (
     <button className="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
       Cliquez-moi !
     </button>
   );

   export default App;
   ```

---

## Compilation CSS

Pour générer une version optimisée de votre fichier CSS en production, ajoutez ce script dans votre fichier `package.json` :

```json
"scripts": {
  "build:css": "npx tailwindcss -i ./src/styles.css -o ./dist/styles.css --minify"
}
```

Ensuite, exécutez la commande :
```bash
npm run build:css
```

---

## Documentation

Pour plus d'informations, consultez la [documentation officielle de Tailwind CSS](https://tailwindcss.com/docs).

---

## Contribuer

Si vous trouvez des erreurs ou souhaitez améliorer ce projet, n'hésitez pas à ouvrir une **issue** ou une **pull request**. 😊

---

## Licence

Ce projet est sous licence [MIT](LICENSE).

---

Enregistrez ce fichier sous le nom **README.md** à la racine de votre projet pour le publier sur GitHub. 😊
