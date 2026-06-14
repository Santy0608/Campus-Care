document.addEventListener('DOMContentLoaded', function() {
  fetch('frase-del-dia.php')
    .then(response => response.json())
    .then(data => {
      document.getElementById('frase-dia').textContent = data.frase;
    })
    .catch(error => {
      console.error('Error al cargar la frase del día:', error);
      document.getElementById('frase-dia').textContent = 'No se pudo cargar la frase del día.';
    });
});
