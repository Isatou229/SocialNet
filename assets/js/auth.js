/**
 * assets/js/auth.js
 * Logique des pages vues/clients/connexion.html, inscription.html,
 * mot-de-passe-oublie.html et reinitialiser-mot-de-passe.html
 */

function showFormMsg(elId, message, type) {
  const el = document.getElementById(elId);
  el.textContent = message;
  el.className = "form-msg " + type;
}

// ----- Connexion -----
function initLoginForm() {
  const form = document.getElementById("form-login");
  if (!form) return;
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const email = document.getElementById("login-email").value.trim();
    const mot_de_passe = document.getElementById("login-password").value;

    const res = await apiFetch("/auth/login.php", {
      method: "POST",
      body: { email, mot_de_passe },
    });
    if (res.success) {
      setCurrentUser(res.user, res.token);
      window.location.href = "accueil.html";
    } else {
      showFormMsg("login-msg", res.message, "error");
    }
  });
}

// ----- Inscription -----
function initRegisterForm() {
  const form = document.getElementById("form-register");
  if (!form) return;
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const nom = document.getElementById("reg-nom").value.trim();
    const prenom = document.getElementById("reg-prenom").value.trim();
    const email = document.getElementById("reg-email").value.trim();
    const mot_de_passe = document.getElementById("reg-password").value;
    const confirmation = document.getElementById("reg-password-confirm").value;

    const res = await apiFetch("/auth/register.php", {
      method: "POST",
      body: { nom, prenom, email, mot_de_passe, confirmation },
    });

    if (res.success) {
      showFormMsg("register-msg", res.message + " Redirection...", "success");
      setTimeout(() => (window.location.href = "connexion.html"), 1500);
    } else {
      showFormMsg("register-msg", res.message, "error");
    }
  });
}

// ----- Mot de passe oublié -----
function initForgotForm() {
  const form = document.getElementById("form-forgot");
  if (!form) return;
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const email = document.getElementById("forgot-email").value.trim();
    const res = await apiFetch("/auth/forgot-password.php", {
      method: "POST",
      body: { email },
    });
    showFormMsg("forgot-msg", res.message, res.success ? "success" : "error");
  });
}

// ----- Réinitialisation -----
function initResetForm() {
  const form = document.getElementById("form-reset");
  if (!form) return;
  const token = new URLSearchParams(window.location.search).get("token");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const mot_de_passe = document.getElementById("reset-password").value;
    const confirmation = document.getElementById(
      "reset-password-confirm",
    ).value;

    const res = await apiFetch("/auth/reset-password.php", {
      method: "POST",
      body: { token, mot_de_passe, confirmation },
    });

    if (res.success) {
      showFormMsg("reset-msg", res.message + " Redirection...", "success");
      setTimeout(() => (window.location.href = "connexion.html"), 1500);
    } else {
      showFormMsg("reset-msg", res.message, "error");
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {
  initLoginForm();
  initRegisterForm();
  initForgotForm();
  initResetForm();
});
