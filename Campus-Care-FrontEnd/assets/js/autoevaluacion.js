document.addEventListener('DOMContentLoaded', function () {
    const ranges = document.querySelectorAll('.form-range');

    ranges.forEach(range => {
        function updateValue() {
            const valueElement = document.getElementById('value_' + range.id);
            if (valueElement) {
                valueElement.textContent = range.value;
                const percentage = ((range.value - range.min) / (range.max - range.min)) * 100;
                range.style.setProperty('--thumb-position', percentage + '%');
            }
        }

        range.addEventListener('input', updateValue);
        range.addEventListener('change', updateValue);
        updateValue();
    });

    const cards = document.querySelectorAll('.category-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.1) + 's';
        card.classList.add('fade-in');
    });
});

function validateForm() {
    const ranges = document.querySelectorAll('.form-range');
    let isValid = true;

    ranges.forEach(range => {
        if (!range.value || range.value < 1 || range.value > 5) {
            isValid = false;
            range.style.borderColor = '#ff6b6b';
        } else {
            range.style.borderColor = '';
        }
    });

    if (!isValid) {
        alert('Por favor, asegúrate de que todas las categorías tengan una puntuación entre 1 y 5.');
        return false;
    }

    return true;
}
