// Dashboard Chart View Switcher v3
(function() {
    'use strict';

    var selector = document.getElementById('chartViewSelector');
    var dateGroup = document.getElementById('dateFilterGroup');
    var labelEl = document.getElementById('labelLeads');
    var pesanEl = document.getElementById('pesanMasukChart');
    var bcEl = document.getElementById('broadcastSummaryChart');
    var loadingEl = document.getElementById('chartLoading');
    var _reqId = 0;

    // Load label on start
    loadLabelChart();

    // Dropdown change
    if (selector) {
        selector.onchange = function() {
            _reqId++;
            hideAll();
            var v = this.value;
            if (v === 'label') {
                if (dateGroup) dateGroup.style.display = 'none';
                loadLabelChart();
            } else {
                if (dateGroup) dateGroup.style.display = 'flex';
                setActiveDay(7);
                doFetch(v, 7);
            }
        };
    }

    // Date buttons - direct onclick
    if (dateGroup) {
        var btns = dateGroup.querySelectorAll('.date-filter-btn');
        for (var i = 0; i < btns.length; i++) {
            (function(btn) {
                btn.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var d = parseInt(this.getAttribute('data-days'));
                    setActiveDay(d);
                    var v = selector ? selector.value : 'label';
                    doFetch(v, d);
                };
            })(btns[i]);
        }
    }

    function setActiveDay(d) {
        if (!dateGroup) return;
        var bs = dateGroup.querySelectorAll('.date-filter-btn');
        for (var i = 0; i < bs.length; i++) {
            bs[i].classList.remove('active');
            if (parseInt(bs[i].getAttribute('data-days')) === d) bs[i].classList.add('active');
        }
    }

    function doFetch(view, days) {
        if (view === 'pesan-masuk') fetchPesanMasuk(days);
        else if (view === 'broadcast') fetchBroadcast(days);
    }

    function hideAll() {
        if (labelEl) labelEl.style.display = 'none';
        if (pesanEl) { pesanEl.style.display = 'none'; pesanEl.innerHTML = ''; }
        if (bcEl) { bcEl.style.display = 'none'; bcEl.innerHTML = ''; }
        if (loadingEl) loadingEl.style.display = 'none';
    }

    function showSpinner() {
        if (pesanEl) { pesanEl.style.display = 'none'; pesanEl.innerHTML = ''; }
        if (bcEl) { bcEl.style.display = 'none'; bcEl.innerHTML = ''; }
        if (loadingEl) loadingEl.style.display = 'block';
    }

    function loadLabelChart() {
        if (!labelEl) return;
        var id = ++_reqId;
        labelEl.innerHTML = '<div style="text-align:center;padding:3rem"><div class="spinner-border text-success spinner-border-sm"></div></div>';
        labelEl.style.display = 'block';

        var x = new XMLHttpRequest();
        x.open('GET', '/app/dashboard/label-leads', true);
        x.onload = function() {
            if (id !== _reqId) return;
            try {
                var resp = JSON.parse(x.responseText);
                var labels = [], series = [], colors = [];
                if (resp.labels) {
                    resp.labels.forEach(function(item) {
                        labels.push(item.label);
                        series.push(item.data);
                        colors.push(item.color);
                    });
                }
                if (series.length === 0) {
                    labelEl.innerHTML = '<div style="text-align:center;padding:3rem;color:#9ca3af">Tidak ada data label</div>';
                    return;
                }
                labelEl.innerHTML = '';
                new ApexCharts(labelEl, {
                    series: series,
                    chart: { height: 270, type: 'donut', fontFamily: 'inherit' },
                    colors: colors.length ? colors : ['#10B981','#3B82F6','#F59E0B','#EF4444','#8B5CF6'],
                    labels: labels,
                    legend: { position: 'bottom', fontSize: '12px', fontWeight: 500 },
                    dataLabels: { dropShadow: { enabled: false } },
                    plotOptions: { pie: { donut: { size: '55%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '13px', fontWeight: 700 } } } } },
                    stroke: { width: 2, colors: ['#fff'] }
                }).render();
            } catch(e) { labelEl.innerHTML = '<div style="text-align:center;padding:3rem;color:#ef4444">Error</div>'; }
        };
        x.send();
    }

    function fetchPesanMasuk(days) {
        var id = ++_reqId;
        showSpinner();

        var x = new XMLHttpRequest();
        x.open('GET', '/app/dashboard/pesan-masuk?days=' + days, true);
        x.onload = function() {
            if (id !== _reqId) return;
            if (loadingEl) loadingEl.style.display = 'none';
            try {
                var data = JSON.parse(x.responseText);
                if (pesanEl) { pesanEl.style.display = 'block'; pesanEl.innerHTML = ''; }
                new ApexCharts(pesanEl, {
                    series: [{ name: 'Pesan Masuk', data: data.totals || [] }],
                    chart: { type: 'area', height: 260, toolbar: { show: false }, fontFamily: 'inherit' },
                    colors: ['#3B82F6'],
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.02, stops: [0, 90, 100] } },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 2.5 },
                    grid: { show: true, borderColor: 'rgba(0,0,0,0.05)', strokeDashArray: 4 },
                    xaxis: { categories: data.dates || [], axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { fontSize: '10px', colors: '#9ca3af' }, rotate: -45 } },
                    yaxis: { labels: { formatter: function(v) { return Math.round(v).toLocaleString(); }, style: { fontSize: '11px', colors: '#9ca3af' } } },
                    tooltip: { theme: 'light' },
                    subtitle: { text: 'Total: ' + (data.grand_total || 0).toLocaleString() + ' pesan', align: 'right', style: { fontSize: '13px', fontWeight: 700, color: '#3B82F6' } }
                }).render();
            } catch(e) { console.error(e); }
        };
        x.onerror = function() { if (id === _reqId && loadingEl) loadingEl.style.display = 'none'; };
        x.send();
    }

    function fetchBroadcast(days) {
        var id = ++_reqId;
        showSpinner();

        var x = new XMLHttpRequest();
        x.open('GET', '/app/dashboard/broadcast-summary?days=' + days, true);
        x.onload = function() {
            if (id !== _reqId) return;
            if (loadingEl) loadingEl.style.display = 'none';
            try {
                var data = JSON.parse(x.responseText);
                if (bcEl) { bcEl.style.display = 'block'; bcEl.innerHTML = ''; }
                new ApexCharts(bcEl, {
                    series: [
                        { name: 'Terkirim', data: data.sent || [] },
                        { name: 'Gagal', data: data.failed || [] }
                    ],
                    chart: { type: 'bar', height: 260, toolbar: { show: false }, fontFamily: 'inherit', stacked: true },
                    colors: ['#10B981', '#EF4444'],
                    dataLabels: { enabled: false },
                    grid: { show: true, borderColor: 'rgba(0,0,0,0.05)', strokeDashArray: 4 },
                    xaxis: { categories: data.dates || [], axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { fontSize: '10px', colors: '#9ca3af' }, rotate: -45 } },
                    yaxis: { labels: { formatter: function(v) { return Math.round(v).toLocaleString(); }, style: { fontSize: '11px', colors: '#9ca3af' } } },
                    plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
                    legend: { position: 'top', fontSize: '12px', fontWeight: 500 },
                    tooltip: { theme: 'light' },
                    subtitle: { text: 'Terkirim: ' + (data.grand_sent || 0).toLocaleString() + '  |  Gagal: ' + (data.grand_failed || 0).toLocaleString(), align: 'right', style: { fontSize: '12px', fontWeight: 700, color: '#374151' } }
                }).render();
            } catch(e) { console.error(e); }
        };
        x.onerror = function() { if (id === _reqId && loadingEl) loadingEl.style.display = 'none'; };
        x.send();
    }
})();
