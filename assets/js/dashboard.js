document.addEventListener("DOMContentLoaded", function () {

    // =========================
    // Email Analytics Chart
    // =========================

    const canvas = document.getElementById("pemAnalyticsChart");

    if (canvas) {

        new Chart(canvas, {

            type: "bar",

            data: {

                labels: [
                    "Opened",
                    "Clicked",
                    "Unsubscribed"
                ],

                datasets: [{

                    label: "Emails",

                    data: [
                        pemDashboard.opens,
                        pemDashboard.clicks,
                        pemDashboard.unsubscribed
                    ]

                }]

            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    }
                },

                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }

        });

    }

    // =========================
    // Campaign Status Chart
    // =========================

    const campaignCanvas = document.getElementById("pemCampaignChart");

    if (campaignCanvas) {

        new Chart(campaignCanvas, {

            type: "doughnut",

            data: {

                labels: [
                    "Running",
                    "Completed",
                    "Draft"
                ],

                datasets: [{

                    data: [
                        pemDashboard.running,
                        pemDashboard.completed,
                        pemDashboard.draft
                    ]

                }]

            },

            options: {

                responsive: true,
                maintainAspectRatio: false

            }

        });

    }

    // =========================
// Last 7 Days Activity Chart
// =========================

const activityCanvas = document.getElementById("pemActivityChart");

if (activityCanvas) {

    const labels = [];
    const totals = [];

    pemDashboard.activity.forEach(function (item) {
        labels.push(item.day);
        totals.push(item.total);
    });

    new Chart(activityCanvas, {

        type: "line",

        data: {

            labels: labels,

            datasets: [{

                label: "Emails Sent",

                data: totals,

                tension: 0.4,
                fill: false

            }]

        },

        options: {

            responsive: true,
            maintainAspectRatio: false,

            scales: {
                y: {
                    beginAtZero: true
                }
            }

        }

    });

}

});