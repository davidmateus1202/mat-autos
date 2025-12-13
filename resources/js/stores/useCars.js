import { defineStore } from 'pinia';
import axios from 'axios';

export const useCarsStore = defineStore('cars', {
    state: () => ({
        cars: [],
        currentCar: null,
        brands: [],
        loading: false,
        errors: {},
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0
        }
    }),
    actions: {
        async fetchCars(params = {}) {
            this.loading = true;
            this.errors = {};
            try {
                // Build query string from params
                const query = new URLSearchParams();
                
                if (params.page) query.set('page', params.page);
                if (params.status) query.set('status', params.status);
                if (params.search) query.set('search', params.search);
                if (params.brand_id && params.brand_id !== 'all') query.set('brand_id', params.brand_id);
                if (params.year_from) query.set('year_from', params.year_from);
                if (params.year_to) query.set('year_to', params.year_to);
                if (params.purchase_date_from) query.set('purchase_date_from', params.purchase_date_from);
                if (params.purchase_date_to) query.set('purchase_date_to', params.purchase_date_to);

                const url = `/api/v1/cars${query.toString() ? '?' + query.toString() : ''}`;
                const response = await axios.get(url);
                const payload = response.data || {};
                
                // Extract data from response
                const items = Array.isArray(payload.data) ? payload.data : (Array.isArray(payload) ? payload : []);
                this.cars = items.map((c) => ({
                    ...c,
                    image_url: c.image_url || (c.images?.[0]?.url || c.images?.[0]?.path) || null
                }));
                
                // Set pagination
                this.pagination = payload.meta || { 
                    current_page: 1, 
                    last_page: 1, 
                    total: this.cars.length 
                };
            } catch (error) {
                console.error('Error fetching cars', error);
                this.errors = { general: ['No se pudieron cargar los vehículos.'] };
            } finally {
                this.loading = false;
            }
        },
        async fetchCar(id) {
            this.loading = true;
            this.errors = {};
            try {
                const response = await axios.get(`/api/v1/cars/${id}`);
                let car = response.data.data || response.data;
                
                // Asegurarse de que images es un array
                if (!car.images) {
                    car.images = [];
                }
                
                // Procesar imágenes para agregar la URL completa si es necesario
                car.images = car.images.map(img => ({
                    ...img,
                    path: img.path || img.url
                }));
                
                this.currentCar = car;
                console.log('Car fetched successfully:', this.currentCar);
            } catch (error) {
                console.error('Error fetching car', error);
                this.errors = { general: ['No se pudo cargar el vehículo. Intenta de nuevo.'] };
                this.currentCar = null;
            } finally {
                this.loading = false;
            }
        },
        async fetchBrands() {
            try {
                const response = await axios.get('/api/v1/brands');
                const payload = response.data || {};
                this.brands = Array.isArray(payload.data) ? payload.data : (Array.isArray(payload) ? payload : []);
            } catch (error) {
                console.error('Error fetching brands', error);
            }
        },
        async createCar(carData) {
            this.loading = true;
            this.errors = {};
            try {
                // If brand_name is provided and no brand_id, create brand first
                if (!carData.brand_id && carData.brand_name) {
                    const brandRes = await axios.post('/api/v1/brands', { name: carData.brand_name });
                    carData.brand_id = brandRes.data.data ? brandRes.data.data.id : brandRes.data.id;
                }

                const formData = new FormData();
                for (const key in carData) {
                    if (carData[key] !== null && key !== 'images' && key !== 'brand_name') {
                        formData.append(key, carData[key]);
                    }
                }
                if (carData.images && Array.isArray(carData.images)) {
                    carData.images.forEach(file => {
                        if (file) formData.append('images[]', file);
                    });
                }
                await axios.post('/api/v1/cars', formData, { headers: { 'Content-Type': 'multipart/form-data' } });
                this.errors = {};
                return true;
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                    console.warn('Errores de validación al crear vehículo', this.errors);
                }
                if (!error.response) {
                    this.errors = { general: ['Error de red o servidor.'] };
                }
                return false;
            } finally {
                this.loading = false;
            }
        },
        async sellCar(carId, saleData) {
            this.loading = true;
            this.errors = {};
            try {
                await axios.post(`/api/v1/cars/${carId}/sale`, saleData);
                return true;
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    this.errors = { general: ['Ocurrió un error al registrar la venta.'] };
                }
                throw error;
            } finally {
                this.loading = false;
            }
        },
        async addExpense(carId, expenseData) {
            this.loading = true;
            this.errors = {};
            try {
                const formData = new FormData();
                for (const key in expenseData) {
                    if (expenseData[key] !== null) {
                        formData.append(key, expenseData[key]);
                    }
                }
                await axios.post(`/api/v1/cars/${carId}/expenses`, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                await this.fetchCar(carId); // Refresh car data
                return true;
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                }
                return false;
            } finally {
                this.loading = false;
            }
        },
        async recordSale(carId, saleData) {
            this.loading = true;
            this.errors = {};
            try {
                await axios.post(`/api/v1/cars/${carId}/sale`, saleData);
                await this.fetchCar(carId);
                return true;
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                }
                return false;
            } finally {
                this.loading = false;
            }
        },
        async updateSalePrice(carId, priceData) {
            this.loading = true;
            this.errors = {};
            try {
                const response = await axios.put(`/api/v1/cars/${carId}`, priceData);
                await this.fetchCar(carId); // Refresh car data
                return true;
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                }
                console.error('Error updating sale price:', error);
                return false;
            } finally {
                this.loading = false;
            }
        }
    }
});
