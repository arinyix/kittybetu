// Toasts/feedback
function showToast(msg, type = 'ok') {
  const toast = document.createElement('div');
  toast.className = 'alert alert-' + type;
  toast.textContent = msg;
  toast.style.position = 'fixed';
  toast.style.bottom = '2rem';
  toast.style.right = '2rem';
  toast.style.zIndex = 9999;
  document.body.appendChild(toast);
  setTimeout(() => { toast.remove(); }, 3000);
}
