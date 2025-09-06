// Validação client-side
const form = document.querySelector('form');
if (form) {
  form.addEventListener('submit', function(e) {
    const email = form.querySelector('[name=email]');
    const cpf = form.querySelector('[name=cpf]');
    const phone = form.querySelector('[name=phone]');
    const password = form.querySelector('[name=password]');
    let errors = [];
    if (email && !/^\S+@\S+\.\S+$/.test(email.value)) errors.push('Email inválido.');
    if (cpf && !/^\d{3}\.\d{3}\.\d{3}-\d{2}$/.test(cpf.value)) errors.push('CPF inválido.');
    if (phone && !/^\(\d{2}\) \d{4,5}-\d{4}$/.test(phone.value)) errors.push('Telefone inválido.');
    if (password && password.value.length < 8) errors.push('Senha deve ter ao menos 8 caracteres.');
    if (errors.length) {
      e.preventDefault();
      alert(errors.join('\n'));
    }
  });
}
