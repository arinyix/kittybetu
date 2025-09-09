// Auto-fecha alerts
document.querySelectorAll('.alert').forEach(el=>{
  setTimeout(()=>{ el.style.display='none'; }, 4500);
});

// Máscara CPF ###.###.###-##
function maskCPF(v){
  v=v.replace(/\D/g,'').slice(0,11);
  if(v.length>9) return v.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/,'$1.$2.$3-$4');
  if(v.length>6) return v.replace(/(\d{3})(\d{3})(\d{0,3})/,'$1.$2.$3');
  if(v.length>3) return v.replace(/(\d{3})(\d{0,3})/,'$1.$2');
  return v;
}
// Máscara telefone (##) #####-####
function maskPhone(v){
  v=v.replace(/\D/g,'').slice(0,11);
  if(v.length<=10) return v.replace(/(\d{0,2})(\d{0,4})(\d{0,4})/, function(_,a,b,c){ 
    return (a?`(${a}) `:'') + (b?b:'') + (c?`-${c}`:''); 
  });
  return v.replace(/(\d{0,2})(\d{0,5})(\d{0,4})/, function(_,a,b,c){ 
    return (a?`(${a}) `:'') + (b?b:'') + (c?`-${c}`:''); 
  });
}

document.addEventListener('input', (e)=>{
  const t = e.target;
  if (t.matches('input[data-mask="cpf"]')) t.value = maskCPF(t.value);
  if (t.matches('input[data-mask="phone"]')) t.value = maskPhone(t.value);
});

// Validação leve (extra ao HTML5)
document.addEventListener('submit', (e)=>{
  const form = e.target;
  if (!(form instanceof HTMLFormElement)) return;
  const email = form.querySelector('input[type="email"]');
  if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    e.preventDefault();
    alert('Informe um email válido.');
  }
});
