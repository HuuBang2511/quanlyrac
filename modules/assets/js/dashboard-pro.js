document.addEventListener('DOMContentLoaded', function () {
    if (typeof dashboardProData !== 'undefined') {
        initProDashboard(dashboardProData);
    }
});

function initProDashboard(data) {
    // Khởi tạo 4 biểu đồ Sparkline trên thẻ KPI
    initSparkline('sparkline1', data.sparkline.series1, '#4A55A2');
    initSparkline('sparkline2', data.sparkline.series2, '#34A853');
    initSparkline('sparkline3', data.sparkline.series3, '#FBBC05');
    initSparkline('sparkline4', data.sparkline.series4, '#EA4335');

    // Khởi tạo các biểu đồ chính
    initMainCharts(data.charts);
}

function initMainCharts(chartData) {
    if (chartData.tinhhinhthugom && chartData.tinhhinhthugom.length > 0) {
        initLineChart('chart7Days', chartData.tinhhinhthugom);
    }
    if (chartData.phuongxa && chartData.phuongxa.length > 0) {
        initBarChart('chartPhuongXa', chartData.phuongxa);
    }
    if (chartData.loaidiemthugom && chartData.loaidiemthugom.length > 0) {
        initPieChart('chartLoaiDiem', chartData.loaidiemthugom);
    }
}

// Hàm khởi tạo chung, tự động resize
function createChart(elementId, option) {
    const chartDom = document.getElementById(elementId);
    if (!chartDom) return;
    const myChart = echarts.init(chartDom);
    myChart.setOption(option);
    window.addEventListener('resize', () => myChart.resize());
}

// Biểu đồ đường mini cho KPI card
function initSparkline(id, data, color) {
    const option = {
        grid: { top: 10, bottom: 10, left: 0, right: 0 },
        xAxis: { type: 'category', show: false },
        yAxis: { type: 'value', show: false },
        series: [{
            data: data,
            type: 'line',
            smooth: true,
            symbol: 'none',
            lineStyle: { color: color, width: 2 },
            areaStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                    offset: 0, color: color
                }, {
                    offset: 1, color: 'rgba(255, 255, 255, 0)'
                }])
            }
        }]
    };
    createChart(id, option);
}

// Biểu đồ đường chính
function initLineChart(id, data) {
    const option = {
        tooltip: { trigger: 'axis' },
        grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: data.map(item => item.date)
        },
        yAxis: { type: 'value' },
        series: [{
            name: 'Điểm xác thực',
            type: 'line',
            data: data.map(item => item.value),
            smooth: true,
            itemStyle: { color: '#4A55A2' },
            areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{ offset: 0, color: 'rgba(74, 85, 162, 0.5)' }, { offset: 1, color: 'rgba(74, 85, 162, 0)' }]) }
        }]
    };
    createChart(id, option);
}

// Biểu đồ cột ngang
function initBarChart(id, data) {
    const option = {
        tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
        grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
        xAxis: { type: 'value', boundaryGap: [0, 0.01] },
        yAxis: { type: 'category', data: data.map(item => item.name).reverse() },
        series: [{
            name: 'Số điểm',
            type: 'bar',
            data: data.map(item => item.value).reverse(),
            itemStyle: { color: '#7895CB' }
        }]
    };
    createChart(id, option);
}

// Biểu đồ tròn
function initPieChart(id, data) {
    const option = {
        tooltip: { trigger: 'item', formatter: '{b}: {c} ({d}%)' },
        legend: { top: 'bottom', left: 'center' },
        series: [{
            name: 'Loại điểm',
            type: 'pie',
            radius: ['50%', '70%'],
            avoidLabelOverlap: true,
            label: { show: false },
            emphasis: { label: { show: true, fontSize: 18, fontWeight: 'bold' } },
            data: data,
            color: ['#4A55A2', '#7895CB', '#A0BFE0']
        }]
    };
    createChart(id, option);
}