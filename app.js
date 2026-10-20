document.addEventListener('submit', () => {
  const status = document.querySelector('.loading');
  if (status) status.hidden = false;
});

const langSelect = document.getElementById('language');
if (langSelect) {
  const applyBtn = langSelect.form ? langSelect.form.querySelector('button[type="submit"]') : null;
  if (applyBtn) applyBtn.style.display = 'none';

  langSelect.addEventListener('change', () => {
    if (typeof langSelect.form.requestSubmit === 'function') {
      langSelect.form.requestSubmit();
    } else {
      langSelect.form.submit();
    }
  });
}
