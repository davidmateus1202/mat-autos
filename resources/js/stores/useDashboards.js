import { defineStore } from 'pinia';
import axios from 'axios';

const createDefaultSummary = () => ({
    available_cash: 0,
    invested_assets: 0,
    cars_sold_month: 0,
    gross_profit_month: 0,
    net_profit_month: 0,
    expenses_month: 0,
    inflows_month: 0,
    outflows_month: 0,
    sales_revenue_month: 0,
    average_sale_ticket_month: 0,
    available_cars: 0,
    sold_cars_total: 0,
    reserved_cars: 0,
    estimated_inventory_value: 0,
    potential_inventory_profit: 0,
    active_loans: 0,
    current_debt: 0,
    available_credit: 0,
    status_breakdown: [],
    top_inventory: [],
    recent_sales: [],
    recent_expenses: []
});

export const useDashboardsStore = defineStore('dashboards', {
    state: () => ({
        summary: createDefaultSummary(),
        salesByBrand: [],
        monthlyStats: [],
        loading: false
    }),
    actions: {
        async fetchSummary() {
            try {
                const response = await axios.get('/api/v1/dashboards/summary');
                this.summary = {
                    ...createDefaultSummary(),
                    ...response.data,
                };
            } catch (error) {
                console.error('Error fetching summary', error);
            }
        },
        async fetchSalesByBrand() {
            try {
                const response = await axios.get('/api/v1/dashboards/sales-by-brand');
                this.salesByBrand = Array.isArray(response.data) ? response.data : [];
            } catch (error) {
                console.error('Error fetching sales by brand', error);
            }
        },
        async fetchMonthlyStats(months = 12) {
            try {
                const response = await axios.get(`/api/v1/dashboards/monthly-stats?months=${months}`);
                this.monthlyStats = Array.isArray(response.data) ? response.data : [];
            } catch (error) {
                console.error('Error fetching monthly stats', error);
            }
        },
        async fetchAll() {
            this.loading = true;
            try {
                await Promise.all([
                    this.fetchSummary(),
                    this.fetchSalesByBrand(),
                    this.fetchMonthlyStats(8)
                ]);
            } finally {
                this.loading = false;
            }
        }
    }
});
