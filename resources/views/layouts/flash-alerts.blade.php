{{-- Success --}}
@if (session('success'))
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                <div class="alert alert-success m-b-none">
                    <strong>Success!</strong> {{ session('success') }}
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Info --}}
@if (session('info'))
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                <div class="alert alert-info m-b-none">
                    {{ session('info') }}
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Error --}}
@if (session('error'))
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                <div class="alert alert-danger m-b-none">
                    <strong>Error!</strong> {{ session('error') }}
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Errors --}}
@if ($errors->any())
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                <div class="alert alert-danger m-b-none">
                    @foreach ($errors->all() as $error)
                        <div><strong>Error!</strong> {{ $error }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Warning --}}
@if (session('warning'))
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                <div class="alert alert-warning m-b-none">
                    {{ session('warning') }}
                </div>
            </div>
        </div>
    </div>
@endif
