/**
 * JavaScript para el Dashboard de Stock
 * Sistema de gestión de stock para Cake Party
 */

class StockDashboard {
    constructor() {
        this.init();
    }

    init() {
        this.setupNavigation();
        this.setupSidebar();
        this.setupResponsive();
        this.setupAutoRefresh();
        this.setupNotifications();
    }

    setupNavigation() {
        const navItems = document.querySelectorAll('.nav-item');
        const sections = document.querySelectorAll('.content-section');

        navItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Remove active class from all nav items
                navItems.forEach(nav => nav.classList.remove('active'));
                
                // Add active class to clicked item
                item.classList.add('active');
                
                // Hide all sections
                sections.forEach(section => section.classList.remove('active'));
                
                // Show selected section
                const targetSection = item.getAttribute('data-section');
                const targetElement = document.getElementById(targetSection);
                
                if (targetElement) {
                    targetElement.classList.add('active');
                    
                    // Load dynamic content if needed
                    this.loadSectionContent(targetSection);
                }
                
                // Close sidebar on mobile after selection
                if (window.innerWidth <= 768) {
                    document.getElementById('sidebar').classList.remove('open');
                }
            });
        });
    }

    setupSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        
        // Toggle sidebar function
        window.toggleSidebar = () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('open');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
        };
    }

    setupResponsive() {
        window.addEventListener('resize', () => {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            if (window.innerWidth > 768) {
                sidebar.classList.remove('open');
                mainContent.classList.remove('expanded');
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
            }
        });
    }

    setupAutoRefresh() {
        // Auto-refresh alerts every 30 seconds
        setInterval(() => {
            const alertsSection = document.getElementById('alertas');
            if (alertsSection && alertsSection.classList.contains('active')) {
                this.refreshAlerts();
            }
        }, 30000);

        // Auto-refresh stats every 2 minutes
        setInterval(() => {
            const dashboardSection = document.getElementById('dashboard');
            if (dashboardSection && dashboardSection.classList.contains('active')) {
                this.refreshStats();
            }
        }, 120000);
    }

    setupNotifications() {
        // Create notification container if it doesn't exist
        if (!document.getElementById('notification-container')) {
            const container = document.createElement('div');
            container.id = 'notification-container';
            document.body.appendChild(container);
        }
    }

    loadSectionContent(section) {
        switch (section) {
            case 'dashboard':
                this.refreshStats();
                break;
            case 'alertas':
                this.refreshAlerts();
                break;
            case 'movimientos':
                // Load recent movements
                break;
        }
    }

    async refreshStats() {
        try {
            const response = await fetch('../../controllers/stock/load_content.php?section=stats');
            const stats = await response.json();
            
            if (stats) {
                this.updateStatsDisplay(stats);
            }
        } catch (error) {
            console.error('Error refreshing stats:', error);
        }
    }

    async refreshAlerts() {
        try {
            const response = await fetch('../../controllers/stock/load_content.php?section=alertas');
            const alerts = await response.json();
            
            if (alerts) {
                this.updateAlertsDisplay(alerts);
            }
        } catch (error) {
            console.error('Error refreshing alerts:', error);
        }
    }

    updateStatsDisplay(stats) {
        const statCards = document.querySelectorAll('.stat-card');
        
        statCards.forEach(card => {
            const numberElement = card.querySelector('.stat-number');
            if (numberElement) {
                const currentValue = parseInt(numberElement.textContent);
                const newValue = this.getStatValue(card, stats);
                
                if (currentValue !== newValue) {
                    this.animateNumber(numberElement, currentValue, newValue);
                }
            }
        });
    }

    updateAlertsDisplay(alerts) {
        const alertsContainer = document.querySelector('#alertas .alerts-container');
        
        if (!alertsContainer) return;
        
        // Update alert count in header
        const alertTitle = document.querySelector('#alertas .table-title');
        if (alertTitle) {
            alertTitle.textContent = `🚨 Alertas Activas (${alerts.length})`;
        }
        
        // Show/hide no alerts message
        const noDataElement = document.querySelector('#alertas .no-data');
        if (alerts.length === 0 && !noDataElement) {
            alertsContainer.innerHTML = `
                <div class="no-data">
                    <h3>🎉 ¡Excelente!</h3>
                    <p>No hay alertas de stock. Todos los insumos tienen stock suficiente.</p>
                </div>
            `;
        } else if (alerts.length > 0 && noDataElement) {
            noDataElement.remove();
            // Rebuild alerts list
            this.buildAlertsList(alerts, alertsContainer);
        }
    }

    buildAlertsList(alerts, container) {
        const header = document.createElement('div');
        header.className = 'table-header';
        header.innerHTML = `<h3 class="table-title">🚨 Alertas Activas (${alerts.length})</h3>`;
        
        const alertsList = alerts.map(alert => `
            <div class="alert-item">
                <div class="alert-info">
                    <div class="alert-title">${this.escapeHtml(alert.insumo_nombre)}</div>
                    <div class="alert-details">
                        <strong>Stock actual:</strong> ${parseFloat(alert.insumo_stock_actual).toFixed(2)} ${this.escapeHtml(alert.insumo_unidad_medida)}<br>
                        <strong>Stock mínimo:</strong> ${parseFloat(alert.insumo_stock_minimo).toFixed(2)} ${this.escapeHtml(alert.insumo_unidad_medida)}<br>
                        <strong>Proveedor:</strong> ${this.escapeHtml(alert.proveedor_nombre)}
                    </div>
                </div>
                <div>
                    <span class="status-badge status-${alert.estado_stock === 'Sin stock' ? 'sin-stock' : 'bajo'}">
                        ${this.escapeHtml(alert.estado_stock)}
                    </span>
                    <br><br>
                    <a href="../../controllers/stock/ingreso_stock.php?insumo=${alert.ID_insumo}" class="btn btn-success btn-small">
                        ➕ Ingresar
                    </a>
                </div>
            </div>
        `).join('');
        
        container.innerHTML = header.outerHTML + alertsList;
    }

    getStatValue(card, stats) {
        if (card.classList.contains('normal')) return stats.normal;
        if (card.classList.contains('bajo')) return stats.bajo;
        if (card.classList.contains('sin-stock')) return stats.sin_stock;
        if (card.classList.contains('total')) return stats.total;
        return 0;
    }

    animateNumber(element, start, end) {
        const duration = 1000; // 1 second
        const range = end - start;
        const increment = range / (duration / 16); // 60fps
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                current = end;
                clearInterval(timer);
            }
            
            element.textContent = Math.round(current);
        }, 16);
    }

    showNotification(message, type = 'info') {
        const container = document.getElementById('notification-container');
        
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        container.appendChild(notification);
        
        // Show notification
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        // Hide notification after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Utility method to format numbers
    formatNumber(number) {
        return new Intl.NumberFormat('es-AR').format(number);
    }

    // Method to export data (for future use)
    exportToCSV(data, filename) {
        const csv = this.convertToCSV(data);
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }

    convertToCSV(data) {
        if (!data || data.length === 0) return '';
        
        const headers = Object.keys(data[0]);
        const csvContent = [
            headers.join(','),
            ...data.map(row => headers.map(header => row[header]).join(','))
        ].join('\n');
        
        return csvContent;
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new StockDashboard();
});

// Global functions for backward compatibility
function toggleSidebar() {
    if (window.stockDashboard) {
        window.stockDashboard.toggleSidebar();
    }
}

// Make StockDashboard available globally
window.StockDashboard = StockDashboard;
