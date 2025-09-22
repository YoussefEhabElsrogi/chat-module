<!-- BEGIN VENDOR JS-->
<script src="{{ asset('assets/dashboard/vendors/js/vendors.min.js') }}" type="text/javascript"></script>
<!-- BEGIN VENDOR JS-->
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{ asset('assets/dashboard/vendors/js/charts/chartist.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/dashboard/vendors/js/charts/chartist-plugin-tooltip.min.js') }}" type="text/javascript">
</script>
<script src="{{ asset('assets/dashboard/vendors/js/charts/raphael-min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/dashboard/vendors/js/charts/morris.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/dashboard/vendors/js/timeline/horizontal-timeline.js') }}" type="text/javascript">
</script>
<!-- END PAGE VENDOR JS-->
<!-- BEGIN MODERN JS-->
<script src="{{ asset('assets/dashboard/js/core/app-menu.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/dashboard/js/core/app.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/dashboard/js/scripts/customizer.js') }}" type="text/javascript"></script>
<!-- END MODERN JS-->
<!-- BEGIN PAGE LEVEL JS-->
<script src="{{ asset('assets/dashboard/js/scripts/pages/dashboard-ecommerce.js') }}" type="text/javascript"></script>
<!-- END PAGE LEVEL JS-->

{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- SATAR DATATABLES --}}
{{-- DataTables CDN --}}
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.min.js"></script>

{{-- Buttons DataTables --}}
<script src="https://cdn.datatables.net/buttons/3.2.4/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.4/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.4/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.4/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.4/js/buttons.print.min.js"></script>

{{-- Excel DataTables --}}
<script src="{{ asset('vendor/datatables/excel/jszip.min.js') }}"></script>

{{-- PDF DataTables --}}
<script src="{{ asset('vendor/datatables/pdf/pdfmake.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/pdf/vfs_fonts.js') }}"></script>

{{-- Responsive DataTables --}}
<script src="https://cdn.datatables.net/responsive/3.0.5/js/dataTables.responsive.min.js"></script>

{{-- ColReorder DataTables --}}
<script src="https://cdn.datatables.net/colreorder/2.1.1/js/dataTables.colReorder.min.js"></script>

{{-- RowReorder DataTables --}}
<script src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.min.js"></script>

{{-- Select DataTables --}}
<script src="https://cdn.datatables.net/select/3.0.1/js/dataTables.select.min.js"></script>

{{-- FixedHeader DataTables --}}
<script src="https://cdn.datatables.net/fixedheader/4.0.3/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/4.0.3/js/fixedHeader.bootstrap5.min.js"></script>

{{-- Scroller DataTables --}}
<script src="https://cdn.datatables.net/scroller/2.4.3/js/dataTables.scroller.min.js"></script>
<script src="https://cdn.datatables.net/scroller/2.4.3/js/scroller.bootstrap5.min.js"></script>
{{-- END DATATABLES --}}

{{-- START FILE INPUT --}}
<script src="{{ asset('vendor/file-input/js/fileinput.min.js') }}"></script>
<script src="{{ asset('vendor/file-input/themes/fa5/theme.min.js') }}"></script>
<script src="{{ asset('vendor/file-input/js/locales/LANG.js') }}"></script>
<script src="{{ asset('vendor/file-input/js/locales/ar.js') }}"></script>

@if (Config::get('app.locale') == 'ar')
    <script src="{{ asset('vendor/file-input/js/locales/LANG.js') }}"></script>
    <script src="{{ asset('vendor/file-input/js/locales/ar.js') }}"></script>
@endif

{{-- END FILE INPUT --}}

{{-- Custom Helper Scripts --}}
<script src="{{ asset('assets/dashboard/js/scripts/helpers/change-status.js') }}" type="text/javascript"></script>

{{-- SweetAlert2 --}}
<script>
    // translations
    var title = "{{ __('dashboard.delete_confirm_title') }}";
    var text = "{{ __('dashboard.delete_confirm_text') }}";
    var confirm = "{{ __('dashboard.delete_confirm_yes') }}";
    var cancel = "{{ __('dashboard.delete_confirm_no') }}";
    var deletedTitle = "{{ __('dashboard.delete_done_title') }}";
    var deletedText = "{{ __('dashboard.delete_done_text') }}";
    var cancelTitle = "{{ __('dashboard.delete_cancel_title') }}";
    var cancelText = "{{ __('dashboard.delete_cancel_text') }}";

    $(document).on("click", ".delete_confirm", function (event) {
        event.preventDefault();

        form = $(this).closest("form");

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger",
            },
            buttonsStyling: true,
        });

        swalWithBootstrapButtons
            .fire({
                title: title,
                text: text,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: confirm,
                cancelButtonText: cancel,
                reverseButtons: true,
            })
            .then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                    swalWithBootstrapButtons.fire({
                        title: deletedTitle,
                        text: deletedText,
                        icon: "success",
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: cancelTitle,
                        text: cancelText,
                        icon: "error",
                    });
                }
            });
    });
</script>

{{-- File Input Scripts --}}
<script>
    var lang = "{{ app()->getLocale() }}";
    $(function () {
        $('#single-image').fileinput({
            theme: 'fa5',
            language: lang,
            allowedFileTypes: ['image'],
            maxFileCount: 1,
            enableResumableUpload: false,
            showUpload: false,
        });
    });
</script>
