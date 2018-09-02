<div class="row border-bottom">
    <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
        @if(env('APP_ENV') == 'production' && Auth::user()->id == 1)
            <div class="alert alert-danger">
                <strong>You are currently on production!</strong>
            </div>
        @endif
        @if(Auth::user()->out_of_office)
                <div class="alert alert-danger">
                    <div class="row">
                        <div class="col-md-8"><h4 class="m-b-none">You are currently out of office</h4></div>
                        <div class="col-md-4" style="text-align: right;">
                            <form class="form-horizontal" role="form" method="POST" action="{{ action('UserController@endCurrentOoo', Auth::user()) }}">
                                {{ csrf_field() }}
                                <input type="submit" value="End period" class="btn btn-primary btn-xs" />
                            </form>
                        </div>
                    </div>
                </div>
        @endif
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
        </div>
        <ul class="nav navbar-top-links navbar-right">
            <li>
                @include('partials.user.weekly_hours', ['user' => Auth::user()])
            </li>
            <li>
                <a href="{{ url('/settings') }}">
                    <i class="fa fa-gear"></i> Settings
                </a>
            </li>
            <li>
                @if(env('APP_ENV', 'local') == 'local')
                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>

                    <a href="{{ url('/logout') }}"
                        onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                @else
                    <a href="{{ url('/saml2/logout') }}">
                @endif
                    <i class="fa fa-sign-out"></i> Log out
                </a>
            </li>
        </ul>
    </nav>
</div>
