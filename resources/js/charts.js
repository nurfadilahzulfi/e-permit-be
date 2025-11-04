import ApexCharts from "apexcharts";

document.addEventListener("DOMContentLoaded", () => {
    const chartEl = document.querySelector("#chart");
    if (!chartEl) return;

    const options = {
        chart: {
            type: "line",
            height: 300,
            toolbar: { show: false },
        },
        series: [
            {
                name: "Permit Aktif",
                data: [5, 7, 8, 6, 9, 10, 12, 8, 7, 11, 9, 10],
            },
        ],
        xaxis: {
            categories: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
        },
        colors: ["#6366f1"],
        stroke: { curve: "smooth" },
    };

    const chart = new ApexCharts(chartEl, options);
    chart.render();
});
