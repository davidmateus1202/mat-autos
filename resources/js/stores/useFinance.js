import { defineStore } from 'pinia';
import axios from 'axios';

export const useFinanceStore = defineStore('finance', {
    state: () => ({
        accounts: [],
        loans: [],
        loading: false,
        errors: {}
    }),
    actions: {
        // Actions for financial accounts
        async fetchAccounts() {
            this.loading = true;
            try {
                const response = await axios.get('/api/v1/financial-accounts');
                this.accounts = response.data.data || [];
            } catch (error) {
                console.error('Error fetching accounts', error);
                this.accounts = [];
            } finally {
                this.loading = false;
            }
        },
        async createAccount(accountData) {
            this.loading = true;
            this.errors = {};
            try {
                await axios.post('/api/v1/financial-accounts', accountData);
                await this.fetchAccounts();
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
        async fetchAccountDetails(accountId) {
            try {
                const response = await axios.get(`/api/v1/financial-accounts/${accountId}`);
                return response.data;
            } catch (error) {
                console.error('Error fetching account details', error);
                return null;
            }
        },
        async adjustAccountBalance(accountId, data) {
            this.loading = true;
            this.errors = {};
            try {
                await axios.post(`/api/v1/financial-accounts/${accountId}/adjust`, data);
                await this.fetchAccounts();
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
        async fetchLoans() {
            this.loading = true;
            try {
                const response = await axios.get('/api/v1/bank-loans');
                this.loans = response.data.data || [];
            } catch (error) {
                console.error('Error fetching loans', error);
                this.loans = [];
            } finally {
                this.loading = false;
            }
        },
        async createLoan(loanData) {
            this.loading = true;
            this.errors = {};
            try {
                await axios.post('/api/v1/bank-loans', loanData);
                await this.fetchLoans();
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
        async disburseLoan(loanId, data) {
            this.loading = true;
            try {
                await axios.post(`/api/v1/bank-loans/${loanId}/disburse`, data);
                await this.fetchLoans();
                await this.fetchAccounts(); // Refresh accounts to show new balance
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
        async payLoan(loanId, paymentData) {
            this.loading = true;
            try {
                await axios.post(`/api/v1/bank-loans/${loanId}/payments`, paymentData);
                await this.fetchLoans();
                await this.fetchAccounts(); // Refresh accounts to show updated balance
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
        async fetchLoanDetails(loanId) {
            this.loading = true;
            try {
                const response = await axios.get(`/api/v1/bank-loans/${loanId}`);
                return response.data;
            } catch (error) {
                console.error('Error fetching loan details', error);
                return null;
            } finally {
                this.loading = false;
            }
        }
    }
});
