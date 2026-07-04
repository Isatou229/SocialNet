/**
 * assets/js/admin.js
 * Logique des pages vues/back-office/*.html
 */

const API_BASE_ADMIN = '../../api';

async function apiFetchAdmin(path, options = {}) {
  const token = sessionStorage.getItem('token');
  const headers = options.headers || {};
  if (!(options.body instanceof FormData)) {
    headers['Content-Type'] = 'application/json';
    if (options.body && typeof options.body !== 'string') options.body = JSON.stringify(options.body);
  }
  if (token) headers['Authorization'] = 'Bearer ' + token;

  const response = await fetch(API_BASE_ADMIN + path, { ...options, headers });
  let data;
  try { data = await response.json(); } catch (e) { data = { success: false, message: 'Réponse invalide' }; }
  if (response.status === 401 || response.status === 403) {
    if (response.status === 401) {
      clearCurrentUser();
      window.location.href = 'dashboard.html';
    }
  }
  return data;
}

function mountAdminNavbar(activePage) {
  const mount = document.getElementById('navbar-mount');
  if (!mount) return;
  const user = getCurrentUser();
  mount.innerHTML = `
    <aside class="admin-sidebar">
      <div class="brand"><span class="dot"></span><span class="word">Social<b>Net</b></span></div>
      <nav class="admin-nav">
        <a href="dashboard.html" class="${activePage === 'dashboard' ? 'active' : ''}"><span class="ic"></span>Dashboard</a>
        <a href="utilisateurs.html" class="${activePage === 'utilisateurs' ? 'active' : ''}"><span class="ic"></span>Utilisateurs</a>
        <a href="articles.html" class="${activePage === 'articles' ? 'active' : ''}"><span class="ic"></span>Publications</a>
        ${user?.role === 'admin' ? `<a href="roles.html" class="${activePage === 'roles' ? 'active' : ''}"><span class="ic"></span>Rôles</a>` : ''}
      </nav>
      <button class="admin-exit" id="btn-admin-logout">← Déconnexion</button>
    </aside>`;
  document.getElementById('btn-admin-logout').addEventListener('click', async () => {
    await apiFetchAdmin('/auth/logout.php', { method: 'POST' });
    clearCurrentUser();
    window.location.href = 'login-admin.html';
  });
}

// ---------- Dashboard ----------
async function loadDashboard() {
  const res = await apiFetchAdmin('/admin/stats.php');
  if (!res.success) return;
  document.getElementById('stat-users').textContent = res.stats.utilisateurs;
  document.getElementById('stat-posts').textContent = res.stats.publications;
  document.getElementById('stat-comments').textContent = res.stats.commentaires;
  document.getElementById('stat-messages').textContent = res.stats.messages;

  const bars = document.getElementById('bars-inscriptions');
  if (res.inscriptions_par_mois.length === 0) {
    bars.innerHTML = '<div class="empty-state">Pas encore assez de données.</div>';
    return;
  }
  const max = Math.max(...res.inscriptions_par_mois.map(i => parseInt(i.total)), 1);
  bars.innerHTML = res.inscriptions_par_mois.map(i => {
    const pct = Math.max(8, Math.round((i.total / max) * 100));
    return `<div class="bar" style="height:${pct}%;"><span>${i.mois.slice(5)}</span></div>`;
  }).join('');
}

// ---------- Utilisateurs ----------
async function loadAdminUsers(query = '') {
  const res = await apiFetchAdmin('/admin/users-list.php?q=' + encodeURIComponent(query));
  const tbody = document.getElementById('users-tbody');
  if (!res.success || res.users.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5">Aucun utilisateur trouvé.</td></tr>';
    return;
  }
  const me = getCurrentUser();
  tbody.innerHTML = res.users.map(u => `
    <tr>
      <td>${escapeHtml(u.prenom)} ${escapeHtml(u.nom)}</td>
      <td>${escapeHtml(u.email)}</td>
      <td class="role"><span class="role-${u.role}">${u.role}</span></td>
      <td>${new Date(u.date_creation).toLocaleDateString('fr-FR')}</td>
      <td>${u.id == me.id ? '' : `<button onclick="deleteAdminUser(${u.id}, this)">Supprimer</button>`}</td>
    </tr>`).join('');
}

async function deleteAdminUser(userId, btn) {
  if (!confirm('Supprimer cet utilisateur ?')) return;
  const res = await apiFetchAdmin('/admin/users-delete.php', { method: 'POST', body: { user_id: userId } });
  if (res.success) {
    btn.closest('tr').remove();
  } else {
    alert(res.message);
  }
}

// ---------- Publications (modération) ----------
async function loadAdminPosts() {
  const res = await apiFetchAdmin('/admin/posts-list.php');
  const tbody = document.getElementById('posts-tbody');
  if (!res.success || res.posts.length === 0) {
    tbody.innerHTML = '<tr><td colspan="4">Aucune publication.</td></tr>';
    return;
  }
  tbody.innerHTML = res.posts.map(p => `
    <tr>
      <td>${escapeHtml(p.prenom)} ${escapeHtml(p.nom)}</td>
      <td>${escapeHtml(p.contenu.slice(0, 60))}${p.contenu.length > 60 ? '…' : ''}</td>
      <td>${new Date(p.date_publication).toLocaleDateString('fr-FR')}</td>
      <td><button onclick="deleteAdminPost(${p.id}, this)">Supprimer</button></td>
    </tr>`).join('');
}

async function deleteAdminPost(postId, btn) {
  if (!confirm('Supprimer cette publication ?')) return;
  const res = await apiFetchAdmin('/admin/posts-delete.php', { method: 'POST', body: { post_id: postId } });
  if (res.success) btn.closest('tr').remove();
}

// ---------- Rôles (admin uniquement) ----------
async function loadAdminRoles() {
  const res = await apiFetchAdmin('/admin/users-list.php');
  const tbody = document.getElementById('roles-tbody');
  const me = getCurrentUser();
  if (!res.success) return;
  tbody.innerHTML = res.users.map(u => `
    <tr>
      <td>${escapeHtml(u.prenom)} ${escapeHtml(u.nom)}</td>
      <td class="role"><span class="role-${u.role}">${u.role}</span></td>
      <td>
        ${u.id == me.id ? '<span style="color:var(--muted);font-size:12px;">moi</span>' : `
        <select onchange="updateUserRole(${u.id}, this.value)">
          <option value="user" ${u.role === 'user' ? 'selected' : ''}>Utilisateur</option>
          <option value="moderateur" ${u.role === 'moderateur' ? 'selected' : ''}>Modérateur</option>
          <option value="admin" ${u.role === 'admin' ? 'selected' : ''}>Administrateur</option>
        </select>`}
      </td>
    </tr>`).join('');
}

async function updateUserRole(userId, role) {
  const res = await apiFetchAdmin('/admin/roles-update.php', { method: 'POST', body: { user_id: userId, role } });
  if (!res.success) alert(res.message);
}
