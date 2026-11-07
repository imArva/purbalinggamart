/* Purbalingga Mart - UI helper only.
 * Tidak ada localStorage, data demo, kalkulasi transaksi, atau logika penyimpanan.
 * Seluruh proses bisnis ditangani oleh endpoint/controller Laravel.
 */
(function () {
  'use strict';

  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('menuOverlay');
  const toggle = document.getElementById('menuToggle');

  function closeMenu() {
    if (sidebar) sidebar.classList.remove('show');
    if (overlay) overlay.classList.remove('show');
  }

  if (toggle) {
    toggle.addEventListener('click', function () {
      if (sidebar) sidebar.classList.toggle('show');
      if (overlay) overlay.classList.toggle('show');
    });
  }
  if (overlay) overlay.addEventListener('click', closeMenu);

  const posPage = document.getElementById('productGrid');
  if (posPage) {
    const csrfToken = document.querySelector('input[name="_token"]')?.value;
    const searchForm = document.getElementById('posSearchForm');
    const productCount = document.getElementById('productCount');
    const cartBody = document.getElementById('cartBody');
    const cartCount = document.getElementById('cartCount');
    const cartSubtotal = document.getElementById('cartSubtotal');
    const cartDiscount = document.getElementById('cartDiscount');
    const cartTotal = document.getElementById('cartTotal');
    const checkoutButton = document.querySelector('#checkoutForm button[type="submit"]');

    const formatCurrency = (value) => 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (character) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[character]);

    async function requestJson(url, options = {}) {
      const response = await fetch(url, {
        credentials: 'same-origin',
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
        },
        ...options
      });

      const data = await response.json();
      if (!response.ok) throw new Error(data.message || 'Permintaan gagal diproses.');
      return data;
    }

    function renderProducts(products) {
      productCount.textContent = products.length + ' item';
      posPage.innerHTML = products.length ? products.map((product) => `
        <form class="pos-product ${product.stock <= 0 ? 'out-of-stock' : ''}" method="POST" action="/kasir/keranjang" data-cart-add>
          <input type="hidden" name="product_id" value="${product.id}">
          <button type="submit" class="border-0 bg-transparent p-0 w-100 text-start" ${product.stock <= 0 ? 'disabled' : ''}>
            <div class="product-art tone-blue"><i class="bi bi-box-seam"></i></div>
            <div class="product-name">${escapeHtml(product.name)}</div>
            <div class="product-code">${escapeHtml(product.code)}</div>
            <div class="product-price">${formatCurrency(product.price)}</div>
            <div class="stock-small"><span>Stok ${product.stock}</span>${product.stock <= product.min_stock ? '<span class="text-warning"><i class="bi bi-exclamation-circle"></i></span>' : ''}</div>
            <span class="add-plus"><i class="bi bi-plus-lg"></i></span>
          </button>
        </form>
      `).join('') : '<div class="empty-state empty-filter"><i class="bi bi-search"></i><strong>Produk tidak ditemukan</strong><span>Produk akan ditampilkan setelah data tersedia.</span></div>';
    }

    function renderCart(data) {
      cartCount.textContent = data.cart_count;
      cartBody.innerHTML = data.cart_items.length ? data.cart_items.map((item) => `
        <div class="cart-item">
          <div class="table-avatar tone-blue"><i class="bi bi-box-seam"></i></div>
          <div class="cart-item-main">
            <div class="cart-item-name">${escapeHtml(item.name)}</div>
            <div class="cart-item-price">${formatCurrency(item.price)}</div>
            <div class="cart-item-bottom">
              <form class="qty-control" method="POST" action="/kasir/keranjang/kuantitas" data-cart-quantity>
                <input type="hidden" name="product_id" value="${item.product_id}">
                <button name="change" value="-1">−</button><span>${item.quantity}</span><button name="change" value="1">+</button>
              </form>
              <span class="line-total">${formatCurrency(item.price * item.quantity)}</span>
            </div>
          </div>
        </div>
      `).join('') : '<div class="empty-cart"><i class="bi bi-basket"></i><strong>Keranjang masih kosong</strong><span>Pilih produk untuk memulai transaksi.</span></div>';
      cartSubtotal.textContent = formatCurrency(data.subtotal);
      cartDiscount.textContent = formatCurrency(data.discount);
      cartTotal.textContent = formatCurrency(data.total);
      if (checkoutButton) checkoutButton.disabled = data.cart_count <= 0;
    }

    async function refreshProducts(url) {
      const data = await requestJson(url);
      renderProducts(data.products);
    }

    document.addEventListener('click', async (event) => {
      const filter = event.target.closest('[data-pos-filter]');
      if (!filter) return;
      event.preventDefault();
      try {
        await refreshProducts(filter.href);
        document.querySelectorAll('[data-pos-filter]').forEach((item) => item.classList.remove('active'));
        filter.classList.add('active');
      } catch (error) {
        window.alert(error.message);
      }
    });

    document.addEventListener('submit', async (event) => {
      const form = event.target;
      if (!form.matches('[data-cart-add], [data-cart-clear], [data-cart-quantity]')) return;
      event.preventDefault();
      const button = event.submitter;
      const formData = new FormData(form);
      if (button?.name) formData.set(button.name, button.value);
      try {
        const data = await requestJson(form.action, {
          method: form.dataset.cartClear !== undefined ? 'DELETE' : 'POST',
          body: new URLSearchParams(formData)
        });
        renderCart(data);
      } catch (error) {
        window.alert(error.message);
      }
    });

    if (searchForm) {
      searchForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const params = new URLSearchParams(new FormData(searchForm));
        try {
          await refreshProducts(`${searchForm.action}?${params.toString()}`);
        } catch (error) {
          window.alert(error.message);
        }
      });
    }
  }


  // Modal form population is presentation-only; submission is handled by Laravel.
  const productModal = document.getElementById('productModal');
  if (productModal) {
    productModal.addEventListener('show.bs.modal', function (event) {
      const trigger = event.relatedTarget;
      const form = document.getElementById('productForm');
      const modalTitle = productModal.querySelector('.modal-title');
      if (!form) return;
      form.reset();
      const data = trigger && trigger.dataset.product ? JSON.parse(trigger.dataset.product) : null;
      document.getElementById('productId').value = data ? data.id : '';
      document.getElementById('productMethod').value = data ? 'PUT' : 'POST';
      form.action = data ? '/produk/' + data.id : '/produk';
      const fields = { product_code: 'product_code', name: 'name', category_id: 'category_id', price: 'price', stock: 'stock', min_stock: 'min_stock' };
      Object.keys(fields).forEach(function (key) {
        const input = form.elements[key];
        if (input && data && data[fields[key]] !== undefined) input.value = data[fields[key]];
        modalTitle.textContent = data ? 'Edit Produk' : 'Tambah Produk';
      });
    });
  }

  const categoryModal = document.getElementById('categoryModal');
  if (categoryModal) {
    categoryModal.addEventListener('show.bs.modal', function (event) {
      const trigger = event.relatedTarget;
      const form = document.getElementById('categoryForm');
      const title = categoryModal.querySelector('.modal-title');
      if (!form) return;
      form.reset();
      const data = trigger && trigger.dataset.category ? JSON.parse(trigger.dataset.category) : null;
      document.getElementById('categoryId').value = data ? data.id : '';
      document.getElementById('categoryMethod').value = data ? 'PUT' : 'POST';
      form.action = data ? '/kategori/' + data.id : '/kategori';
      if (data) {
        // Modal title jadi "Edit Kategori" jika ada data kategori, atau "Tambah Kategori" jika tidak ada.
        if (title) title.textContent = 'Edit Kategori';
        form.elements.category_name.value = data.category_name || '';
        form.elements.description.value = data.description || '';
      } else {
        // Modal title jadi "Tambah Kategori" jika tidak ada data kategori.
        if (title) title.textContent = 'Tambah Kategori';
      }
    });
  }

  const stockModal = document.getElementById('stockModal');
  if (stockModal) {
    stockModal.addEventListener('show.bs.modal', function (event) {
      const trigger = event.relatedTarget;
      const data = trigger && trigger.dataset.product ? JSON.parse(trigger.dataset.product) : null;
      const select = stockModal.querySelector('select[name="product_id"]');
      if (data && select) select.value = data.id;
    });
  }

  const chartCanvas = document.getElementById('salesChart');
  if (chartCanvas && window.Chart) {
    let labels = [], values = [];
    try {
      labels = JSON.parse(chartCanvas.dataset.labels || '[]');
      values = JSON.parse(chartCanvas.dataset.values || '[]');
    } catch (error) {
      console.warn('Data grafik tidak valid.', error);
    }
    new Chart(chartCanvas, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Penjualan',
          data: values,
          borderColor: '#0758c9',
          backgroundColor: 'rgba(7, 88, 201, .10)',
          fill: true,
          tension: .38,
          borderWidth: 2.5,
          pointRadius: 3,
          pointHoverRadius: 5,
          pointBackgroundColor: '#fff',
          pointBorderColor: '#0758c9'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false }, border: { display: false }, ticks: { color: '#929cad', font: { size: 10 } } },
          y: { beginAtZero: true, grid: { color: '#f0f2f7' }, border: { display: false }, ticks: { color: '#929cad', font: { size: 10 } } }
        }
      }
    });
  }
})();
