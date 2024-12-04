# sprint-dev-2024


- Cahier des charges (facultatif)
- Devis (tous ce que on vas facturer) (combeien par action)
- GANTT (le diagrame) (donc prendre des notes durant le projet)
- BDD (mcd mld shema)
- doc utilisateur (la notice pour quelqu'un qui n'y connais rien)
- doc techenique (si uqelqu'un doit reprendre le code)




Comment utiliser tailwind ?
https://tailwindcss.com/docs/installation
- Install tailwindcss via npm, and create your tailwind.config.js file.
  - Pour faire ca utilsier ses commandes dans le terminal :

npm install -D tailwindcss
npx tailwindcss init

- Add the paths to all of your template files in your tailwind.config.js file.
 ajout du lien pour connecter tailwind a votre code :
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./src/**/*.{html,js}"],
  theme: {
    extend: {},
  },
  plugins: [],
}

  - par exemple pour moi ca donne ca : 

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./Applications/XAMPP/xamppfiles/htdocs/MNB/sprint-dev-2024.{html,js}"],
  theme: {
    extend: {},
  },
  plugins: [],
}

- Add the @tailwind directives for each of Tailwind’s layers to your main CSS file.
  @tailwind base;
  @tailwind components;
  @tailwind utilities;



- Run the CLI tool to scan your template files for classes and build your CSS.
  -dans le terminal :


    npx tailwindcss -i ./src/input.css -o ./src/output.css --watch
