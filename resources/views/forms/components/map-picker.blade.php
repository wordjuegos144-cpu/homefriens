<div
    x-data="{
        map: null,
        marker: null,
        address: null,
        init() {
            // Cargar el script de Google Maps
            if (typeof google === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places';
                script.async = true;
                script.defer = true;
                script.addEventListener('load', () => this.initMap());
                document.head.appendChild(script);
            } else {
                this.initMap();
            }
        },
        initMap() {
            // Inicializar el mapa
            const defaultLocation = { lat: -17.783330, lng: -63.182126 }; // Santa Cruz de la Sierra
            this.map = new google.maps.Map(this.$refs.map, {
                center: defaultLocation,
                zoom: 13,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false
            });

            // Crear el marcador
            this.marker = new google.maps.Marker({
                position: defaultLocation,
                map: this.map,
                draggable: true
            });

            // Geocoder para obtener la dirección
            const geocoder = new google.maps.Geocoder();

            // Evento cuando se arrastra el marcador
            google.maps.event.addListener(this.marker, 'dragend', () => {
                const position = this.marker.getPosition();
                geocoder.geocode({ location: position }, (results, status) => {
                    if (status === 'OK') {
                        if (results[0]) {
                            this.address = results[0].formatted_address;
                        }
                    }
                });
            });

            // Si ya hay una dirección, centrar el mapa allí
            const currentAddress = $wire.get('data.direccion');
            if (currentAddress) {
                geocoder.geocode({ address: currentAddress }, (results, status) => {
                    if (status === 'OK') {
                        const location = results[0].geometry.location;
                        this.map.setCenter(location);
                        this.marker.setPosition(location);
                        this.address = results[0].formatted_address;
                    }
                });
            }
        },
        saveLocation() {
            if (this.address) {
                // Disparar evento personalizado con la dirección
                window.dispatchEvent(new CustomEvent('map-saved', {
                    detail: { address: this.address }
                }));
                // Cerrar el modal
                $wire.set('data.showMap', false);
            }
        }
    }"
    class="relative w-full"
    style="height: 400px;"
>
    <div x-ref="map" class="w-full h-full rounded-lg"></div>
    <div class="absolute bottom-4 left-4 right-4 bg-white p-4 rounded-lg shadow-lg">
        <div class="text-sm mb-2" x-show="address" x-text="address"></div>
        <button
            type="button"
            x-show="address"
            @click="saveLocation"
            class="w-full px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
        >
            Guardar esta ubicación
        </button>
    </div>
</div>