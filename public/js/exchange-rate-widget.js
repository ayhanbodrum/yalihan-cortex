/**
 * Exchange Rate Widget
 * 
 * Context7: TCMB real-time currency widget for international listings
 * Usage: Alpine.js component
 */

function exchangeRateWidget() {
    return {
        rates: [],
        selectedCurrency: 'USD',
        amount: 1,
        convertedAmount: 0,
        loading: false,
        lastUpdate: null,
        error: null,
        
        init() {
            this.loadRates();
            // Auto-refresh every 1 hour
            setInterval(() => this.loadRates(), 3600000);
        },
        
        async loadRates() {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await fetch('/api/exchange-rates');
                const data = await response.json();
                
                if (data.success) {
                    this.rates = Object.values(data.data);
                    this.lastUpdate = data.updated_at;
                    
                    // Auto calculate if amount is set
                    if (this.amount > 0) {
                        this.calculateConversion();
                    }
                } else {
                    this.error = 'Kurlar yüklenemedi';
                }
            } catch (error) {
                console.error('Exchange rate error:', error);
                this.error = 'Bağlantı hatası';
            } finally {
                this.loading = false;
            }
        },
        
        async calculateConversion() {
            if (!this.amount || this.amount <= 0) {
                this.convertedAmount = 0;
                return;
            }
            
            try {
                const response = await fetch('/api/exchange-rates/convert', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({
                        amount: this.amount,
                        from: this.selectedCurrency,
                        to: 'TRY'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.convertedAmount = data.data.result;
                }
            } catch (error) {
                console.error('Conversion error:', error);
            }
        },
        
        getRate(currencyCode) {
            const rate = this.rates.find(r => r.code === currencyCode);
            return rate ? rate.forex_selling : 0;
        },
        
        formatCurrency(amount, currency = 'TRY') {
            const symbols = {
                'TRY': '₺',
                'USD': '$',
                'EUR': '€',
                'GBP': '£',
                'CHF': 'CHF',
                'CAD': 'C$',
                'AUD': 'A$',
                'JPY': '¥'
            };
            
            return `${symbols[currency] || currency} ${Number(amount).toFixed(2)}`;
        },
        
        getRateChange(rate) {
            // Compare buying vs selling for trend indication
            const diff = rate.forex_selling - rate.forex_buying;
            const percent = (diff / rate.forex_buying) * 100;
            return percent.toFixed(2);
        }
    };
}

// Global helper function for inline usage
window.exchangeRateWidget = exchangeRateWidget;



