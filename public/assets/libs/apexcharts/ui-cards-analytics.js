/**
 * Card Analytics
 */

"use strict";

(function () {
    let cardColor,
        headingColor,
        labelColor,
        shadeColor,
        borderColor,
        legendColor,
        heatMap1,
        heatMap2,
        heatMap3,
        heatMap4;

    if (isDarkStyle) {
        cardColor = config.colors_dark.cardColor;
        headingColor = config.colors_dark.headingColor;
        labelColor = config.colors_dark.textMuted;
        borderColor = config.colors_dark.borderColor;
        legendColor = config.colors_dark.bodyColor;
        shadeColor = "dark";
        heatMap1 = "#4f51c0";
        heatMap2 = "#595cd9";
        heatMap3 = "#8789ff";
        heatMap4 = "#c3c4ff";
    } else {
        cardColor = config.colors.cardColor;
        headingColor = config.colors.headingColor;
        labelColor = config.colors.textMuted;
        borderColor = config.colors.borderColor;
        legendColor = config.colors.bodyColor;
        shadeColor = "";
        heatMap1 = "#e1e2ff";
        heatMap2 = "#c3c4ff";
        heatMap3 = "#a5a7ff";
        heatMap4 = "#696cff";
    }

    // Total Income - Area Chart
    // --------------------------------------------------------------------
    const interactionChartEl = document.querySelector("#interactionChart");
 

    if (interactionChartEl) {
        fetch("/app/dashboard/interaction")
            .then((response) => response.json())
            .then((data) => {
                // Generate full date range for the current month
                const now = new Date();
                const daysInMonth = new Date(
                    now.getFullYear(),
                    now.getMonth() + 1,
                    0
                ).getDate();
                const fullDates = Array.from(
                    { length: daysInMonth },
                    (_, i) => {
                        const day = i + 1;
                        return `${now.getFullYear()}-${(now.getMonth() + 1)
                            .toString()
                            .padStart(2, "0")}-${day
                            .toString()
                            .padStart(2, "0")}`;
                    }
                );

                // Map API data to a dictionary
                const dataMap = data.reduce((acc, item) => {
                    acc[item.date] = item.count;
                    return acc;
                }, {});

                // Create final dataset with missing dates filled with 0
                const categories = fullDates;
                const seriesData = fullDates.map((date) => dataMap[date] || 0);

                const interactionChartConfig = {
                    chart: {
                        height: 220,
                        type: "area",
                        toolbar: false,
                        dropShadow: {
                            enabled: true,
                            top: 14,
                            left: 2,
                            blur: 3,
                            color: "#7367F0",
                            opacity: 0.15,
                        },
                    },
                    series: [
                        {
                            name: "Interactions",
                            data: seriesData,
                        },
                    ],
                    dataLabels: { enabled: false },
                    stroke: { width: 3, curve: "smooth" },
                    colors: ["#7367F0"],
                    fill: {
                        type: "gradient",
                        gradient: {
                            shade: "dark",
                            shadeIntensity: 0.8,
                            opacityFrom: 0.7,
                            opacityTo: 0.25,
                            stops: [0, 95, 100],
                        },
                    },
                    grid: { show: true, borderColor: "#ebebeb" },
                    xaxis: {
                        categories: categories,
                        labels: {
                            style: { fontSize: "13px", colors: "#6e6b7b" },
                        },
                    },
                    yaxis: {
                        labels: {
                            formatter: (val) => val.toLocaleString(),
                            style: { fontSize: "13px", colors: "#6e6b7b" },
                        },
                    },
                };

                const interactionChart = new ApexCharts(
                    interactionChartEl,
                    interactionChartConfig
                );
                interactionChart.render();
            })
            .catch((error) => {
                console.error("Error fetching interaction data:", error);
            });
    }

    // Total Income - Area Chart
    // --------------------------------------------------------------------
    const responseAiChartEl = document.querySelector("#responseAiChart");
 

    if (responseAiChartEl) {
        fetch("/administrator/dashboard/response-ai")
            .then((response) => response.json())
            .then((data) => {
                // Generate full date range for the current month
                const now = new Date();
                const daysInMonth = new Date(
                    now.getFullYear(),
                    now.getMonth() + 1,
                    0
                ).getDate();
                const fullDates = Array.from(
                    { length: daysInMonth },
                    (_, i) => {
                        const day = i + 1;
                        return `${now.getFullYear()}-${(now.getMonth() + 1)
                            .toString()
                            .padStart(2, "0")}-${day
                            .toString()
                            .padStart(2, "0")}`;
                    }
                );

                // Map API data to a dictionary
                const dataMap = data.reduce((acc, item) => {
                    acc[item.date] = item.count;
                    return acc;
                }, {});

                // Create final dataset with missing dates filled with 0
                const categories = fullDates;
                const seriesData = fullDates.map((date) => dataMap[date] || 0);

                const responseAiChartConfig = {
                    chart: {
                        height: 220,
                        type: "area",
                        toolbar: false,
                        dropShadow: {
                            enabled: true,
                            top: 14,
                            left: 2,
                            blur: 3,
                            color: "#7367F0",
                            opacity: 0.15,
                        },
                    },
                    series: [
                        {
                            name: "Interactions",
                            data: seriesData,
                        },
                    ],
                    dataLabels: { enabled: false },
                    stroke: { width: 3, curve: "smooth" },
                    colors: ["#7367F0"],
                    fill: {
                        type: "gradient",
                        gradient: {
                            shade: "dark",
                            shadeIntensity: 0.8,
                            opacityFrom: 0.7,
                            opacityTo: 0.25,
                            stops: [0, 95, 100],
                        },
                    },
                    grid: { show: true, borderColor: "#ebebeb" },
                    xaxis: {
                        categories: categories,
                        labels: {
                            style: { fontSize: "13px", colors: "#6e6b7b" },
                        },
                    },
                    yaxis: {
                        labels: {
                            formatter: (val) => val.toLocaleString(),
                            style: { fontSize: "13px", colors: "#6e6b7b" },
                        },
                    },
                };

                const responseAiChart = new ApexCharts(
                    responseAiChartEl,
                    responseAiChartConfig
                );
                responseAiChart.render();
            })
            .catch((error) => {
                console.error("Error fetching interaction data:", error);
            });
    }
})();
