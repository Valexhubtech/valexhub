import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
import path from 'path';
import tailwindcss from "@tailwindcss/vite";

// Get all available themes
const themesDir = path.resolve(__dirname, 'resources/themes');
const themes = fs.existsSync(themesDir) 
  ? fs.readdirSync(themesDir, { withFileTypes: true })
      .filter(dirent => dirent.isDirectory())
      .map(dirent => dirent.name)
  : ['anchor']; // fallback

console.log(`Building assets for themes: ${themes.join(', ')}`);

// Build input array for all themes
const inputs = [
  'resources/css/filament/admin/theme.css',
];

// Add CSS and JS files for each theme
themes.forEach(theme => {
  const cssPath = `resources/themes/${theme}/assets/css/app.css`;
  const jsPath = `resources/themes/${theme}/assets/js/app.js`;
  
  if (fs.existsSync(path.resolve(__dirname, cssPath))) {
    inputs.push(cssPath);
  }
  if (fs.existsSync(path.resolve(__dirname, jsPath))) {
    inputs.push(jsPath);
  }
});

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: inputs,
            refresh: [
                'resources/themes/**/*',
            ],
        }),
    ],
});