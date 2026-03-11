/**
 * PAX Data Visualization Script
 * 
 * Handles interactive charts with lazy loading and filtering
 */

(function($) {
    'use strict';

    let paxChart = null;
    let currentChartType = 'line';
    let chartData = [];
    let chartReady = false; // Flag to track if chart is initialized
    let chartSettings = {
        colorScheme: 'default',
        animation: true,
        legend: true,
        grid: true
    };
    let isDarkMode = false;

    // Detect theme changes
    function detectTheme() {
        isDarkMode = document.documentElement.getAttribute('data-theme') === 'dark';
        return isDarkMode;
    }

    // Watch for theme changes
    const themeObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'data-theme') {
                detectTheme();
                if (paxChart) {
                    updateChart(); // Redraw chart with new theme colors
                }
            }
        });
    });

    themeObserver.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-theme']
    });

    // Debounce function for performance
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = function() {
                clearTimeout(timeout);
                const index = debounceTimeouts.indexOf(timeout);
                if (index > -1) {
                    debounceTimeouts.splice(index, 1);
                }
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            debounceTimeouts.push(timeout);
            return timeout;
        };
    }

    // Cache for AJAX requests
    const dataCache = new Map();
    const MAX_CACHE_SIZE = 50;
    function getCacheKey(filters) {
        return JSON.stringify(filters);
    }

    // Request cancellation
    let currentRequest = null;
    
    // Debounce timeout tracking for cleanup
    const debounceTimeouts = [];
    
    // RAF-based chart update queue
    let chartUpdateScheduled = false;
    let pendingChartUpdate = null;

    // Color schemes
    const colorSchemes = {
        default: {
            primary: ['#003366', '#D4AF37', '#C9A961', '#0A1628', '#1A1A2E'],
            secondary: ['rgba(0, 51, 102, 0.5)', 'rgba(212, 175, 55, 0.5)', 'rgba(201, 169, 97, 0.5)']
        },
        blue: {
            primary: ['#1e3a8a', '#3b82f6', '#60a5fa', '#93c5fd', '#dbeafe'],
            secondary: ['rgba(30, 58, 138, 0.5)', 'rgba(59, 130, 246, 0.5)', 'rgba(96, 165, 250, 0.5)']
        },
        green: {
            primary: ['#14532d', '#16a34a', '#22c55e', '#86efac', '#dcfce7'],
            secondary: ['rgba(20, 83, 45, 0.5)', 'rgba(22, 163, 74, 0.5)', 'rgba(34, 197, 94, 0.5)']
        },
        warm: {
            primary: ['#dc2626', '#f59e0b', '#fbbf24', '#fcd34d', '#fef3c7'],
            secondary: ['rgba(220, 38, 38, 0.5)', 'rgba(245, 158, 11, 0.5)', 'rgba(251, 191, 36, 0.5)']
        },
        cool: {
            primary: ['#0891b2', '#06b6d4', '#22d3ee', '#67e8f9', '#cffafe'],
            secondary: ['rgba(8, 145, 178, 0.5)', 'rgba(6, 182, 212, 0.5)', 'rgba(34, 211, 238, 0.5)']
        }
    };

    /**
     * Initialize the visualization
     */
    function init() {
        // Detect initial theme
        detectTheme();
        
        // Lazy load chart when container is in viewport
        setupLazyLoading();
        
        // Setup event listeners
        setupEventListeners();
        
        // Don't load data until chart is initialized
    }

    /**
     * Setup lazy loading using Intersection Observer
     */
    function setupLazyLoading() {
        const chartContainer = $('.pax-chart-container');
        
        if (!chartContainer.length || !chartContainer.attr('data-lazy-load')) {
            return;
        }

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    // Container is in viewport, initialize chart
                    initializeChart();
                    observer.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '100px' // Start loading 100px before visible
        });

        observer.observe(chartContainer[0]);
    }

    /**
     * Initialize Chart.js chart
     */
    function initializeChart() {
        const ctx = document.getElementById('pax-chart');
        
        if (!ctx) {
            return;
        }

        // Show canvas, hide loading
        $('.chart-loading').fadeOut();
        $(ctx).fadeIn();
        
        // Mark chart as ready and load initial data
        chartReady = true;
        loadChartData();
    }

    /**
     * Setup event listeners
     */
    function setupEventListeners() {
        // Chart type switcher
        $('.chart-type-btn').on('click', function() {
            $('.chart-type-btn').removeClass('active');
            $(this).addClass('active');
            currentChartType = $(this).data('chart-type');
            updateChart();
        });

        // Mobile filter toggle
        if (window.innerWidth <= 768) {
            $('.filter-header').on('click', function() {
                $(this).toggleClass('collapsed');
                $('.pax-filters').toggleClass('collapsed').slideToggle(300);
            });
        }

        // Apply filters button
        $('#apply-filters').on('click', function() {
            // Clear previous validation errors
            $('.filter-group').removeClass('error');
            
            // Validate year range
            const yearFrom = $('#filter-year-from').val();
            const yearTo = $('#filter-year-to').val();
            
            if (yearFrom && yearTo && parseInt(yearFrom) > parseInt(yearTo)) {
                // Show validation feedback
                $('#filter-year-from').closest('.filter-group').addClass('error');
                $('#filter-year-to').closest('.filter-group').addClass('error');
                
                // Show alert
                alert('"Year From" must be less than or equal to "Year To"');
                $('#filter-year-from').focus();
                return;
            }
            
            const $btn = $(this);
            // Disable button and show loading state
            $btn.prop('disabled', true).addClass('loading');
            const originalText = $btn.html();
            $btn.html('<span class="spinner"></span> Loading...');
            
            // Store original text for restoration
            $btn.data('original-text', originalText);
            
            loadChartData();
        });

        // Clear filters
        $('#clear-filters').on('click', function() {
            const $btn = $(this);
            
            // Clear all filter values
            $('#filter-iata').val('');
            $('#filter-icao').val('');
            $('#filter-pax-type').val('');
            $('#filter-year-from').val('');
            $('#filter-year-to').val('');
            $('#filter-month').val('');
            
            // Add loading state
            $btn.prop('disabled', true).addClass('loading');
            const originalText = $btn.html();
            $btn.html('<span class="spinner"></span> Clearing...');
            $btn.data('original-text', originalText);
            
            loadChartData();
        });

        // Chart settings toggle
        $('#chart-settings-toggle').on('click', function() {
            $('#chart-settings-panel').slideToggle(300);
        });

        // Apply chart settings
        $('#apply-chart-settings').on('click', function() {
            chartSettings.colorScheme = $('#chart-color-scheme').val();
            chartSettings.animation = $('#chart-animation').is(':checked');
            chartSettings.legend = $('#chart-legend').is(':checked');
            chartSettings.grid = $('#chart-grid').is(':checked');
            updateChart();
        });
    }

    /**
     * Load chart data via AJAX
     */
    function loadChartData() {
        const filters = {
            action: 'get_pax_data',
            nonce: paxData.nonce,
            iata_code: $('#filter-iata').val(),
            icao_code: $('#filter-icao').val(),
            pax_type: $('#filter-pax-type').val(),
            year_from: $('#filter-year-from').val(),
            year_to: $('#filter-year-to').val(),
            month: $('#filter-month').val()
        };

        const cacheKey = getCacheKey(filters);
        
        // Check cache first
        if (dataCache.has(cacheKey)) {
            chartData = dataCache.get(cacheKey);
            scheduleChartUpdate();
            resetApplyButton();
            return;
        }

        // Cancel previous request if still pending
        if (currentRequest && currentRequest.abort) {
            currentRequest.abort();
        }

        $('.chart-loading').fadeIn();
        
        currentRequest = $.ajax({
            url: paxData.ajaxUrl,
            type: 'POST',
            data: filters,
            timeout: 30000, // 30 second timeout
            success: function(response) {
                currentRequest = null;
                
                if (response.success) {
                    chartData = response.data.data;
                    
                    // Cache management - remove oldest if cache is full
                    if (dataCache.size >= MAX_CACHE_SIZE) {
                        const firstKey = dataCache.keys().next().value;
                        dataCache.delete(firstKey);
                    }
                    dataCache.set(cacheKey, chartData);
                    
                    scheduleChartUpdate();
                } else {
                    // Check for nonce failure
                    if (response.data && response.data.includes && response.data.includes('nonce')) {
                        console.error('Session expired. Please reload the page.');
                        alert('Your session has expired. Please reload the page.');
                    } else {
                        console.error('Failed to load chart data:', response.data);
                    }
                }
                $('.chart-loading').fadeOut();
                resetApplyButton();
            },
            error: function(xhr) {
                if (xhr.statusText !== 'abort') {
                    currentRequest = null;
                    console.error('AJAX error loading chart data');
                    $('.chart-loading').fadeOut();
                    resetApplyButton();
                }
            }
        });
    }

    /**
     * Reset apply and clear buttons to normal state
     */
    function resetApplyButton() {
        // Reset Apply button
        const $applyBtn = $('#apply-filters');
        const applyOriginalText = $applyBtn.data('original-text') || 'Apply Filters';
        $applyBtn.prop('disabled', false).removeClass('loading').html(applyOriginalText);
        
        // Reset Clear button
        const $clearBtn = $('#clear-filters');
        const clearOriginalText = $clearBtn.data('original-text') || 'Clear';
        $clearBtn.prop('disabled', false).removeClass('loading').html(clearOriginalText);
    }

    /**
     * Schedule chart update using RAF for smooth animation
     */
    function scheduleChartUpdate() {
        if (!chartUpdateScheduled) {
            chartUpdateScheduled = true;
            requestAnimationFrame(function() {
                processChartData();
                updateChart();
                updateSummary();
                chartUpdateScheduled = false;
            });
        }
    }

    /**
     * Process chart data based on chart type
     */
    function processChartData() {
        // Data is already loaded in chartData variable
        // Additional processing can be done here if needed
    }

    /**
     * Update chart with current data and settings
     */
    function updateChart() {
        // Check if Chart.js library is loaded
        if (typeof Chart === 'undefined') {
            console.error('Chart.js library not loaded');
            return;
        }
        
        // Check if chart is ready
        if (!chartReady) {
            return;
        }
        
        const ctx = document.getElementById('pax-chart');
        
        if (!ctx) {
            return;
        }

        // Optimize: Update existing chart data instead of destroying when only data changes
        if (paxChart && currentChartType === paxChart.config.type) {
            // Just update the data
            const newConfig = getChartConfigForType(currentChartType);
            paxChart.data = newConfig.data;
            paxChart.options = newConfig.options;
            paxChart.update('none'); // Update without animation for performance
            return;
        }

        // Destroy existing chart only when changing chart type
        if (paxChart) {
            paxChart.destroy();
        }

        // Prepare data based on chart type
        let chartConfig = getChartConfigForType(currentChartType);

        // Create new chart
        paxChart = new Chart(ctx, chartConfig);
    }
    
    /**
     * Get chart configuration for a specific type
     */
    function getChartConfigForType(type) {
        switch (type) {
            case 'line':
                return getLineChartConfig();
            case 'bar':
                return getBarChartConfig();
            case 'pie':
                return getPieChartConfig();
            default:
                return getLineChartConfig();
        }
    }

    /**
     * Get Line Chart configuration
     */
    function getLineChartConfig() {
        const colors = colorSchemes[chartSettings.colorScheme];
        
        // Group data by PAX type
        const datasets = [];
        const groupedData = {};
        const labels = [];
        
        chartData.forEach(function(record) {
            const label = record.year + ' ' + record.month;
            
            if (!labels.includes(label)) {
                labels.push(label);
            }
            
            if (!groupedData[record.pax_type]) {
                groupedData[record.pax_type] = {};
            }
            
            groupedData[record.pax_type][label] = parseInt(record.passengers);
        });

        // Create datasets
        let colorIndex = 0;
        Object.keys(groupedData).forEach(function(paxType) {
            const data = labels.map(function(label) {
                return groupedData[paxType][label] || 0;
            });
            
            datasets.push({
                label: paxType,
                data: data,
                borderColor: colors.primary[colorIndex % colors.primary.length],
                backgroundColor: colors.secondary[colorIndex % colors.secondary.length],
                borderWidth: 2,
                tension: 0.4,
                fill: true
            });
            
            colorIndex++;
        });

        return {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: getChartOptions('Line Chart')
        };
    }

    /**
     * Get Bar Chart configuration
     */
    function getBarChartConfig() {
        const colors = colorSchemes[chartSettings.colorScheme];
        
        // Group data by airport or year
        const labels = [];
        const dataValues = [];
        const backgroundColors = [];
        
        chartData.forEach(function(record, index) {
            const label = record.airport_name + ' (' + record.year + ')';
            labels.push(label);
            dataValues.push(parseInt(record.passengers));
            backgroundColors.push(colors.primary[index % colors.primary.length]);
        });

        return {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Passengers',
                    data: dataValues,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors,
                    borderWidth: 1
                }]
            },
            options: getChartOptions('Bar Chart')
        };
    }

    /**
     * Get Pie Chart configuration
     */
    function getPieChartConfig() {
        const colors = colorSchemes[chartSettings.colorScheme];
        
        // Aggregate data by PAX type or airport
        const aggregated = {};
        
        chartData.forEach(function(record) {
            const key = record.pax_type || record.airport_name;
            
            if (!aggregated[key]) {
                aggregated[key] = 0;
            }
            
            aggregated[key] += parseInt(record.passengers);
        });

        const labels = Object.keys(aggregated);
        const dataValues = Object.values(aggregated);
        const backgroundColors = labels.map(function(label, index) {
            return colors.primary[index % colors.primary.length];
        });

        return {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: backgroundColors,
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: getChartOptions('Pie Chart')
        };
    }

    /**
     * Get common chart options
     */
    function getChartOptions(title) {
        const textColor = isDarkMode ? 'rgba(250, 248, 245, 0.9)' : '#1A1A2E';
        const gridColor = isDarkMode ? 'rgba(212, 175, 55, 0.1)' : 'rgba(0, 0, 0, 0.05)';
        const backgroundColor = isDarkMode ? 'rgba(10, 22, 40, 0.9)' : 'rgba(0, 0, 0, 0.8)';
        
        return {
            responsive: true,
            maintainAspectRatio: false,
            animation: chartSettings.animation ? {
                duration: 750,
                easing: 'easeInOutQuart'
            } : false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: chartSettings.legend,
                    position: 'top',
                    labels: {
                        font: {
                            family: 'Inter, sans-serif',
                            size: 12
                        },
                        color: textColor,
                        padding: 15,
                        usePointStyle: true
                    }
                },
                title: {
                    display: true,
                    text: title,
                    font: {
                        family: 'Playfair Display, serif',
                        size: 20,
                        weight: 'bold'
                    },
                    color: textColor,
                    padding: 20
                },
                tooltip: {
                    backgroundColor: backgroundColor,
                    titleColor: 'rgba(212, 175, 55, 1)',
                    bodyColor: 'rgba(250, 248, 245, 0.9)',
                    borderColor: 'rgba(212, 175, 55, 0.3)',
                    borderWidth: 1,
                    padding: 12,
                    titleFont: {
                        size: 14,
                        family: 'Inter, sans-serif',
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13,
                        family: 'Lora, serif'
                    },
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += new Intl.NumberFormat().format(context.parsed.y || context.parsed);
                            return label;
                        }
                    }
                }
            },
            scales: currentChartType !== 'pie' ? {
                x: {
                    grid: {
                        display: chartSettings.grid,
                        color: gridColor
                    },
                    ticks: {
                        font: {
                            family: 'Inter, sans-serif',
                            size: 11
                        },
                        color: textColor
                    }
                },
                y: {
                    grid: {
                        display: chartSettings.grid,
                        color: gridColor
                    },
                    ticks: {
                        font: {
                            family: 'Inter, sans-serif',
                            size: 11
                        },
                        color: textColor,
                        callback: function(value) {
                            return new Intl.NumberFormat().format(value);
                        }
                    }
                }
            } : {}
        };
    }

    /**
     * Update data summary with comprehensive statistics
     */
    function updateSummary() {
        if (!chartData || chartData.length === 0) {
            resetSummary();
            return;
        }

        const totalRecords = chartData.length;
        let totalPassengers = 0;
        const monthlyData = {};
        const airportData = {};
        const paxTypeData = { 'INTERNATIONAL PASSENGERS': 0, 'DOMESTIC PASSENGERS': 0 };
        const yearlyData = {};
        
        // Aggregate data
        chartData.forEach(function(record) {
            const passengers = parseInt(record.passengers, 10) || 0;
            
            // Total passengers
            if (totalPassengers < Number.MAX_SAFE_INTEGER - passengers) {
                totalPassengers += passengers;
            }
            
            // Monthly aggregation
            const monthKey = record.month || 'Unknown';
            monthlyData[monthKey] = (monthlyData[monthKey] || 0) + passengers;
            
            // Airport aggregation
            const airportKey = record.airport_name || 'Unknown';
            airportData[airportKey] = (airportData[airportKey] || 0) + passengers;
            
            // Passenger type aggregation
            const paxType = record.pax_type || 'Unknown';
            if (paxTypeData[paxType] !== undefined) {
                paxTypeData[paxType] += passengers;
            }
            
            // Yearly aggregation for growth calculation
            const year = record.year || 'Unknown';
            if (!yearlyData[year]) {
                yearlyData[year] = { total: 0, count: 0 };
            }
            yearlyData[year].total += passengers;
            yearlyData[year].count++;
        });
        
        // Calculate statistics
        const avgPassengers = totalRecords > 0 ? Math.round(totalPassengers / totalRecords) : 0;
        
        // Peak month
        let peakMonth = '-';
        let peakPassengers = 0;
        Object.keys(monthlyData).forEach(function(month) {
            if (monthlyData[month] > peakPassengers) {
                peakPassengers = monthlyData[month];
                peakMonth = month;
            }
        });
        
        // Busiest airport
        let busiestAirport = '-';
        let busiestCount = 0;
        Object.keys(airportData).forEach(function(airport) {
            if (airportData[airport] > busiestCount) {
                busiestCount = airportData[airport];
                busiestAirport = airport;
            }
        });
        
        // Passenger type split
        const intlPax = paxTypeData['INTERNATIONAL PASSENGERS'];
        const domPax = paxTypeData['DOMESTIC PASSENGERS'];
        const totalTypedPax = intlPax + domPax;
        let paxSplit = '-';
        let paxSplitSubtitle = '-';
        
        if (totalTypedPax > 0) {
            const intlPercent = Math.round((intlPax / totalTypedPax) * 100);
            const domPercent = 100 - intlPercent;
            paxSplit = intlPercent + '% Intl';
            paxSplitSubtitle = domPercent + '% Domestic';
        }
        
        // Growth rate calculation
        const years = Object.keys(yearlyData).sort();
        let growthRate = '-';
        let growthPeriod = 'No data';
        
        if (years.length >= 2) {
            const firstYear = years[0];
            const lastYear = years[years.length - 1];
            const firstYearTotal = yearlyData[firstYear].total;
            const lastYearTotal = yearlyData[lastYear].total;
            
            if (firstYearTotal > 0) {
                const growth = ((lastYearTotal - firstYearTotal) / firstYearTotal) * 100;
                const growthSymbol = growth >= 0 ? '↑' : '↓';
                growthRate = growthSymbol + ' ' + Math.abs(growth).toFixed(1) + '%';
                growthPeriod = firstYear + ' to ' + lastYear;
            }
        }
        
        // Average trend indicator
        const medianPassengers = calculateMedian(chartData.map(r => parseInt(r.passengers, 10) || 0));
        const avgTrend = avgPassengers > medianPassengers ? 'Above median' : avgPassengers < medianPassengers ? 'Below median' : 'At median';
        
        // Batch DOM updates for better performance
        requestAnimationFrame(function() {
            $('#total-passengers').text(new Intl.NumberFormat().format(totalPassengers));
            $('#total-records').text(new Intl.NumberFormat().format(totalRecords) + ' records');
            
            $('#growth-rate').text(growthRate);
            $('#growth-period').text(growthPeriod);
            
            $('#peak-month').text(peakMonth);
            $('#peak-passengers').text(new Intl.NumberFormat().format(peakPassengers) + ' passengers');
            
            $('#busiest-airport').text(busiestAirport.length > 20 ? busiestAirport.substring(0, 17) + '...' : busiestAirport);
            $('#busiest-airport-count').text(new Intl.NumberFormat().format(busiestCount) + ' passengers');
            
            $('#pax-split').text(paxSplit);
            $('#pax-split-subtitle').text(paxSplitSubtitle);
            
            $('#avg-passengers').text(new Intl.NumberFormat().format(avgPassengers));
            $('#avg-trend').text(avgTrend);
        });
    }
    
    /**
     * Calculate median value
     */
    function calculateMedian(values) {
        if (values.length === 0) return 0;
        
        const sorted = values.slice().sort(function(a, b) { return a - b; });
        const mid = Math.floor(sorted.length / 2);
        
        return sorted.length % 2 === 0 
            ? (sorted[mid - 1] + sorted[mid]) / 2 
            : sorted[mid];
    }
    
    /**
     * Reset summary to default state
     */
    function resetSummary() {
        requestAnimationFrame(function() {
            $('#total-passengers').text('-');
            $('#total-records').text('- records');
            $('#growth-rate').text('-');
            $('#growth-period').text('-');
            $('#peak-month').text('-');
            $('#peak-passengers').text('-');
            $('#busiest-airport').text('-');
            $('#busiest-airport-count').text('-');
            $('#pax-split').text('-');
            $('#pax-split-subtitle').text('-');
            $('#avg-passengers').text('-');
            $('#avg-trend').text('-');
        });
    }

    // Initialize when document is ready
    $(document).ready(function() {
        init();
    });

    // Cleanup on page unload (beforeunload is more reliable than unload)
    $(window).on('beforeunload', function() {
        if (paxChart) {
            paxChart.destroy();
            paxChart = null;
        }
        dataCache.clear();
        themeObserver.disconnect();
        if (currentRequest && currentRequest.abort) {
            currentRequest.abort();
        }
        // Clear all debounce timeouts
        debounceTimeouts.forEach(function(timeout) {
            clearTimeout(timeout);
        });
        debounceTimeouts.length = 0;
    });

})(jQuery);
