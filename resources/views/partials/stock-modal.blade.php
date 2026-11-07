<div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <form method="POST" action="{{ url('/stok') }}">
            @csrf
            <div class="modal-header">
               <div>
                  <div class="eyebrow">INVENTARIS</div>
                  <h5 class="modal-title">Perbarui Stok</h5>
               </div>
               <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  aria-label="Tutup"
               ></button>
            </div>
            <div class="modal-body">
               <div class="mb-3">
                  <label class="form-label">Produk</label
                  ><select name="product_id" class="form-select" required>
                     @foreach(($products ?? collect()) as $product)
                     <option value="{{ $product->id }}">
                        {{ $product->name }} (stok: {{ $product->stock }})
                     </option>
                     @endforeach
                  </select>
               </div>
               <div class="row g-3">
                  <div class="col-sm-6">
                     <label class="form-label">Jenis perubahan</label
                     ><select name="action" class="form-select">
                        <option value="in">Stok masuk (+)</option>
                        <option value="out">Stok keluar (−)</option>
                        <option value="set">Atur stok akhir</option>
                     </select>
                  </div>
                  <div class="col-sm-6">
                     <label class="form-label">Jumlah</label
                     ><input
                        name="quantity"
                        type="number"
                        min="0"
                        class="form-control"
                        required
                        value="1"
                     />
                  </div>
                  <div class="col-12">
                     <label class="form-label">Catatan</label
                     ><input
                        name="note"
                        class="form-control"
                        placeholder="Contoh: Restock dari supplier"
                     />
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button
                  type="button"
                  class="btn btn-light"
                  data-bs-dismiss="modal"
               >
                  Batal</button
               ><button type="submit" class="btn btn-primary">
                  Simpan perubahan
               </button>
            </div>
         </form>
      </div>
   </div>
</div>
