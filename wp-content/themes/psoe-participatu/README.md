# PSOE Participatiu 2027 — Theme WordPress + Tailwind

Réplica del diseño de la captura: hero con gradiente rojo, stats, valores, áreas temáticas, propuestas destacadas, newsletter y footer.

## Instalación
1. Copia la carpeta `psoe-participatu` en `wp-content/themes/` (ya está aquí).
2. En WP-Admin → Apariencia → Temas, activa **PSOE Participatiu 2027**.
3. Crea una página “Portada” (sin contenido, plantilla por defecto) y en Ajustes → Lectura márcala como portada estática. `front-page.php` se usará automáticamente.
4. Personaliza textos e imagen hero en Apariencia → Personalizar → **Portada PSOE 2027**.
5. Crea propuestas en **Propuestas → Añadir**. Asigna categoría y votos. Las 3 más votadas salen en portada.

## Tailwind
- En desarrollo funciona sin compilar gracias al CDN (`https://cdn.tailwindcss.com` + config inline en `functions.php`).
- Para producción:
```bash
npm install
npm run build
```
Genera `assets/css/theme.css` (se carga automáticamente si existe).

## Funcionalidad
- CPT `propuesta` + taxonomías `categoria_propuesta` y `area_tematica`.
- Votos AJAX (`inc/votos.php`, cookie anti-doblevoto, formato 1.2k).
- Buscador del header filtra propuestas (`?s=&post_type=propuesta`).
- Responsive mobile-first como la captura (max-width 1120px).

## Páginas + Gravity Forms (Image 1 / Image 2)
- Crea una página “Enviar propuesta”, asígnale la plantilla **Enviar propuesta (PSOE)** (`template-propuesta.php`)
  e inserta el formulario Gravity en el contenido. El hero “Tu voz en el programa 2027” y la tarjeta
  “Nueva Propuesta” se generan solos; el extracto de la página se usa como subtítulo si existe.
- Las páginas normales (ej. **Enquesta**) usan `page.php`: título a la izquierda + tarjeta blanca con el form.
- El estilo es automático, pero el form debe respetar estas etiquetas para activar los extras:
  - Radio obligatorio etiquetado **“Categoría o Área Temática”** → se muestra en tarjetas 3×2 con icono
    (opciones que contengan: Sanidad, Educación, Economía/Empleo, Transición/Ecología, Igualdad, Otras Áreas).
  - Campo de texto **“Título de la propuesta”**, **“Descripción detallada”** (textarea, contador 0/2000),
    **“Nombre (Opcional)”** y **“Email (Opcional)”** (con iconos), checkbox de **Política de Privacidad**.
  - En formularios de encuesta, los campos required checkbox/radio muestran “(Obligatorio)” en rojo.
  - El botón conserva el texto configurado en Gravity y se pinta rojo full-width (con flecha en propuestas).
- Header de páginas: logo PSOE + Programa 2027 / Noticias / Participa + **Iniciar Sesión** (`wp_login_url()`).
  Footer de páginas: versión clara centrada; el resto del sitio mantiene newsletter + footer oscuro.

## Estructura
```
style.css / functions.php / header.php / footer.php / front-page.php
archive-propuesta.php / single-propuesta.php / index.php / page.php / single.php / search.php / 404.php
template-parts/card-propuesta.php
inc/cpt-propuestas.php, votos.php, customizer.php, helpers.php
src/input.css / tailwind.config.js / assets/css/custom.css / js/main.js
```
