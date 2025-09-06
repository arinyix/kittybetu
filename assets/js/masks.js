// Máscara CPF
const cpfInput = document.getElementById('cpf');
if (cpfInput) {
  cpfInput.addEventListener('input', function(e) {
    let v = e.target.value.replace(/\D/g, '');
    if (v.length > 11) v = v.slice(0, 11);
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
    v = v.replace(/(\d{3})\.(\d{3})\.(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
    e.target.value = v;
  });
}
// Máscara Telefone
const phoneInput = document.getElementById('phone');
if (phoneInput) {
  phoneInput.addEventListener('input', function(e) {
    let v = e.target.value.replace(/\D/g, '');
    if (v.length > 11) v = v.slice(0, 11);
    if (v.length > 10) {
      v = v.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else {
      v = v.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    }
    e.target.value = v;
  });
}
