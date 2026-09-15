document.addEventListener('DOMContentLoaded',()=>{
 const token=window.CSRF; const search=document.getElementById('tableSearch');
 const sidebar=document.getElementById('sidebar'); const menu=document.getElementById('mobileMenu');
 if(menu&&sidebar){menu.addEventListener('click',()=>{
  const open=sidebar.classList.toggle('open');
  menu.setAttribute('aria-expanded',open?'true':'false');
});document.addEventListener('click',e=>{if(window.innerWidth<=900&&!sidebar.contains(e.target)&&e.target!==menu)sidebar.classList.remove('open');menu.setAttribute('aria-expanded','false');});}
 if(search){search.addEventListener('input',()=>{const q=search.value.toLowerCase();document.querySelectorAll('#dataTable tbody tr').forEach(r=>r.style.display=r.innerText.toLowerCase().includes(q)?'':'none');});}
 async function ajax(action,id,row){const fd=new FormData();fd.append('csrf',token);fd.append('action',action);fd.append('id',id);let url='index.php?page=api/admin';if(action==='delete_availability')url='index.php?page=teacher/delete-availability';try{const res=await fetch(url,{method:'POST',body:fd,headers:{'X-Requested-With':'XMLHttpRequest'}});const data=await res.json();toast(data.message,data.ok);if(data.ok&&row)row.remove();}catch(e){toast('Request failed.',false);}}
 document.querySelectorAll('[data-ajax-action]').forEach(btn=>btn.addEventListener('click',()=>{if(confirm(btn.dataset.confirm||'Are you sure?'))ajax(btn.dataset.ajaxAction,btn.dataset.id,btn.closest('tr'));}));
 document.querySelectorAll('form').forEach(f=>f.addEventListener('submit',e=>{const pwd=f.querySelector('input[type=password]');if(pwd&&pwd.value.length<8){e.preventDefault();toast('Password must contain at least 8 characters.',false);}}));
 document.querySelectorAll('[data-auto-hide],.alert').forEach(a=>setTimeout(()=>a.remove(),4500));
});
function toast(msg,ok=true){let t=document.getElementById('toast');if(!t){t=document.createElement('div');t.id='toast';document.body.appendChild(t);}t.textContent=msg;t.className=ok?'toast ok':'toast bad';setTimeout(()=>t.remove(),2800);}


// Fetch API helper used by the AJAX CRUD endpoints.
async function apiFetch(url, data = {}) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'},
        body: new URLSearchParams(data)
    });
    return response.json();
}
