
function chartJsArea(elementId) {
  return {
    chartInstance: null, // Store the chart instance for reuse

    initChart() {
      const ctx = document.getElementById(elementId).getContext('2d');

      // Initialize the chart instance
      this.chartInstance = new Chart(ctx, {
        type: 'line', // Set the type to 'line' for area charts
        data: {
          labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
          datasets: [
            {
              label: '# of Votes',
              data: [12, 19, 3, 5, 2, 3],
              backgroundColor: 'rgba(75, 192, 192, 0.2)', // Area fill color
              borderColor: 'rgba(75, 192, 192, 1)', // Line color
              borderWidth: 2,
              fill: true, // Enable area fill
              tension: 0.4, // Smooth curve
            },
          ],
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
            },
          },
        },
      });
    },

    updateChartTheme(theme) {
      if (this.chartInstance) {
        const isDark = theme === 'dark';

        // Update the colors dynamically
        this.chartInstance.data.datasets[0].backgroundColor = isDark
          ? 'rgba(75, 85, 99, 0.2)'
          : 'rgba(75, 192, 192, 0.2)';
        this.chartInstance.data.datasets[0].borderColor = isDark
          ? 'rgba(75, 85, 99, 1)'
          : 'rgba(75, 192, 192, 1)';

        this.chartInstance.update();
      }
    },
  };
}
function chartJsLine(elementId) {
  return {
    chartInstance: null, // Store the chart instance for reuse

    initChart() {
      const ctx = document.getElementById(elementId).getContext('2d');

      // Initialize the chart instance
      this.chartInstance = new Chart(ctx, {
        type: 'line', // Set the type to 'line' for area charts
        data: {
          labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
          datasets: [
            {
              label: '# of Votes',
              data: [12, 19, 3, 5, 2, 3],
              backgroundColor: 'rgba(75, 192, 192, 0.2)', // Area fill color
              borderColor: 'rgba(75, 192, 192, 1)', // Line color
              borderWidth: 2,
              fill: false, // Enable area fill
              tension: 0.4, // Smooth curve
            },
          ],
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
            },
          },
        },
      });
    },

    updateChartTheme(theme) {
      if (this.chartInstance) {
        const isDark = theme === 'dark';

        // Update the colors dynamically
        this.chartInstance.data.datasets[0].backgroundColor = isDark
          ? 'rgba(75, 85, 99, 0.2)'
          : 'rgba(75, 192, 192, 0.2)';
        this.chartInstance.data.datasets[0].borderColor = isDark
          ? 'rgba(75, 85, 99, 1)'
          : 'rgba(75, 192, 192, 1)';

        this.chartInstance.update();
      }
    },
  };
}
function chartJsBar(elementId) {
  return {
    chartInstance: null, // Store the chart instance for reuse

    initChart() {
      const ctx = document.getElementById(elementId).getContext('2d');

      // Initialize the chart instance
      this.chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
          datasets: [
            {
              label: '# of Votes',
              data: [12, 19, 3, 5, 2, 3],
              backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)',
              ],
              borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
              ],
              borderWidth: 1,
            },
          ],
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
            },
          },
        },
      });
    },

    updateChartTheme(theme) {
      if (this.chartInstance) {
        const isDark = theme === 'dark';

        // Update the colors dynamically
        this.chartInstance.data.datasets[0].backgroundColor = isDark
          ? [
              'rgba(75, 85, 99, 0.2)',
              'rgba(107, 114, 128, 0.2)',
              'rgba(156, 163, 175, 0.2)',
              'rgba(209, 213, 219, 0.2)',
              'rgba(229, 231, 235, 0.2)',
              'rgba(243, 244, 246, 0.2)',
            ]
          : [
              'rgba(255, 99, 132, 0.2)',
              'rgba(54, 162, 235, 0.2)',
              'rgba(255, 206, 86, 0.2)',
              'rgba(75, 192, 192, 0.2)',
              'rgba(153, 102, 255, 0.2)',
              'rgba(255, 159, 64, 0.2)',
            ];

        this.chartInstance.update();
      }
    },
  };
}
function chartRadar(elementId) {
  return {
    chartInstance: null, // Store the chart instance for reuse

    initChart() {
      const canvas = document.getElementById(elementId);

      // Validate the canvas element
      if (!canvas) {
        console.error(`Canvas element with ID "${elementId}" not found.`);
        return;
      }

      const ctx = canvas.getContext('2d');

      // Initialize the radar chart instance
      this.chartInstance = new Chart(ctx, {
        type: 'radar', // Specify radar chart type
        data: {
          labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'], // Categories for the radar chart
          datasets: [
            {
              label: '# of Votes',
              data: [12, 19, 3, 5, 2, 3], // Data points
              backgroundColor: 'rgba(75, 192, 192, 0.2)', // Transparent fill for radar
              borderColor: 'rgba(75, 192, 192, 1)', // Border color for radar
              borderWidth: 2,
              pointBackgroundColor: 'rgba(75, 192, 192, 1)', // Point fill color
              pointBorderColor: '#fff', // Point border color
              pointHoverBackgroundColor: '#fff', // Hover background for points
              pointHoverBorderColor: 'rgba(75, 192, 192, 1)', // Hover border for points
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false, // Ensures proper resizing
          scales: {
            r: {
              beginAtZero: true, // Start radar chart from zero
              grid: {
                color: 'rgba(128, 128, 128, 0.2)', // Gridline color
              },
              angleLines: {
                color: 'rgba(128, 128, 128, 0.2)', // Angle line color
              },
            },
          },
          plugins: {
            legend: {
              display: true,
              position: 'top', // Position of the legend
            },
          },
        },
      });
    },

    updateChartTheme(theme) {
      if (this.chartInstance) {
        const isDark = theme === 'dark';

        // Update dataset styles based on the theme
        this.chartInstance.data.datasets[0].backgroundColor = isDark
          ? 'rgba(107, 114, 128, 0.2)' // Dark theme fill
          : 'rgba(75, 192, 192, 0.2)'; // Light theme fill
        this.chartInstance.data.datasets[0].borderColor = isDark
          ? 'rgba(107, 114, 128, 1)' // Dark theme border
          : 'rgba(75, 192, 192, 1)'; // Light theme border
        this.chartInstance.data.datasets[0].pointBackgroundColor = isDark
          ? 'rgba(107, 114, 128, 1)' // Dark theme point fill
          : 'rgba(75, 192, 192, 1)'; // Light theme point fill

        // Update the chart with new styles
        this.chartInstance.update();
      } else {
        console.error('Chart instance not found to update theme.');
      }
    },
  };
}

function chartJsPie(elementId) {
  return {
    chartInstance: null, // Store the chart instance for reuse

    initChart() {
      const canvas = document.getElementById(elementId);

      // Validate the canvas element
      if (!canvas) {
        console.error(`Canvas element with ID "${elementId}" not found.`);
        return;
      }

      const ctx = canvas.getContext('2d');

      // Initialize the pie chart instance
      this.chartInstance = new Chart(ctx, {
        type: 'pie', // Specify pie chart type
        data: {
          labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'], // Categories
          datasets: [
            {
              label: '# of Votes',
              data: [12, 19, 3, 5, 2, 3], // Data points
              backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)',
              ],
              borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
              ],
              borderWidth: 1,
            },
          ],
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top', // Position of the legend
            },
            tooltip: {
              callbacks: {
                label: function (tooltipItem) {
                  const value = tooltipItem.raw;
                  return `${tooltipItem.label}: ${value}`;
                },
              },
            },
          },
        },
      });
    },

    updateChartTheme(theme) {
      if (this.chartInstance) {
        const isDark = theme === 'dark';

        // Update dataset styles based on the theme
        this.chartInstance.data.datasets[0].backgroundColor = isDark
          ? [
              'rgba(75, 85, 99, 0.2)',
              'rgba(107, 114, 128, 0.2)',
              'rgba(156, 163, 175, 0.2)',
              'rgba(209, 213, 219, 0.2)',
              'rgba(229, 231, 235, 0.2)',
              'rgba(243, 244, 246, 0.2)',
            ]
          : [
              'rgba(255, 99, 132, 0.2)',
              'rgba(54, 162, 235, 0.2)',
              'rgba(255, 206, 86, 0.2)',
              'rgba(75, 192, 192, 0.2)',
              'rgba(153, 102, 255, 0.2)',
              'rgba(255, 159, 64, 0.2)',
            ];
        this.chartInstance.update();
      } else {
        console.error('Chart instance not found to update theme.');
      }
    },
  };
}

