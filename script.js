// JavaScript para manejar el formulario de contacto
document.getElementById('contactForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const message = document.getElementById('message').value;

    if (name && email && message) {
        alert('Gracias por contactarnos, ' + name + '. Nos pondremos en contacto contigo pronto.');
        document.getElementById('contactForm').reset();
    } else {
        alert('Por favor, completa todos los campos del formulario.');
    }
});