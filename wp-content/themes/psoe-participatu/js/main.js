// Menú móvil + votos + newsletter + Gravity Forms PSOE
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('menuBtn');
  const menu = document.getElementById('mobileMenu');
  if (btn && menu) {
    btn.addEventListener('click', () => {
      const open = menu.classList.toggle('hidden');
      btn.setAttribute('aria-expanded', String(!open));
    });
  }

  document.querySelectorAll('[data-vote]').forEach((el) => {
    el.addEventListener('click', async () => {
      if (el.disabled || el.classList.contains('voted')) return;
      const post_id = el.getAttribute('data-vote');
      el.disabled = true;
      try {
        const fd = new FormData();
        fd.append('action', 'psoe_vote');
        fd.append('nonce', PSOE.nonce);
        fd.append('post_id', post_id);
        const res = await fetch(PSOE.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: fd });
        const json = await res.json();
        if (json.success) {
          el.querySelector('.vote-count').textContent = json.data.formateado;
          el.classList.add('voted');
        } else if (json.data && json.data.votos !== undefined) {
          el.querySelector('.vote-count').textContent = json.data.votos;
          el.classList.add('voted');
        }
      } catch (e) { el.disabled = false; }
    });
  });

  const form = document.getElementById('newsForm');
  const msg = document.getElementById('newsMsg');
  if (form && msg) form.addEventListener('submit', () => {
    if (!form.checkValidity()) { form.reportValidity(); return; }
    msg.classList.remove('hidden');
    form.querySelector('input[type=email]').value = '';
  });

  initPsoeGravity();
  initPsoeBlocs();
  // Re-inicializar tras render AJAX de Gravity (multipágina / validación).
  if (window.jQuery) {
    window.jQuery(document).on('gform_post_render', () => { initPsoeGravity(); initPsoeBlocs(); });
  }
});

function initPsoeGravity() {
  document.querySelectorAll('.gform_wrapper').forEach((wrap) => {
    // 1. Detecta el campo "Categoría o Área Temática" y lo convierte en tarjetas.
    wrap.querySelectorAll('.gfield_radio').forEach((radio) => {
      const field = radio.closest('.gfield');
      if (!field || field.classList.contains('psoe-cats')) return;
      const label = field.querySelector('.gfield_label');
      const txt = label ? label.textContent : '';
      if (/categor|rea tem/i.test(txt)) field.classList.add('psoe-cats');
    });

    // 2. Iconos de ayuda para Nombre / Email (si el filtro PHP no los marcó).
    wrap.querySelectorAll('.gfield').forEach((field) => {
      const label = field.querySelector('.gfield_label');
      if (!label) return;
      const t = label.textContent.toLowerCase();
      if (t.includes('nombre')) field.classList.add('psoe-field-nombre');
      if (t.includes('email') || t.includes('e-mail') || t.includes('correo')) field.classList.add('psoe-field-email');
      // Consentimiento: fila flexible.
      if (field.querySelector('input[type="checkbox"]') && /privacidad|acepto|consiento/i.test(field.textContent)) {
        field.classList.add('psoe-consent');
      }
    });

    // 3. "(Obligatorio)" bajo preguntas required tipo checkbox/radio (estilo Image 2).
    wrap.querySelectorAll('.gfield_contains_required.gfield--type-checkbox, .gfield_contains_required.gfield--type-radio').forEach((field) => {
      if (field.querySelector('.psoe-obligatorio')) return;
      const label = field.querySelector('.gfield_label');
      if (!label) return;
      const s = document.createElement('span');
      s.className = 'psoe-obligatorio';
      s.textContent = '(Obligatorio)';
      label.after(s);
    });

    // 4. Contador "0 / 2000 caracteres" en textareas de propuesta.
    wrap.querySelectorAll('textarea').forEach((ta) => {
      const field = ta.closest('.gfield');
      if (!field || field.querySelector('.psoe-counter')) return;
      const max = parseInt(ta.getAttribute('maxlength') || '2000', 10);
      ta.setAttribute('maxlength', String(max));
      const holder = document.createElement('div');
      holder.className = 'psoe-textarea-wrap';
      ta.parentNode.insertBefore(holder, ta);
      holder.appendChild(ta);
      const c = document.createElement('span');
      c.className = 'psoe-counter';
      holder.appendChild(c);
      const update = () => { c.textContent = ta.value.length + ' / ' + max + ' caracteres'; };
      ta.addEventListener('input', update);
      update();
    });
  });
}

// 5. Cabeceras elegantes para las líneas "BLOC N: Título (subtítulo)" de la enquesta.
function initPsoeBlocs() {
  const scope = document.querySelector('.psoe-card-body') || document.querySelector('main#content');
  if (!scope || scope.dataset.psoeBlocsDone) return;
  scope.dataset.psoeBlocsDone = '1';

  const sel = 'p, h1, h2, h3, h4, div, li, span, strong, em';
  const blocText = (node) => {
    const t = node.textContent.trim().replace(/\s+/g, ' ');
    if (t.length === 0 || t.length > 220) return null;
    return t.match(/^BLOC\s*(\d+)\s*:?\s*(.+)$/i);
  };
  // Solo el elemento coincidente más interno: si contiene otro match, se salta.
  const matches = [...scope.querySelectorAll(sel)].filter((el) => {
    if (el.dataset.psoeBloc || el.closest('.psoe-bloc-head')) return false;
    // No tocar etiquetas de campos, opciones, leyendas ni enlaces.
    if (el.closest('.gfield_label, .gchoice, legend, label, a, button')) return false;
    if (el.querySelector('input, select, textarea, button, a, canvas, iframe')) return false;
    return blocText(el) !== null;
  });
  matches.forEach((el) => {
    if (el.closest('.psoe-bloc-head')) return;
    if (matches.some((o) => o !== el && el.contains(o))) return;
    const m = blocText(el);
    if (!m) return;

    const num = m[1];
    let rest = m[2].trim();
    let tagline = '';
    const pm = rest.match(/\(([^()]*)\)\s*$/);
     if (pm) {
      tagline = pm[1].trim();
      rest = rest.slice(0, pm.index).trim().replace(/[:–—-]\s*$/, '');
    } 

    const badge = document.createElement('span');
    badge.className = 'psoe-bloc-num';
    badge.textContent = 'BLOC ' + num;
    const titles = document.createElement('span');
    titles.className = 'psoe-bloc-titles';
    const title = document.createElement('strong');
    title.textContent = rest || ('Bloc ' + num);
    titles.appendChild(title);
    if (tagline) {
      const tag = document.createElement('em');
      // Se conservan los paréntesis originales del título.
      tag.textContent = ' (' + tagline + ')';
      titles.appendChild(tag);
    }
    // Transformación in situ: conserva la etiqueta original (h1, p, div…).
    el.classList.add('psoe-bloc-head');
    el.replaceChildren(badge, titles);
    el.dataset.psoeBloc = '1';
  });
}
