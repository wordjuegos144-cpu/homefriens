<div x-data="locationPicker()" class="filament-forms-field-wrapper">
    <button type="button" x-on:click="open()" class="filament-button filament-button-size-sm filament-button-color-primary" style="display:inline-flex;align-items:center;">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4.438 8-10a8 8 0 10-16 0c0 5.562 8 10 8 10z"/></svg>
        Seleccionar en mapa
    </button>

    <!-- Modal -->
    <div x-show="openModal" x-cloak style="display:none;" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" x-on:click="close()"></div>
        <div class="bg-white rounded-lg shadow-lg p-4 w-11/12 md:w-3/4 lg:w-2/3 z-50">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold">Seleccionar ubicación</h3>
                <button type="button" x-on:click="close()" class="text-gray-600">Cerrar</button>
            </div>

            <input id="location-search" class="block w-full border rounded px-2 py-1 mb-2" placeholder="Buscar lugar o dirección" />
            <div id="location-map" style="height:400px;" class="mb-2"></div>

            <div class="flex items-center justify-end gap-2">
                <button type="button" x-on:click="confirm()" class="filament-button filament-button-size-sm filament-button-color-primary">Confirmar</button>
                <button type="button" x-on:click="close()" class="filament-button filament-button-size-sm">Cancelar</button>
            </div>
        </div>
    </div>

    <script>
        function locationPicker() {
            return {
                openModal: false,
                map: null,
                marker: null,
                geocoder: null,
                autocomplete: null,
                open() {
                    this.openModal = true;
                    this.$nextTick(() => this.initMap());
                },
                close() {
                    this.openModal = false;
                },
                initMap() {
                    if (this.map) return;

                    // Load Google Maps script if not loaded
                    if (!window.google || !window.google.maps) {
                        const script = document.createElement('script');
                        script.src = 'https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAPS_API_KEY") }}&libraries=places';
                        script.defer = true;
                        script.onload = () => this._init();
                        document.head.appendChild(script);
                    } else {
                        this._init();
                    }
                },
                _init() {
                    const defaultLatLng = { lat: -17.7863, lng: -63.1812 }; // Centro por defecto (puedes cambiarlo)
                    this.map = new google.maps.Map(document.getElementById('location-map'), {
                        center: defaultLatLng,
                        zoom: 13
                    });

                    this.marker = new google.maps.Marker({ map: this.map, draggable: true });
                    this.geocoder = new google.maps.Geocoder();

                    // Search box
                    const input = document.getElementById('location-search');
                    this.autocomplete = new google.maps.places.SearchBox(input);
                    this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                    this.map.addListener('click', (e) => {
                        this.setMarker(e.latLng);
                    });

                    this.autocomplete.addListener('places_changed', () => {
                        const places = this.autocomplete.getPlaces();
                        if (!places || !places.length) return;
                        const place = places[0];
                        if (place.geometry && place.geometry.location) {
                            this.map.panTo(place.geometry.location);
                            this.map.setZoom(15);
                            this.setMarker(place.geometry.location);
                        }
                    });

                    // If direccion input has value, try to geocode and set marker
                    const direccionInput = document.getElementById('direccion');
                    if (direccionInput && direccionInput.value) {
                        this.geocoder.geocode({ address: direccionInput.value }, (results, status) => {
                            if (status === 'OK' && results[0]) {
                                this.map.panTo(results[0].geometry.location);
                                this.map.setZoom(15);
                                this.setMarker(results[0].geometry.location);
                            }
                        });
                    }

                    // When marker dragged, update position
                    this.marker.addListener('dragend', () => {
                        const pos = this.marker.getPosition();
                        this.map.panTo(pos);
                        this.updateAddressFromLatLng(pos);
                    });
                },
                setMarker(latLng) {
                    this.marker.setPosition(latLng);
                    this.marker.setMap(this.map);
                    this.updateAddressFromLatLng(latLng);
                },
                updateAddressFromLatLng(latLng) {
                    this.geocoder.geocode({ location: latLng }, (results, status) => {
                        if (status === 'OK' && results[0]) {
                            const formatted = results[0].formatted_address;
                            // set the direccion input value
                            const input = document.getElementById('direccion');
                            if (input) {
                                input.value = formatted;
                                input.dispatchEvent(new Event('input', { bubbles: true }));
                            }
                        }
                    });
                },
                confirm() {
                    // On confirm, ensure direccion input is filled (marker already sets it). Then close modal.
                    this.close();
                }
            }
        }
    </script>
</div>
