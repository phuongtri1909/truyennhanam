{{-- Load SweetAlert2 library globally --}}
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Global SweetAlert2 configurations
        window.showToast = function(message, type = 'success') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            
            Toast.fire({
                icon: type,
                title: message
            });
        };
        
        window.showAlert = function(title, message, type = 'success') {
            return Swal.fire({
                icon: type,
                title: title,
                text: message,
                confirmButtonColor: 'var(--primary-color-3)'
            });
        };
        
        window.showConfirm = function(title, message, confirmCallback, cancelCallback = null, type = 'warning') {
            Swal.fire({
                icon: type,
                title: title,
                text: message,
                showCancelButton: true,
                confirmButtonColor: 'var(--primary-color-3)',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (typeof confirmCallback === 'function') {
                        confirmCallback();
                    }
                } else if (cancelCallback && typeof cancelCallback === 'function') {
                    cancelCallback();
                }
            });
        };

        // Xác nhận form với SweetAlert2 (form có class form-submit-confirm và data-message)
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form.form-submit-confirm').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const msg = this.getAttribute('data-message') || 'Xác nhận thực hiện?';
                    const withNote = form.classList.contains('form-submit-with-note');
                    const noteInputName = form.getAttribute('data-note-name') || 'submitted_note';

                    if (withNote) {
                        Swal.fire({
                            icon: 'question',
                            title: 'Gửi duyệt truyện',
                            text: msg,
                            input: 'textarea',
                            inputLabel: 'Ghi chú cho admin (tùy chọn)',
                            inputPlaceholder: 'Thêm ghi chú cho admin khi xem xét...',
                            inputAttributes: { rows: 3 },
                            showCancelButton: true,
                            confirmButtonText: 'Gửi duyệt',
                            cancelButtonText: 'Hủy',
                            confirmButtonColor: 'var(--primary-color-7, #0d6efd)',
                            cancelButtonColor: '#6c757d'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var existing = form.querySelector('input[name="' + noteInputName + '"]');
                                if (existing) {
                                    existing.value = result.value || '';
                                } else {
                                    var inp = document.createElement('input');
                                    inp.type = 'hidden';
                                    inp.name = noteInputName;
                                    inp.value = result.value || '';
                                    form.appendChild(inp);
                                }
                                form.submit();
                            }
                        });
                    } else if (form.classList.contains('form-delete-confirm')) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Xác nhận xóa',
                            text: msg,
                            showCancelButton: true,
                            confirmButtonText: 'Xóa',
                            cancelButtonText: 'Hủy',
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'question',
                            title: 'Xác nhận',
                            text: msg,
                            showCancelButton: true,
                            confirmButtonText: 'Đồng ý',
                            cancelButtonText: 'Hủy',
                            confirmButtonColor: 'var(--primary-color-7, #0d6efd)',
                            cancelButtonColor: '#6c757d'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush

{{-- Set a flag to indicate SweetAlert2 is loaded --}}
@php
    $loadedSweetAlert = true;
@endphp 