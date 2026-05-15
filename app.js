document.addEventListener('DOMContentLoaded', () => {

  // Progress Slider
  const slider = document.getElementById('progress-slider');
  if (slider) {
    const output = document.getElementById('progress-out');
    slider.addEventListener('input', () => {
      output.textContent = slider.value + '%';
    });

    slider.addEventListener('change', () => {
      fetch('/dkap/api/progress.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          resource_id: slider.dataset.rid,
          percent: parseInt(slider.value)
        })
      })
      .then(r => r.json())
      .then(data => {
        if (data.ok) {
          document.querySelector('.progress > div').style.width = slider.value + '%';
        } else {
          alert(data.error || "Failed to save progress");
        }
      })
      .catch(() => alert("Connection error"));
    });
  }

  // Bookmark Toggle
  document.querySelectorAll('[data-bookmark]').forEach(btn => {
    btn.addEventListener('click', () => {
      fetch('/dkap/api/bookmark.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ resource_id: btn.dataset.bookmark })
      })
      .then(r => r.json())
      .then(data => {
        if (data.ok) {
          btn.textContent = data.saved ? '★ Saved' : '☆ Save';
        }
      });
    });
  });
});