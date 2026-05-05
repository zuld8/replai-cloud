/**
 * Conversation Rate Analytics - JavaScript
 * Interactive features for dashboard
 */

// ========================================
// Chart Initialization
// ========================================

let charts = {};

function initializeCharts(data) {
    // Conversation Distribution Chart (Doughnut)
    const conversationCtx = document.getElementById(
        "conversationDistributionChart"
    );
    if (conversationCtx) {
        charts.conversation = new Chart(conversationCtx, {
            type: "doughnut",
            data: {
                labels: data.conversationDistribution.labels,
                datasets: [
                    {
                        data: data.conversationDistribution.data,
                        backgroundColor: data.conversationDistribution.colors,
                        borderWidth: 0,
                        hoverOffset: 10,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: "rgba(0,0,0,0.8)",
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: "bold",
                        },
                        bodyFont: {
                            size: 13,
                        },
                        callbacks: {
                            label: function (context) {
                                const total = context.dataset.data.reduce(
                                    (a, b) => a + b,
                                    0
                                );
                                const percentage = (
                                    (context.parsed / total) *
                                    100
                                ).toFixed(1);
                                return `${
                                    context.label
                                }: ${context.parsed.toLocaleString()} (${percentage}%)`;
                            },
                        },
                    },
                },
                cutout: "70%",
            },
        });
    }

    // Message Distribution Chart (Doughnut)
    const messageCtx = document.getElementById("messageDistributionChart");
    if (messageCtx) {
        charts.message = new Chart(messageCtx, {
            type: "doughnut",
            data: {
                labels: data.messageDistribution.labels,
                datasets: [
                    {
                        data: data.messageDistribution.data,
                        backgroundColor: data.messageDistribution.colors,
                        borderWidth: 0,
                        hoverOffset: 10,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: "rgba(0,0,0,0.8)",
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: "bold",
                        },
                        bodyFont: {
                            size: 13,
                        },
                        callbacks: {
                            label: function (context) {
                                const total = context.dataset.data.reduce(
                                    (a, b) => a + b,
                                    0
                                );
                                const percentage = (
                                    (context.parsed / total) *
                                    100
                                ).toFixed(1);
                                return `${
                                    context.label
                                }: ${context.parsed.toLocaleString()} (${percentage}%)`;
                            },
                        },
                    },
                },
                cutout: "70%",
            },
        });
    }

    // Top Agents Chart (Bar)
    const topAgentsCtx = document.getElementById("topAgentsChart");
    if (topAgentsCtx) {
        charts.topAgents = new Chart(topAgentsCtx, {
            type: "bar",
            data: {
                labels: data.topAgents.labels,
                datasets: [
                    {
                        label: "Resolution Rate (%)",
                        data: data.topAgents.data,
                        backgroundColor: createGradient(
                            topAgentsCtx,
                            "#667eea",
                            "#764ba2"
                        ),
                        borderRadius: 8,
                        borderSkipped: false,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: "rgba(0,0,0,0.8)",
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: "bold",
                        },
                        bodyFont: {
                            size: 13,
                        },
                        callbacks: {
                            label: function (context) {
                                return `Resolution Rate: ${context.parsed.y.toFixed(
                                    1
                                )}%`;
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function (value) {
                                return value + "%";
                            },
                        },
                        grid: {
                            color: "rgba(0,0,0,0.05)",
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                    },
                },
            },
        });
    }
}

// Helper function to create gradient
function createGradient(ctx, color1, color2) {
    const gradient = ctx.getContext("2d").createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, color1);
    gradient.addColorStop(1, color2);
    return gradient;
}

// ========================================
// Table Features
// ========================================

let sortState = {
    column: null,
    direction: "asc",
};

function initializeTableFeatures() {
    // Search functionality
    const searchInput = document.getElementById("searchAgent");
    if (searchInput) {
        searchInput.addEventListener("input", function (e) {
            filterTable(e.target.value.toLowerCase());
        });
    }

    // Sorting functionality
    const sortableHeaders = document.querySelectorAll(".sortable");
    sortableHeaders.forEach((header) => {
        header.addEventListener("click", function () {
            const column = this.dataset.sort;
            sortTable(column);
        });
    });

    // Reset filters
    const resetBtn = document.getElementById("resetFilters");
    if (resetBtn) {
        resetBtn.addEventListener("click", function () {
            window.location.href = window.location.pathname;
        });
    }
}

// Filter table by search
function filterTable(searchTerm) {
    const table = document.getElementById("performanceTable");
    if (!table) return;

    const rows = table.querySelectorAll("tbody tr");

    rows.forEach((row) => {
        const agentName = row.dataset.agentName || "";
        if (agentName.includes(searchTerm)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });

    // Show/hide empty state
    const visibleRows = Array.from(rows).filter(
        (row) => row.style.display !== "none"
    );
    if (visibleRows.length === 0) {
        showEmptyState(table);
    } else {
        hideEmptyState(table);
    }
}

// Sort table by column
function sortTable(column) {
    const table = document.getElementById("performanceTable");
    if (!table) return;

    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr"));

    // Determine sort direction
    if (sortState.column === column) {
        sortState.direction = sortState.direction === "asc" ? "desc" : "asc";
    } else {
        sortState.column = column;
        sortState.direction = "asc";
    }

    // Sort rows
    rows.sort((a, b) => {
        let aValue, bValue;

        switch (column) {
            case "name":
                aValue = a.querySelector(".agent-name").textContent.trim();
                bValue = b.querySelector(".agent-name").textContent.trim();
                return sortState.direction === "asc"
                    ? aValue.localeCompare(bValue)
                    : bValue.localeCompare(aValue);

            case "total":
                aValue = parseInt(a.cells[1].textContent.replace(/,/g, ""));
                bValue = parseInt(b.cells[1].textContent.replace(/,/g, ""));
                break;

            case "resolved":
                aValue = parseInt(a.cells[2].textContent.replace(/,/g, ""));
                bValue = parseInt(b.cells[2].textContent.replace(/,/g, ""));
                break;

            case "resolution_rate":
                aValue = parseFloat(a.cells[3].textContent.replace("%", ""));
                bValue = parseFloat(b.cells[3].textContent.replace("%", ""));
                break;

            case "messages":
                aValue = parseInt(a.cells[4].textContent.replace(/,/g, ""));
                bValue = parseInt(b.cells[4].textContent.replace(/,/g, ""));
                break;

            case "avg_messages":
                aValue = parseFloat(a.cells[5].textContent);
                bValue = parseFloat(b.cells[5].textContent);
                break;

            case "response_time":
                aValue = parseFloat(a.cells[6].textContent.replace(" min", ""));
                bValue = parseFloat(b.cells[6].textContent.replace(" min", ""));
                break;

            case "engagement":
                aValue = parseFloat(a.cells[7].textContent.replace("%", ""));
                bValue = parseFloat(b.cells[7].textContent.replace("%", ""));
                break;

            default:
                return 0;
        }

        if (sortState.direction === "asc") {
            return aValue - bValue;
        } else {
            return bValue - aValue;
        }
    });

    // Clear tbody and append sorted rows
    tbody.innerHTML = "";
    rows.forEach((row) => tbody.appendChild(row));

    // Update sort indicators
    updateSortIndicators(column);
}

// Update visual sort indicators
function updateSortIndicators(activeColumn) {
    const headers = document.querySelectorAll(".sortable");
    headers.forEach((header) => {
        const icon = header.querySelector("i");
        if (header.dataset.sort === activeColumn) {
            icon.className =
                sortState.direction === "asc"
                    ? "bi bi-arrow-up"
                    : "bi bi-arrow-down";
            icon.style.opacity = "1";
        } else {
            icon.className = "bi bi-arrow-down-up";
            icon.style.opacity = "0.3";
        }
    });
}

// Show empty state
function showEmptyState(table) {
    let emptyRow = table.querySelector(".empty-search-state");
    if (!emptyRow) {
        const tbody = table.querySelector("tbody");
        emptyRow = document.createElement("tr");
        emptyRow.className = "empty-search-state";
        emptyRow.innerHTML = `
            <td colspan="9" class="text-center py-5">
                <div class="empty-state">
                    <i class="bi bi-search"></i>
                    <p class="empty-text">Tidak ada agent yang cocok dengan pencarian</p>
                </div>
            </td>
        `;
        tbody.appendChild(emptyRow);
    }
    emptyRow.style.display = "";
}

// Hide empty state
function hideEmptyState(table) {
    const emptyRow = table.querySelector(".empty-search-state");
    if (emptyRow) {
        emptyRow.style.display = "none";
    }
}

// ========================================
// Filter Auto-submit
// ========================================

document.addEventListener("DOMContentLoaded", function () {
    const yearSelect = document.getElementById("year");
    const monthSelect = document.getElementById("month");

    // Optional: Auto-submit on change (remove if you want manual submit only)
    /*
    if (yearSelect) {
        yearSelect.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
    
    if (monthSelect) {
        monthSelect.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
    */
});

// ========================================
// Export Functionality
// ========================================

function exportToCSV() {
    const year = document.getElementById("year").value;
    const month = document.getElementById("month").value;
    const agentId = document.getElementById("agent_id").value;

    let url = `/api/analytics/conversation-rate/export?year=${year}&month=${month}&format=csv`;
    if (agentId) {
        url += `&agent_id=${agentId}`;
    }

    window.location.href = url;
}

// ========================================
// Utility Functions
// ========================================

// Number formatting
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Percentage formatting
function formatPercentage(num, decimals = 1) {
    return num.toFixed(decimals) + "%";
}

// Time formatting
function formatMinutes(minutes) {
    if (minutes < 1) {
        return "<1 min";
    } else if (minutes < 60) {
        return minutes.toFixed(1) + " min";
    } else {
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;
        return `${hours}h ${mins.toFixed(0)}m`;
    }
}

// Get badge class based on value
function getBadgeClass(value, type) {
    if (type === "resolution_rate") {
        return value >= 80
            ? "bg-success"
            : value >= 50
            ? "bg-warning"
            : "bg-danger";
    } else if (type === "response_time") {
        return value <= 5
            ? "bg-success"
            : value <= 15
            ? "bg-warning"
            : "bg-danger";
    } else if (type === "engagement") {
        return value >= 90
            ? "bg-success"
            : value >= 70
            ? "bg-warning"
            : "bg-danger";
    }
    return "bg-secondary";
}

// ========================================
// Print Functionality
// ========================================

window.onbeforeprint = function () {
    // Optional: Add print-specific modifications
    console.log("Preparing to print...");
};

window.onafterprint = function () {
    // Optional: Cleanup after print
    console.log("Print completed");
};

// ========================================
// Tooltips Initialization (if using Bootstrap tooltips)
// ========================================

document.addEventListener("DOMContentLoaded", function () {
    // Initialize Bootstrap tooltips if available
    if (typeof bootstrap !== "undefined") {
        const tooltipTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});

// ========================================
// Responsive Table
// ========================================

function makeTableResponsive() {
    const table = document.getElementById("performanceTable");
    if (!table) return;

    if (window.innerWidth < 768) {
        table.classList.add("table-sm");
    } else {
        table.classList.remove("table-sm");
    }
}

window.addEventListener("resize", makeTableResponsive);
document.addEventListener("DOMContentLoaded", makeTableResponsive);
