// Discreet first-visit notice (no tracking cookies on this site)
  (()=>{const n=document.getElementById('cookie-notice');if(!n)return;let seen=false;try{seen=localStorage.getItem('cookie-notice')==='1'}catch(_){}
  if(!seen){n.hidden=false;requestAnimationFrame(()=>n.classList.add('in'))}
  document.getElementById('cookie-ok').addEventListener('click',()=>{try{localStorage.setItem('cookie-notice','1')}catch(_){}n.classList.remove('in');setTimeout(()=>n.hidden=true,500)})})();