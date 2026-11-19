/**
 * ============================================================
 * assets/js/reports.js — Dedicated JavaScript for Reports Module
 * Paired with: /pages/reports.php
 * Demonstrates:
 *   - 'click' events: Print report & CSV Export
 *   - 'mouseover' / 'mouseout': Interactive bar animations and tooltips
 * ============================================================
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {

  const printBtn  = document.getElementById('btn-print-report');
  const exportBtn = document.getElementById('btn-export-csv');

  // ============================================================
  // 1. Print Report Handler (Event 1: 'click')
  // ============================================================
  if (printBtn) {
    printBtn.addEventListener('click', () => {
      window.print();
    });
  }

  // ============================================================
  // 2. CSV Export Handler (Event 1: 'click')
  //    Converts HTML report tables into a downloadable CSV file.
  // ============================================================
  if (exportBtn) {
    exportBtn.addEventListener('click', () => {
      let csvContent = 'data:text/csv;charset=utf-8,';
      csvContent += 'CreativeKit 3A - Executive System Report\n';
      csvContent += 'Generated on: ' + new Date().toLocaleString() + '\n\n';

      const tables = document.querySelectorAll('.report-table');
      tables.forEach((table) => {
        const title = table.closest('.report-section')?.querySelector('h2')?.innerText.trim() || 'Report';
        csvContent += `"${title}"\n`;

        // Extract headers
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => `"${th.innerText.replace(/"/g, '""').trim()}"`);
        csvContent += headers.join(',') + '\n';

        // Extract rows
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach((row) => {
          const cells = Array.from(row.querySelectorAll('td')).map(td => `"${td.innerText.replace(/"/g, '""').trim().replace(/\n/g, ' ')}"`);
          csvContent += cells.join(',') + '\n';
        });

        csvContent += '\n';
      });

      // Trigger download
      const encodedUri = encodeURI(csvContent);
      const link = document.createElement('a');
      link.setAttribute('href', encodedUri);
      link.setAttribute('download', `CreativeKit3A_Report_${new Date().toISOString().slice(0, 10)}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    });
  }

  // ============================================================
  // 3. Interactive Progress Bar Hover (mouseover & mouseout)
  // ============================================================
  const progressBars = document.querySelectorAll('.progress-bar-wrap');
  progressBars.forEach((bar) => {
    bar.addEventListener('mouseover', () => {
      const fill = bar.querySelector('.progress-bar-fill');
      if (fill) {
        fill.style.boxShadow = '0 0 10px rgba(255, 107, 0, 0.6)';
        fill.style.filter = 'brightness(1.15)';
      }
    });

    bar.addEventListener('mouseout', () => {
      const fill = bar.querySelector('.progress-bar-fill');
      if (fill) {
        fill.style.boxShadow = '';
        fill.style.filter = '';
      }
    });
  });

});
