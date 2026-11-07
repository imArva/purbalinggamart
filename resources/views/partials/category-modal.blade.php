<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <form id="categoryForm" method="POST" action="{{ url('/kategori') }}">
            @csrf
            <input type="hidden" name="id" id="categoryId" /><input
               type="hidden"
               name="_method"
               id="categoryMethod"
               value="POST"
            />
            <div class="modal-header">
               <h5 class="modal-title">Tambah Kategori</h5>
               <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  aria-label="Tutup"
               ></button>
            </div>
            <div class="modal-body">
               <div class="mb-3">
                  <label class="form-label">Nama kategori</label
                  ><input
                     name="category_name"
                     class="form-control"
                     required
                     placeholder="Contoh: Minuman"
                  />
               </div>
               <div>
                  <label class="form-label">Deskripsi</label
                  ><textarea
                     name="description"
                     rows="3"
                     class="form-control"
                     placeholder="Keterangan kategori (opsional)"
                  ></textarea>
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
                  Simpan kategori
               </button>
            </div>
         </form>
      </div>
   </div>
</div>
