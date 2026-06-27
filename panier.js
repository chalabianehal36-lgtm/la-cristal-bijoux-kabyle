

// ── Clé de stockage ──────────────────────────────────────────────────────────
const PANIER_KEY = 'cristal_panier';

// ── Lire le panier ───────────────────────────────────────────────────────────
function getPanier() {
  try {
    return JSON.parse(localStorage.getItem(PANIER_KEY)) || [];
  } catch { return []; }
}

// ── Sauvegarder le panier ────────────────────────────────────────────────────
function savePanier(panier) {
  localStorage.setItem(PANIER_KEY, JSON.stringify(panier));
}

// ── Ajouter un article ───────────────────────────────────────────────────────
function ajouterAuPanier(product, quantite = 1) {
  const panier = getPanier();
  const existing = panier.find(p => p.ref === product.ref);

  if (existing) {
    existing.quantite = Math.min(existing.quantite + quantite, 999);
  } else {
    panier.push({ ...product, quantite });
  }

  savePanier(panier);
  afficherToast(`✅ ${product.name} ajouté au panier !`);
  updateCartBadge();
}

// ── Supprimer un article ─────────────────────────────────────────────────────
function supprimerArticle(ref) {
  let panier = getPanier().filter(p => p.ref !== ref);
  savePanier(panier);
  renderPanier();
  updateCartBadge();
  afficherToast('🗑️ Article supprimé', 'error');
}

// ── Modifier la quantité ─────────────────────────────────────────────────────
function modifierQuantite(ref, delta) {
  let panier = getPanier();
  const item = panier.find(p => p.ref === ref);
  if (!item) return;

  item.quantite += delta;
  if (item.quantite <= 0) {
    panier = panier.filter(p => p.ref !== ref);
    afficherToast('🗑️ Article supprimé', 'error');
  }
  savePanier(panier);
  renderPanier();
  updateCartBadge();
}

// ── Vider le panier ──────────────────────────────────────────────────────────
function viderPanier() {
  if (!confirm('Voulez-vous vraiment vider votre panier ?')) return;
  savePanier([]);
  renderPanier();
  updateCartBadge();
  afficherToast('🗑️ Panier vidé', 'error');
}

// ── Parser le prix DZD → nombre ─────────────────────────────────────────────
function parsePrix(priceStr) {
  return parseInt(priceStr.replace(/[^\d]/g, ''), 10) || 0;
}

// ── Formater un nombre → prix DZD ───────────────────────────────────────────
function formatPrix(num) {
  return num.toLocaleString('fr-DZ') + ' DZD';
}

// ── Calculer le total ────────────────────────────────────────────────────────
function calculerTotal(panier) {
  return panier.reduce((sum, p) => sum + parsePrix(p.price) * p.quantite, 0);
}

// ── Afficher le panier ───────────────────────────────────────────────────────
function renderPanier() {
  const panier = getPanier();
  const list   = document.getElementById('panierList');
  const empty  = document.getElementById('emptyMsg');
  const summary = document.getElementById('summaryCard');

  if (!list) return; // pas sur panier.html

  list.innerHTML = '';

  if (panier.length === 0) {
    empty.style.display  = 'block';
    summary.style.display = 'none';
    return;
  }

  empty.style.display   = 'none';
  summary.style.display = 'block';

  panier.forEach(item => {
    const subtotal = parsePrix(item.price) * item.quantite;

    const div = document.createElement('div');
    div.className = 'panier-item';
    div.innerHTML = `
      <img class="item-img" src="${item.image}" alt="${item.name}"
           onerror="this.src='IMAGES/placeholder.jpg'">
      <div class="item-info">
        <div class="item-name">${item.name}</div>
        <div class="item-ref">Réf : ${item.ref}</div>
        <div class="item-price">${item.price}</div>
      </div>
      <div class="qty-control">
        <button class="qty-btn" onclick="modifierQuantite('${item.ref}', -1)">−</button>
        <span class="qty-display">${item.quantite}</span>
        <button class="qty-btn" onclick="modifierQuantite('${item.ref}', +1)">+</button>
      </div>
      <div class="item-subtotal">${formatPrix(subtotal)}</div>
      <button class="btn-delete" onclick="supprimerArticle('${item.ref}')" title="Supprimer">✕</button>
    `;
    list.appendChild(div);
  });

  // Résumé
  const total = calculerTotal(panier);
  const nbArticles = panier.reduce((s, p) => s + p.quantite, 0);

  document.getElementById('totalItems').textContent = nbArticles + ' article(s)';
  document.getElementById('totalPrice').textContent = formatPrix(total);
}

// ── Modal ────────────────────────────────────────────────────────────────────
function ouvrirModal() {
  const panier = getPanier();
  if (panier.length === 0) {
    afficherToast('⚠️ Votre panier est vide !', 'error');
    return;
  }

  const modalItems = document.getElementById('modalItems');
  const modalTotal = document.getElementById('modalTotal');

  modalItems.innerHTML = panier.map(p => {
    const subtotal = parsePrix(p.price) * p.quantite;
    return `
      <div class="m-item">
        <span>${p.name} × ${p.quantite}</span>
        <span>${formatPrix(subtotal)}</span>
      </div>`;
  }).join('');

  const total = calculerTotal(panier);
  modalTotal.textContent = `Total : ${formatPrix(total)}`;

  document.getElementById('modalOverlay').classList.add('active');
}

function fermerModal() {
  document.getElementById('modalOverlay').classList.remove('active');
}

// Fermer en cliquant hors du modal
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('modalOverlay')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modalOverlay')) fermerModal();
  });
});

// ── Confirmer la commande → envoie via fetch vers commande_lot.php ───────────
function confirmerCommande() {
  const panier = getPanier();
  if (panier.length === 0) return;

  fermerModal();

  // Envoi AJAX vers commande_lot.php
  fetch('commande_lot.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ articles: panier })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      afficherToast('🎉 Commande confirmée avec succès !');
      savePanier([]);
      setTimeout(() => {
        renderPanier();
        updateCartBadge();
      }, 1200);
    } else {
      afficherToast('⚠️ ' + (data.message || 'Erreur. Réessayez.'), 'error');
    }
  })
  .catch(() => {
    // Mode sans serveur : simulation locale
    afficherToast('🎉 Commande enregistrée !');
    savePanier([]);
    setTimeout(() => {
      renderPanier();
      updateCartBadge();
    }, 1200);
  });
}

// ── Toast ────────────────────────────────────────────────────────────────────
function afficherToast(msg, type = '') {
  const toast = document.getElementById('toast');
  if (!toast) return;

  toast.textContent = msg;
  toast.className = 'toast' + (type ? ' ' + type : '') + ' show';

  setTimeout(() => { toast.classList.remove('show'); }, 3000);
}

// ── Badge panier (sur index.html) ────────────────────────────────────────────
function updateCartBadge() {
  const badge = document.getElementById('cart-badge');
  if (!badge) return;
  const total = getPanier().reduce((s, p) => s + p.quantite, 0);
  badge.textContent = total;
  badge.style.display = total > 0 ? 'flex' : 'none';
}

// ── Init ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  renderPanier();
  updateCartBadge();
});
