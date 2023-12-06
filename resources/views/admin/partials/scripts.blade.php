{{-- toastr --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
<!-- Library Bundle Script -->
<script src="{{ asset('assets') }}/js/core/libs.min.js"></script>
<!-- External Library Bundle Script -->
<script src="{{ asset('assets') }}/js/core/external.min.js"></script>
<!-- Widgetchart Script -->
<script src="{{ asset('assets') }}/js/charts/widgetcharts.js"></script>
<!-- mapchart Script -->
<script src="{{ asset('assets') }}/js/charts/vectore-chart.js"></script>
<script src="{{ asset('assets') }}/js/charts/dashboard.js"></script>
<!-- fslightbox Script -->
<script src="{{ asset('assets') }}/js/plugins/fslightbox.js"></script>
<!-- Settings Script -->
<script src="{{ asset('assets') }}/js/plugins/setting.js"></script>
<!-- Slider-tab Script -->
<script src="{{ asset('assets') }}/js/plugins/slider-tabs.js"></script>
<!-- Form Wizard Script -->
<script src="{{ asset('assets') }}/js/plugins/form-wizard.js"></script>
<!-- AOS Animation Plugin-->
<script src="{{ asset('assets') }}/vendor/aos/dist/aos.js"></script>
<script src="{{asset('assets/js/ckeditor/ckeditor.js')}}"> </script>
{{--<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"> </script>--}}

<!-- App Script -->
<script src="{{ asset('assets') }}/js/hope-ui.js" defer></script>
{{-- select2 --}}
<script src="{{ asset('assets/js/plugins/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.close-sidebar').on('click', function() {
            if ($('.navs-rounded-all').hasClass('sidebar-mini')) {
                $('.navs-rounded-all').removeClass('sidebar-mini');
            } else {
                $('.navs-rounded-all').addClass('sidebar-mini');
            }
        })
    })
</script>
@stack('extra-scripts')
