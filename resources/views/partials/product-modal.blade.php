<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="productForm" method="POST" action="{{ url('/produk') }}">
                @csrf
                <input type="hidden" name="id" id="productId">
                <input type="hidden" name="_method" id="productMethod" value="POST">
                <div class="modal-header">
                    <div>
                        <div class="eyebrow">MANAJEMEN INVENTARIS</div>
                        <h5 class="modal-title">Tambah Produk</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"> 
                            <label class="form-label">Kode produk</label>
                            <input name="product_code" class="form-control" required maxlength="20" placeholder="PRD-001">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama produk</label>
                            <input name="name" class="form-control" required placeholder="Nama barang">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select" required>
                                @foreach(($categories ?? collect()) as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga jual (Rp)</label>
                            <input name="price" type="number" min="1" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stok awal</label>
                            <input name="stock" type="number" min="0" class="form-control" required value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Batas stok menipis</label>
                            <input name="min_stock" type="number" min="0" class="form-control" required value="5">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Simpan produk</button>
                </div>
            </form>
        </div>
    </div>