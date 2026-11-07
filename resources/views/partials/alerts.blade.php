{{-- Toast / flash message partial --}}
@if (session('success') || session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                window.cocToast && window.cocToast('{{ addslashes(session('success')) }}', 'success');
            @endif
            @if (session('error'))
                window.cocToast && window.cocToast('{{ addslashes(session('error')) }}', 'error');
            @endif
        });
    </script>
@endif
