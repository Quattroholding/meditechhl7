/**
 * Responsive Table Functionality
 * Simulates DataTables responsive behavior without using the library
 */

function toggleRowDetails(btn) {
    const row = btn.closest('tr');
    const rowId = row.getAttribute('data-row-id');
    const detailsRow = document.querySelector(`tr.row-details[data-parent-row="${rowId}"]`);
    const icon = btn.querySelector('i');

    if (detailsRow.classList.contains('d-none')) {
        // Show details
        populateRowDetails(row, detailsRow);
        detailsRow.classList.remove('d-none');
        icon.classList.remove('fa-plus-circle');
        icon.classList.add('fa-minus-circle');
        btn.classList.add('expanded');
    } else {
        // Hide details
        detailsRow.classList.add('d-none');
        icon.classList.remove('fa-minus-circle');
        icon.classList.add('fa-plus-circle');
        btn.classList.remove('expanded');
    }
}

function populateRowDetails(row, detailsRow) {
    const detailsContent = detailsRow.querySelector('.row-details-content');
    let detailsHTML = '';

    //console.log('Populating row details...');

    // Get all cells from the row
    const cells = row.querySelectorAll('td[data-column]');
    //console.log('Found cells:', cells.length);

    const screenWidth = window.innerWidth;
    let startFromColumn = 1; // Default: show all columns

    // Determine which columns should be hidden based on screen width
    if (screenWidth <= 576) {
        startFromColumn = 3; // Hide from 3rd column onwards (show first 2)
    } else if (screenWidth <= 768) {
        startFromColumn = 4; // Hide from 4th column onwards (show first 3)
    }

    //console.log(`Screen width: ${screenWidth}px, hiding from column ${startFromColumn} onwards`);

    cells.forEach((cell, index) => {
        const column = cell.getAttribute('data-column');
        const label = cell.getAttribute('data-label');
        const cellIndex = index + 1; // 1-based index

        //console.log(`Cell ${cellIndex}: ${column}`);

        // Check if this column should be shown in details (based on our nth-child logic)
        const shouldBeHidden = cellIndex >= startFromColumn;

        if (shouldBeHidden) {
            // Extract content more carefully
            let value = '';

            if (column === 'acciones') {
                // For actions column, get the button group
                const btnGroup = cell.querySelector('.btn-group');
                if (btnGroup) {
                    value = btnGroup.outerHTML;
                }
            } else {
                // First try to get content from .cell-content
                const contentSpan = cell.querySelector('.cell-content');
                if (contentSpan) {
                    value = contentSpan.innerHTML;
                } else {
                    // If no .cell-content, get all text but exclude the expand button
                    const clonedCell = cell.cloneNode(true);
                    const expandBtn = clonedCell.querySelector('.row-expand-btn');
                    if (expandBtn) {
                        expandBtn.remove();
                    }
                    value = clonedCell.innerHTML.trim();
                }

                // Clean up the value
                value = value.replace(/<!--\[if BLOCK\]><!\[endif\]-->/g, '');
                value = value.replace(/<!--\[if ENDBLOCK\]><!\[endif\]-->/g, '');
                value = value.trim();
            }

            //console.log(`Adding to details: ${column} = ${value}`);

            if (column === 'acciones') {
                // Special styling for actions
                detailsHTML += `
                    <div class="detail-item">
                        <span class="detail-label">${label}:</span>
                        <span class="detail-value">${value}</span>
                    </div>
                `;
            } else {
                detailsHTML += `
                    <div class="detail-item">
                        <span class="detail-label">${label}:</span>
                        <span class="detail-value">${value}</span>
                    </div>
                `;
            }
        }
    });

    //console.log('Final details HTML:', detailsHTML);
    detailsContent.innerHTML = detailsHTML;

    if (detailsHTML === '') {
        detailsContent.innerHTML = '<p class="text-muted">No hay columnas ocultas para mostrar.</p>';
    }
}

function checkResponsiveVisibility() {
    //console.log('Checking responsive visibility...');
    const expandButtons = document.querySelectorAll('.row-expand-btn');
    //console.log('Found expand buttons:', expandButtons.length);

    const screenWidth = window.innerWidth;
    //console.log('Screen width:', screenWidth);

    let startFromColumn = 1; // Default: show all columns
    let hasHiddenColumns = false;

    // Determine which columns should be hidden based on screen width
    if (screenWidth <= 576) {
        startFromColumn = 3; // Hide from 3rd column onwards (show first 2)
        hasHiddenColumns = true;
    } else if (screenWidth <= 768) {
        startFromColumn = 4; // Hide from 4th column onwards (show first 3)
        hasHiddenColumns = true;
    }

    //console.log(`Hiding columns from ${startFromColumn} onwards, hasHiddenColumns: ${hasHiddenColumns}`);

    // Apply/remove responsive-hidden class to columns
    const table = document.querySelector('.responsive-table');
    if (table) {
        // Get all header cells and data cells
        const headerCells = table.querySelectorAll('thead th');
        const dataCells = table.querySelectorAll('tbody td');

        // Hide/show header cells
        headerCells.forEach((cell, index) => {
            const cellIndex = index + 1; // 1-based index
            if (hasHiddenColumns && cellIndex >= startFromColumn) {
                //console.log(`Hiding header cell ${cellIndex}`);
                cell.classList.add('responsive-hidden');
            } else {
                cell.classList.remove('responsive-hidden');
            }
        });

        // Hide/show data cells
        dataCells.forEach((cell) => {
            const rowCells = cell.parentElement.querySelectorAll('td');
            const cellIndexInRow = Array.from(rowCells).indexOf(cell) + 1; // 1-based index

            if (hasHiddenColumns && cellIndexInRow >= startFromColumn) {
                //console.log(`Hiding data cell ${cellIndexInRow}`);
                cell.classList.add('responsive-hidden');
            } else {
                cell.classList.remove('responsive-hidden');
            }
        });
    }

    // Show/hide expand buttons based on whether there are hidden columns
    expandButtons.forEach((btn, index) => {
        //console.log(`Button ${index}:`, btn);
        if (hasHiddenColumns) {
            //console.log('Showing button');
            btn.classList.remove('d-none');
            btn.style.display = 'inline-block';
        } else {
            //console.log('Hiding button');
            btn.classList.add('d-none');
            btn.style.display = 'none';
        }
    });

    // Add/remove class for browsers that don't support :has()
    const tableResponsive = document.querySelector('.table-responsive');
    if (tableResponsive) {
        if (hasHiddenColumns) {
            tableResponsive.classList.add('has-responsive-controls');
        } else {
            tableResponsive.classList.remove('has-responsive-controls');
        }
    }
}

// Initialize responsive behavior on page load
document.addEventListener('DOMContentLoaded', function() {
    //console.log('DOM loaded, checking responsive visibility...');
    setTimeout(checkResponsiveVisibility, 500);
});

// Check visibility on window resize
window.addEventListener('resize', function() {
    checkResponsiveVisibility();

    // Close all expanded details on resize
    document.querySelectorAll('.row-expand-btn.expanded').forEach(btn => {
        const icon = btn.querySelector('i');
        const row = btn.closest('tr');
        const rowId = row.getAttribute('data-row-id');
        const detailsRow = document.querySelector(`tr.row-details[data-parent-row="${rowId}"]`);

        if (detailsRow && !detailsRow.classList.contains('d-none')) {
            detailsRow.classList.add('d-none');
            icon.classList.remove('fa-minus-circle');
            icon.classList.add('fa-plus-circle');
            btn.classList.remove('expanded');
        }
    });
});

// Reinitialize on Livewire updates
document.addEventListener('livewire:navigated', function() {
    setTimeout(checkResponsiveVisibility, 100);
});
