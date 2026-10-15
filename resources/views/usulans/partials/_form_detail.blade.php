@include('usulans.partials._form_detail_rt')

@if (($currentJenis ?? ($usulan->jenis ?? \App\Models\Usulan::JENIS_CCTV)) === \App\Models\Usulan::JENIS_WIFI)
    @include('usulans.partials._form_detail_wifi')
@else
    @include('usulans.partials._form_detail_cctv')
@endif

