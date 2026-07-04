/**
 * assets/js/chat.js
 * Logique de vues/clients/chat.html
 * Actualisation par setInterval toutes les 3s (pas de rechargement de page),
 * conforme à la consigne : "intervalle JS" comme alternative aux sockets Node.js.
 */

let currentConvUserId = null;
let pollingInterval = null;

function renderConvItem(conv, isActive) {
  const photo = conv.photo ? '../../' + conv.photo : 'https://api.dicebear.com/9.x/avataaars/svg?seed=' + encodeURIComponent(conv.prenom);
  return `<div class="conv-item ${isActive ? 'active' : ''}" data-user-id="${conv.id}" onclick="openConversation(${conv.id}, '${escapeHtml(conv.prenom)} ${escapeHtml(conv.nom)}', '${photo}')">
    <img class="avatar" src="${photo}" alt="">
    <div class="info"><b>${escapeHtml(conv.prenom)} ${escapeHtml(conv.nom)}</b><span>${escapeHtml(conv.dernier_message || 'Image envoyée')}</span></div>
  </div>`;
}

async function loadConversations() {
  const res = await apiFetch('/messages/conversations.php');
  const list = document.getElementById('conv-list');
  if (!res.success || res.conversations.length === 0) {
    list.innerHTML = '<div class="empty-state">Aucune conversation. Cherche un ami pour démarrer !</div>';
    return;
  }
  list.innerHTML = res.conversations.map(c => renderConvItem(c, c.id === currentConvUserId)).join('');
}

function openConversation(userId, name, photo) {
  currentConvUserId = userId;
  document.getElementById('chat-empty').classList.add('hidden');
  document.getElementById('chat-window').classList.remove('hidden');
  document.getElementById('chat-name').textContent = name;
  document.getElementById('chat-photo').src = photo;
  document.querySelectorAll('.conv-item').forEach(el => {
    el.classList.toggle('active', el.dataset.userId == userId);
  });
  loadMessages();
  if (pollingInterval) clearInterval(pollingInterval);
  pollingInterval = setInterval(loadMessages, 3000);
}

function renderMessage(m, myId) {
  const isMe = m.sender_id == myId;
  const img = m.image ? `<img src="../../${m.image}" alt="">` : '';
  const text = m.message ? escapeHtml(m.message) : '';
  return `<div class="bubble-row ${isMe ? 'me' : ''}"><div class="chat-bubble">${text}${img}</div></div>`;
}

async function loadMessages() {
  if (!currentConvUserId) return;
  const user = getCurrentUser();
  const res = await apiFetch('/messages/thread.php?with=' + currentConvUserId);
  if (!res.success) return;
  const body = document.getElementById('chat-body');
  const wasAtBottom = body.scrollHeight - body.scrollTop - body.clientHeight < 60;
  body.innerHTML = res.messages.map(m => renderMessage(m, user.id)).join('');
  if (wasAtBottom) body.scrollTop = body.scrollHeight;
}

async function sendChatMessage() {
  if (!currentConvUserId) return;
  const input = document.getElementById('chat-input-text');
  const fileInput = document.getElementById('chat-input-image');
  if (!input.value.trim() && !fileInput.files[0]) return;

  const formData = new FormData();
  formData.append('receiver_id', currentConvUserId);
  formData.append('message', input.value.trim());
  if (fileInput.files[0]) formData.append('image', fileInput.files[0]);

  const res = await apiFetch('/messages/send.php', { method: 'POST', body: formData });
  if (res.success) {
    input.value = '';
    fileInput.value = '';
    loadMessages();
    loadConversations();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  if (!document.getElementById('conv-list')) return;
  loadConversations();

  document.getElementById('btn-send-chat').addEventListener('click', sendChatMessage);
  document.getElementById('chat-input-text').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') sendChatMessage();
  });

  // Ouvre directement une conversation si on arrive via amis.html?with=ID
  const withId = new URLSearchParams(window.location.search).get('with');
  if (withId) {
    apiFetch('/users/profile.php?id=' + withId).then(res => {
      if (res.success) {
        const photo = res.profil.photo ? '../../' + res.profil.photo : 'https://api.dicebear.com/9.x/avataaars/svg?seed=' + encodeURIComponent(res.profil.prenom);
        openConversation(res.profil.id, res.profil.prenom + ' ' + res.profil.nom, photo);
      }
    });
  }
});
