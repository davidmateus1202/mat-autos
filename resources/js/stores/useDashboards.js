import { defineStore } from 'pinia';
import axios from 'axios';

export const useDashboardsStore = defineStore('dashboards', {
    state: () => ({
        summary: {
            available_cash: 0,
            invested_assets: 0,
            cars_sold_month: 0,
            gross_profit_month: 0,
            net_profit_month: 0
        },
        salesByBrand: [],
        monthlyStats: [],
        loading: false
    }),
    actions: {
        async fetchSummary() {
            this.loading = true;
            try {
                const response = await axios.get('/api/v1/dashboards/summary');
                this.summary = response.data;
            } catch (error) {
                console.error('Error fetching summary', error);
            } finally {
                this.loading = false;
            }
        },
        async fetchSalesByBrand() {
            try {
                const response = await axios.get('/api/v1/dashboards/sales-by-brand');
                this.salesByBrand = response.data;
            } catch (error) {
                console.error('Error fetching sales by brand', error);
            }
        },
        async fetchMonthlyStats(months = 12) {
            try {
                const response = await axios.get(`/api/v1/dashboards/monthly-stats?months=${months}`);
                this.monthlyStats = response.data;
            } catch (error) {
                console.error('Error fetching monthly stats', error);
            }
        },
        async fetchAll() {
            this.loading = true;
            await Promise.all([
                this.fetchSummary(),
                this.fetchSalesByBrand(),
                this.fetchMonthlyStats()
            ]);
            this.loading = false;
        }
    }
});
