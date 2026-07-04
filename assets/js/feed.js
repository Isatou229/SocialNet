/**
 * assets/js/feed.js
 * Logique de vues/clients/accueil.html (et profil.html pour les posts d'un utilisateur)
 */

function renderPost(post, currentUserId) {
  const isLiked = post.ma_reaction === 'like';
  const isDisliked = post.ma_reaction === 'dislike';
  const peutSupprimer = post.auteur_id == currentUserId;
  const photoAuteur = post.photo ? '../../' + post.photo : 'https://api.dicebear.com/9.x/avataaars/svg?seed=' + encodeURIComponent(post.prenom);

  return `
  <article class="card post" data-post-id="${post.id}">
    <div class="post-head">
      <img class="avatar" src="${photoAuteur}" alt="">
      <div>
        <b><a href="profil.html?id=${post.auteur_id}">${escapeHtml(post.prenom)} ${escapeHtml(post.nom)}</a></b>
        <span class="meta">${formatDate(post.date_publication)}</span>
      </div>
      ${peutSupprimer ? '<button class="post-delete" onclick="deletePost(' + post.id + ', this)">Supprimer</button>' : ''}
    </div>
    <p class="post-body">${escapeHtml(post.contenu)}</p>
    ${post.image ? `<img class="post-image" src="../../${post.image}" alt="">` : ''}
    <div class="post-actions">
      <button class="react-btn like ${isLiked ? 'active' : ''}" onclick="toggleReaction(${post.id}, 'like', this)">👍 <span class="count">${post.nb_likes}</span></button>
      <button class="react-btn dislike ${isDisliked ? 'active' : ''}" onclick="toggleReaction(${post.id}, 'dislike', this)">👎 <span class="count">${post.nb_dislikes}</span></button>
      <button class="react-btn comment" onclick="toggleComments(${post.id}, this)">💬 ${post.nb_comments} commentaire${post.nb_comments == 1 ? '' : 's'}</button>
    </div>
    <div class="comments hidden" id="comments-${post.id}">
      <div class="comment-list"></div>
      <div class="comment-add">
        <input type="text" placeholder="Écrire un commentaire..." onkeydown="if(event.key==='Enter') addComment(${post.id}, this)">
        <button onclick="addComment(${post.id}, this.previousElementSibling)">Envoyer</button>
      </div>
    </div>
  </article>`;
}

async function loadFeed(userIdFilter = null) {
  const container = document.getElementById('feed-container');
  if (!container) return;
  const user = getCurrentUser();
  const url = userIdFilter ? `/posts/list.php?user_id=${userIdFilter}` : '/posts/list.php';
  const res = await apiFetch(url);

  if (!res.success || res.posts.length === 0) {
    container.innerHTML = '<div class="card empty-state">Aucune publication pour le moment.</div>';
    return;
  }
  container.innerHTML = res.posts.map(p => renderPost(p, user.id)).join('');
}

async function createPost() {
  const text = document.getElementById('composer-text');
  const fileInput = document.getElementById('composer-image');
  if (!text.value.trim()) return;

  const formData = new FormData();
  formData.append('contenu', text.value.trim());
  if (fileInput.files[0]) formData.append('image', fileInput.files[0]);

  const res = await apiFetch('/posts/create.php', { method: 'POST', body: formData });
  if (res.success) {
    text.value = '';
    fileInput.value = '';
    loadFeed();
  } else {
    alert(res.message);
  }
}

async function deletePost(postId, btn) {
  if (!confirm('Supprimer cette publication ?')) return;
  const res = await apiFetch('/posts/delete.php', { method: 'POST', body: { post_id: postId } });
  if (res.success) {
    btn.closest('.post').remove();
  } else {
    alert(res.message);
  }
}

async function toggleReaction(postId, type, btn) {
  const res = await apiFetch('/likes/toggle.php', { method: 'POST', body: { post_id: postId, type } });
  if (!res.success) return;
  const post = btn.closest('.post');
  const likeBtn = post.querySelector('.like');
  const dislikeBtn = post.querySelector('.dislike');
  likeBtn.querySelector('.count').textContent = res.nb_likes;
  dislikeBtn.querySelector('.count').textContent = res.nb_dislikes;
  likeBtn.classList.toggle('active', res.ma_reaction === 'like');
  dislikeBtn.classList.toggle('active', res.ma_reaction === 'dislike');
}

async function toggleComments(postId, btn) {
  const box = document.getElementById('comments-' + postId);
  box.classList.toggle('hidden');
  if (!box.classList.contains('hidden') && !box.dataset.loaded) {
    box.dataset.loaded = '1';
    await loadComments(postId);
  }
}

async function loadComments(postId) {
  const box = document.getElementById('comments-' + postId);
  const list = box.querySelector('.comment-list');
  const res = await apiFetch('/comments/list.php?post_id=' + postId);
  if (!res.success || res.comments.length === 0) {
    list.innerHTML = '';
    return;
  }
  list.innerHTML = res.comments.map(c => renderComment(c)).join('');
}

function renderComment(c) {
  const photo = c.photo ? '../../' + c.photo : 'https://api.dicebear.com/9.x/avataaars/svg?seed=' + encodeURIComponent(c.prenom);
  return `<div class="comment-item">
    <img class="avatar" src="${photo}" alt="">
    <div class="bubble"><b>${escapeHtml(c.prenom)} ${escapeHtml(c.nom)}</b>${escapeHtml(c.contenu)}</div>
  </div>`;
}

async function addComment(postId, inputEl) {
  if (!inputEl.value.trim()) return;
  const res = await apiFetch('/comments/add.php', { method: 'POST', body: { post_id: postId, contenu: inputEl.value.trim() } });
  if (res.success) {
    const box = document.getElementById('comments-' + postId);
    box.querySelector('.comment-list').insertAdjacentHTML('beforeend', renderComment(res.comment));
    inputEl.value = '';
    // met à jour le compteur affiché sur le bouton "commentaires"
    const post = document.querySelector(`.post[data-post-id="${postId}"]`);
    const commentBtn = post.querySelector('.comment');
    const current = parseInt(commentBtn.textContent.match(/\d+/)[0]) + 1;
    commentBtn.innerHTML = `💬 ${current} commentaire${current === 1 ? '' : 's'}`;
  }
}
