// ─── STEP NAVIGATION ─────────────────────────────────────
function goStep(n) {
  document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
  document.getElementById('step' + n).classList.add('active');
  document.querySelector('.step[data-step="' + n + '"]').classList.add('active');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}