/**
 * SMS2 - Registrar Module JavaScript Helpers
 * Shared utilities for fetch, modals, toasts, and forms
 */

// ============================================================================
// CONFIGURATION
// ============================================================================

const REG_API_BASE = '/modules/registrar/api';
const REG_CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

// ============================================================================
// FETCH WRAPPER (CSRF-protected)
// ============================================================================

async function regFetch(endpoint, options = {}) {
  const url = `${REG_API_BASE}/${endpoint}`;
  const headers = {
    'Content-Type': 'application/json',
    'X-CSRF-Token': REG_CSRF_TOKEN,
    ...options.headers
  };

  try {
    const response = await fetch(url, {
      ...options,
      headers,
      method: options.method || 'GET'
    });

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    const data = await response.json();
    
    if (!data.success && data.error) {
      throw new Error(data.error);
    }

    return data;
  } catch (error) {
    console.error('Fetch error:', error);
    showRegToast(error.message || 'Request failed', 'error');
    throw error;
  }
}

// ============================================================================
// TOAST NOTIFICATIONS
// ============================================================================

function showRegToast(message, type = 'info', duration = 4000) {
  const toastId = 'toast-' + Date.now();
  const toast = document.createElement('div');
  toast.id = toastId;
  toast.className = `reg-toast ${type}`;
  toast.textContent = message;

  document.body.appendChild(toast);

  setTimeout(() => {
    toast.remove();
  }, duration);

  return toastId;
}

function showRegSuccess(message, duration) {
  return showRegToast(message, 'success', duration);
}

function showRegError(message, duration) {
  return showRegToast(message, 'error', duration);
}

function showRegWarning(message, duration) {
  return showRegToast(message, 'warning', duration);
}

function showRegInfo(message, duration) {
  return showRegToast(message, 'info', duration);
}

// ============================================================================
// MODALS
// ============================================================================

function showRegModal(modalId, data = {}) {
  const modal = document.getElementById(modalId);
  if (!modal) {
    console.error(`Modal not found: ${modalId}`);
    return;
  }

  // Populate modal with data if provided
  if (Object.keys(data).length > 0) {
    populateModalForm(modal, data);
  }

  const bsModal = new bootstrap.Modal(modal);
  bsModal.show();
}

function hideRegModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    const bsModal = bootstrap.Modal.getInstance(modal);
    if (bsModal) {
      bsModal.hide();
    }
  }
}

function populateModalForm(modal, data) {
  Object.keys(data).forEach(key => {
    const input = modal.querySelector(`[name="${key}"]`);
    if (input) {
      input.value = data[key];
    }
  });
}

// ============================================================================
// FORM HANDLING
// ============================================================================

async function submitRegForm(formId, endpoint, method = 'POST', redirectUrl = null) {
  const form = document.getElementById(formId);
  if (!form) {
    console.error(`Form not found: ${formId}`);
    return;
  }

  try {
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    const response = await regFetch(endpoint, {
      method,
      body: JSON.stringify(data)
    });

    if (response.success) {
      showRegSuccess(response.message || 'Operation successful');
      form.reset();

      // Redirect or refresh after 1.5 seconds
      if (redirectUrl) {
        setTimeout(() => {
          window.location.href = redirectUrl;
        }, 1500);
      } else {
        setTimeout(() => {
          location.reload();
        }, 1500);
      }
    }

    return response;
  } catch (error) {
    console.error('Form submission error:', error);
    showRegError(error.message);
    return null;
  }
}

// ============================================================================
// TABLE OPERATIONS
// ============================================================================

function initRegTable(tableSelector, options = {}) {
  const table = document.querySelector(tableSelector);
  if (!table) return;

  // Add row click handlers if needed
  table.querySelectorAll('tbody tr').forEach(row => {
    row.addEventListener('click', function() {
      if (options.onRowClick) {
        const rowData = extractRowData(this);
        options.onRowClick(rowData);
      }
    });
  });

  // Add checkbox handlers for bulk operations
  const selectAllCheckbox = table.querySelector('thead input[type="checkbox"]');
  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
      table.querySelectorAll('tbody input[type="checkbox"]').forEach(cb => {
        cb.checked = this.checked;
      });
    });
  }
}

function extractRowData(rowElement) {
  const data = {};
  rowElement.querySelectorAll('td').forEach((td, index) => {
    const header = document.querySelector(`thead th:nth-child(${index + 1})`);
    if (header) {
      const key = header.textContent.trim().toLowerCase();
      data[key] = td.textContent.trim();
    }
  });
  return data;
}

function getSelectedRows(tableSelector) {
  const table = document.querySelector(tableSelector);
  const selectedRows = [];

  table.querySelectorAll('tbody input[type="checkbox"]:checked').forEach(checkbox => {
    selectedRows.push(extractRowData(checkbox.closest('tr')));
  });

  return selectedRows;
}

// ============================================================================
// SEARCH & FILTER
// ============================================================================

async function performRegSearch(searchTerm, searchEndpoint, resultsCallback) {
  if (!searchTerm || searchTerm.length < 2) {
    resultsCallback([]);
    return;
  }

  try {
    const results = await regFetch(searchEndpoint + '?q=' + encodeURIComponent(searchTerm));
    resultsCallback(results.data || []);
  } catch (error) {
    console.error('Search error:', error);
    resultsCallback([]);
  }
}

function debounce(func, delay = 300) {
  let timeoutId;
  return function(...args) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => func(...args), delay);
  };
}

// ============================================================================
// PAGINATION
// ============================================================================

function initRegPagination(paginationSelector, onPageChange) {
  const paginationElement = document.querySelector(paginationSelector);
  if (!paginationElement) return;

  paginationElement.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const pageNum = this.getAttribute('data-page');
      if (pageNum) {
        onPageChange(parseInt(pageNum));
      }
    });
  });
}

// ============================================================================
// FILE UPLOAD
// ============================================================================

function initRegDropzone(dropzoneSelector, uploadEndpoint, onFileSelect) {
  const dropzone = document.querySelector(dropzoneSelector);
  if (!dropzone) return;

  ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, preventDefaults, false);
  });

  ['dragenter', 'dragover'].forEach(eventName => {
    dropzone.addEventListener(eventName, () => {
      dropzone.classList.add('drag-over');
    }, false);
  });

  ['dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, () => {
      dropzone.classList.remove('drag-over');
    }, false);
  });

  dropzone.addEventListener('drop', (e) => {
    const dt = e.dataTransfer;
    const files = dt.files;
    handleFilesUpload(files, uploadEndpoint, onFileSelect);
  }, false);

  // Click to upload
  dropzone.addEventListener('click', function() {
    const input = document.createElement('input');
    input.type = 'file';
    input.multiple = true;
    input.addEventListener('change', (e) => {
      handleFilesUpload(e.target.files, uploadEndpoint, onFileSelect);
    });
    input.click();
  });
}

function preventDefaults(e) {
  e.preventDefault();
  e.stopPropagation();
}

async function handleFilesUpload(files, endpoint, callback) {
  const formData = new FormData();
  Array.from(files).forEach(file => {
    formData.append('files', file);
  });

  try {
    const response = await fetch(`${REG_API_BASE}/${endpoint}`, {
      method: 'POST',
      headers: {
        'X-CSRF-Token': REG_CSRF_TOKEN
      },
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      showRegSuccess('Files uploaded successfully');
      if (callback) {
        callback(data.files || []);
      }
    } else {
      showRegError(data.error || 'Upload failed');
    }
  } catch (error) {
    console.error('Upload error:', error);
    showRegError('Upload failed: ' + error.message);
  }
}

// ============================================================================
// DATA FORMATTING
// ============================================================================

function formatDate(dateString) {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-PH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
}

function formatDateTime(dateString) {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-PH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

function formatCurrency(amount) {
  return new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP'
  }).format(amount || 0);
}

function formatFileSize(bytes) {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
}

// ============================================================================
// STATUS HELPERS
// ============================================================================

function getStatusBadgeClass(status) {
  const statusMap = {
    'active': 'badge-status active',
    'pending': 'badge-status pending',
    'processing': 'badge-status processing',
    'released': 'badge-status released',
    'inactive': 'badge-status inactive',
    'cancelled': 'badge-status inactive'
  };
  return statusMap[status.toLowerCase()] || 'badge-secondary';
}

function getChannelIcon(channel) {
  return channel === 'online' ? '🌐 Online' : '🏢 Walk-in';
}

// ============================================================================
// LOADING STATE
// ============================================================================

function showRegLoading(elementSelector) {
  const element = document.querySelector(elementSelector);
  if (element) {
    element.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
  }
}

function hideRegLoading(elementSelector) {
  const element = document.querySelector(elementSelector);
  if (element) {
    element.innerHTML = '';
  }
}

// ============================================================================
// INITIALIZATION ON DOM READY
// ============================================================================

document.addEventListener('DOMContentLoaded', function() {
  // Initialize all tooltips
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new bootstrap.Tooltip(el);
  });

  // Initialize all popovers
  document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
    new bootstrap.Popover(el);
  });
});

// Export for use in modules
window.regFetch = regFetch;
window.showRegToast = showRegToast;
window.showRegSuccess = showRegSuccess;
window.showRegError = showRegError;
window.showRegWarning = showRegWarning;
window.showRegInfo = showRegInfo;
window.showRegModal = showRegModal;
window.hideRegModal = hideRegModal;
window.submitRegForm = submitRegForm;
window.initRegTable = initRegTable;
window.performRegSearch = performRegSearch;
window.debounce = debounce;
window.initRegPagination = initRegPagination;
window.initRegDropzone = initRegDropzone;
window.formatDate = formatDate;
window.formatDateTime = formatDateTime;
window.formatCurrency = formatCurrency;
window.formatFileSize = formatFileSize;
window.getStatusBadgeClass = getStatusBadgeClass;
window.getChannelIcon = getChannelIcon;
window.showRegLoading = showRegLoading;
window.hideRegLoading = hideRegLoading;
