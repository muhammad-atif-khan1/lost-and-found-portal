

document.addEventListener('DOMContentLoaded', ()=>{
const form = document.getElementById('itemForm');
if(!form) return;
form.addEventListener('submit', (e)=>{
const title = form.querySelector('input[name=title]').value.trim();
const loc = form.querySelector('input[name=location]').value.trim();
if(!title || !loc){
alert('Title and Location are required'); e.preventDefault(); return false;
}
const file = form.querySelector('input[name=image]').files[0];
if(file && file.size > 2*1024*1024){ alert('Image too large (max 2MB)'); e.preventDefault(); }
});
});
