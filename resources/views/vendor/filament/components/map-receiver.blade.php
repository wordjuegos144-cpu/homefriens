<script>
window.addEventListener('message', function(event) {
    if (event.origin !== window.location.origin) return;
    if (!event.data.address && !event.data.direccion) return;

    // Detectar el campo de dirección en Filament
    const direccionInput = document.querySelector('input[name="data.direccion"], input[name="direccion"], input[name="data[direccion]"]');

    // Actualizar valor de la dirección
    if (direccionInput) {
        direccionInput.value = event.data.address || event.data.direccion;
        // Notificar a Filament (Livewire) que el valor cambió
        direccionInput.dispatchEvent(new Event('input', { bubbles: true }));
        direccionInput.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // Feedback opcional
    alert("✅ Ubicación guardada correctamente:\n" + (event.data.address || event.data.direccion));
});
</script>
