<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <span class="block m-t-xs m-b-sm">
                    <a href="{{ url('/settings') }}">
                        <strong class="font-bold">{{ Auth::user()->name }}</strong>
                    </a>
                </span>
                <span class="label label-primary">Level {{ Auth::user()->level }}</span>
            </li>
            <li class="{{ Request::is('dashboard*') ? 'active' : '' }}">
                @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                    @if (Auth::user()->isManager() || Auth::user()->getAccessibleClientsQuery()->count() > 0)
                        <a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboard</span> <span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level collapse">
                            <li class="{{ Request::is('dashboard/admin') ? 'active' : '' }}">
                                <a href="{{ action('DashboardController@admin') }}">Admin</a>
                            </li>
                            @if (Auth::user()->isManager())
                                <li class="{{ Request::is('dashboard/manager') ? 'active' : '' }}">
                                    <a href="{{ action('DashboardController@manager') }}">Manager</a>
                                </li>
                            @endif
                            <li class="{{ Request::is('dashboard/employee') ? 'active' : '' }}">
                                <a href="{{ action('DashboardController@employee') }}">Employee</a>
                            </li>
                        </ul>
                    @else
                        <a href="{{ action('DashboardController@admin') }}">
                            <i class="fa fa-th-large"></i>
                            <span class="nav-label">Dashboard</span>
                        </a>
                    @endif
                @else
                    <a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboard</span> <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        @if (Auth::user()->isManager())
                            <li class="{{ Request::is('dashboard/manager') ? 'active' : '' }}">
                                <a href="{{ action('DashboardController@manager') }}">Manager</a>
                            </li>
                            <li class="{{ Request::is('dashboard/employee') ? 'active' : '' }}">
                                <a href="{{ action('DashboardController@employee') }}">Employee</a>
                            </li>
                        @elseif(Auth::user()->hasRole(\App\Repositories\User\User::ROLE_CUSTOMER_SERVICE))
                            <li class="{{ Request::is('dashboard/employee') ? 'active' : '' }}">
                                <a href="{{ action('DashboardController@employee') }}">Employee</a>
                            </li>
                        @else
                            <li class="{{ Request::is('dashboard/employee') ? 'active' : '' }}">
                                <a href="{{ action('DashboardController@employee') }}">Employee</a>
                            </li>
                        @endif
                        <li class="{{ Request::is('dashboard/tasks') ? 'active' : '' }}">
                            <a href="{{ action('DashboardController@tasks') }}">Task Report</a>
                        </li>
                    </ul>
                @endif
            </li>
            <li class="{{ Request::is('clients*') ? 'active' : '' }}">
                <a href="#"><i class="fa fa-building"></i> <span class="nav-label">Clients</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    @if(Auth::user()->hasRole(\App\Repositories\User\User::ROLE_CUSTOMER_SERVICE))
                        <li class="{{ Request::is('clients/all') ? 'active' : '' }}">
                            <a href="{{ url('/clients/all') }}"><i class="fa fa-building"></i> <span class="nav-label">All Clients</span></a>
                        </li>
                    @else
                        <li class="{{ Request::is('clients') ? 'active' : '' }}">
                            <a href="{{ url('/clients') }}"><i class="fa fa-building"></i> <span class="nav-label">Active Clients</span></a>
                        </li>
                        <li class="{{ Request::is('clients/old') ? 'active' : '' }}">
                            <a href="{{ url('/clients/old') }}"><i class="fa fa-building"></i> <span class="nav-label">Old Clients</span></a>
                        </li>
                    @endif
                    <li class="{{ Request::is('clients/internal') ? 'active' : '' }}">
                        <a href="{{ url('/clients/internal') }}"><i class="fa fa-building"></i> <span class="nav-label">Internal Projects</span></a>
                    </li>
                    @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                        <li class="{{ Request::is('clients/paused') ? 'active' : '' }}">
                            <a href="{{ url('/clients/paused') }}"><i class="fa fa-building"></i> <span class="nav-label">Paused Clients</span></a>
                        </li>
                        <li class="{{ Request::is('clients/deactivated') ? 'active' : '' }}">
                            <a href="{{ url('/clients/deactivated') }}"><i class="fa fa-building"></i> <span class="nav-label">Deactivated</span></a>
                        </li>
                    @endif
                </ul>
            </li>
            @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                <li class="{{ (Request::is('templates*') || Request::is('system_settings/*_templates*')) ? 'active' : '' }}">
                    <a href="#"><i class="fa fa-th-list"></i> <span class="nav-label">Templates</span> <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li class="{{ Request::is('templates') ? 'active' : '' }}">
                            <a href="{{ url('/templates') }}">Task Templates</a>
                        </li>
                        <li class="{{ Request::is('system_settings/email_templates') ? 'active' : '' }}">
                            <a href="{{ route('email_templates.index') }}">Email Templates</a>
                        </li>
                        <li class="{{ Request::is('system_settings/rating_templates*') ? 'active' : '' }}">
                            <a href="{{ route('rating_templates.index') }}">Rating Templates</a>
                        </li>
                    </ul>
                </li>
                <li class="{{ Request::is('users*') ? 'active' : '' }}">
                    <a href="#"><i class="fa fa-users"></i> <span class="nav-label">Users</span> <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li class="{{ Request::is('users') ? 'active' : '' }}">
                            <a href="{{ url('/users') }}"><i class="fa fa-users"></i> <span class="nav-label">Active Users</span></a>
                        </li>
                        <li class="{{ Request::is('users/deactivated') ? 'active' : '' }}">
                            <a href="{{ url('/users/deactivated') }}"><i class="fa fa-user-times"></i> <span class="nav-label">Deactivated</span></a>
                        </li>
                    </ul>
                </li>
            @endif
            @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN) || Auth::user()->hasRole(\App\Repositories\User\User::ROLE_CUSTOMER_SERVICE))
                <li class="{{ Request::is('reports*') ? 'active' : '' }}">
                    <a href="#"><i class="fa fa-bar-chart"></i> <span class="nav-label">Reports</span> <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                            <li class="{{ Request::is('reports/tasks/all*') ? 'active' : '' }}">
                                <a href="{{ url('/reports/tasks/all') }}"><span class="nav-label">Tasks</span></a>
                            </li>
                            <li class="{{ Request::is('reports/rating*') ? 'active' : '' }}">
                                <a href="{{ url('/reports/rating') }}"><span class="nav-label">Ratings</span></a>
                            </li>
                        @endif
                        <li class="{{ Request::is('reports/capacity') ? 'active' : '' }}">
                            <a href="{{ url('/reports/capacity') }}"><span class="nav-label">Capacity</span></a>
                        </li>
                        @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                            <li class="{{ Request::is('reports/overdue*') ? 'active' : '' }}">
                                <a href="{{ url('/reports/overdue') }}"><span class="nav-label">Overdue</span></a>
                            </li>
                            <li class="{{ Request::is('reports/2') ? 'active' : '' }}">
                                <a href="{{ url('/reports/2') }}"><span class="nav-label">Weekly Tasks</span></a>
                            </li>
                            <li class="{{ Request::is('reports/tasks/overdue*') ? 'active' : '' }}">
                                <a href="{{ url('/reports/tasks/overdue') }}"><span class="nav-label">Overdue Tasks</span></a>
                            </li>
                            <li class="{{ Request::is('reports/clients/overdue*') ? 'active' : '' }}">
                                <a href="{{ url('/reports/clients/overdue') }}"><span class="nav-label">Overdue Client</span></a>
                            </li>
                            <li class="{{ Request::is('reports/filter/overdue*') ? 'active' : '' }}">
                                <a href="{{ url('/reports/filter/overdue') }}"><span class="nav-label">Overdue Reason</span></a>
                            </li>
                            <li class="{{ Request::is('reports/1') ? 'active' : '' }}">
                                <a href="{{ url('/reports/1') }}"><span class="nav-label">Aggregated Overdue</span></a>
                            </li>
                            <li class="{{ Request::is('reports/it*') ? 'active' : '' }}">
                                <a href="{{ url('/reports/it') }}"><span class="nav-label">IT Report</span></a>
                            </li>
                        @endif
                    </ul>
                </li>
                <li  class="{{ ((Request::is('system_settings*') && ! Request::is('system_settings/*_templates*')) || Request::is('users_information*')) ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-gear"></i>
                        <span class="nav-label">System Settings</span>
                        <span class="fa arrow"></span>
                        @if ($processedNotificationPending > 0 && Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                            <span class="label label-danger pull-right" style="margin-right: 10px;">{{$processedNotificationPending}}</span>
                        @endif
                    </a>
                    <ul class="nav nav-second-level collapse">
                        @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                            <li class="{{ Request::is('system_settings/faq-categories*') ? 'active' : '' }}">
                                <a href="{{ action('FaqCategoryController@index') }}">FAQ Settings</a>
                            </li>
                            <li class="{{ Request::is('system_settings/groups') ? 'active' : '' }}">
                                <a href="{{ route('groups.index') }}">User Groups</a>
                            </li>
                            <li class="{{ Request::is('system_settings/flags') ? 'active' : '' }}">
                                <a href="{{ route('settings.flags.index') }}">User Flagging</a>
                            </li>
                            <li class="{{ Request::is('system_settings/users_information*') ? 'active' : '' }}">
                                <a href="{{ route('settings.information.index') }}">User Information</a>
                            </li>
                            <li class="{{ Request::is('system_settings/templates/notifications/pending') ? 'active' : '' }}">
                                <a href="{{ route('templates.notifications.processed.pending') }}">
                                    <span class="nav-label">Pending Notifications</span>
                                    @if ($processedNotificationPending > 0)
                                        <span class="label label-danger pull-right" style="margin-top: -30px;">{{$processedNotificationPending}}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="{{ Request::is('system_settings/templates/notifications/declined') ? 'active' : '' }}">
                                <a href="{{ route('templates.notifications.processed.declined') }}">
                                    <span class="nav-label">Declined Notifications</span>
                                </a>
                            </li>
                            <li class="{{ Request::is('system_settings/faq-categories*') ? 'active' : '' }}">
                                <a href="{{ action('FaqCategoryController@index') }}">FAQ Settings</a>
                            </li>
                            <li class="{{ Request::is('system_settings/groups') ? 'active' : '' }}">
                                <a href="{{ route('groups.index') }}">User Groups</a>
                            </li>
                            <li class="{{ Request::is('system_settings/flags') ? 'active' : '' }}">
                                <a href="{{ route('settings.flags.index') }}">User Flagging</a>
                            </li>
                            <li class="{{ Request::is('system_settings/users_information*') ? 'active' : '' }}">
                                <a href="{{ route('settings.information.index') }}">User Information</a>
                            </li>
                            <li class="{{ Request::is('system_settings/contacts') ? 'active' : '' }}">
                                <a href="{{ action('ContactController@index') }}">Client Contacts</a>
                            </li>
                            <li class="{{ Request::is('system_settings/review/settings') ? 'active' : '' }}">
                                <a href="{{ route('review.settings.show') }}">Review Settings</a>
                            </li>
                            <li class="{{ Request::is('system_settings/options*') ? 'active' : '' }}">
                                <a href="{{ route('settings.options.index') }}">Option Settings</a>
                            </li>
                            <li class="{{ Request::is('system_settings/overdue') ? 'active' : '' }}">
                                <a href="{{ action('OverdueReasonController@index') }}">Overdue Settings</a>
                            </li>
                            <li class="{{ Request::is('system_settings/systems*') ? 'active' : '' }}">
                                <a href="{{ route('systems.index') }}">Software Settings</a>
                            </li>
                            <li class="{{ Request::is('/system_settings/ooo-reasons') ? 'active' : '' }}">
                                <a href="{{ route('settings.ooo.index') }}">Out Of Office Settings</a>
                            </li>
                        @endif
                        <li class="{{ Request::is('system_settings/sms*') ? 'active' : '' }}">
                            <a href="{{ route('settings.sms.index') }}">SMS Sent Logs</a>
                        </li>
                        <li class="{{ Request::is('system_settings/phone_call_logs') ? 'active' : '' }}">
                            <a href="{{ route('settings.phone.logs.index') }}">Phone Call Logs</a>
                        </li>
                        @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                            <li class="{{ Request::is('system_settings/phone_system') ? 'active' : '' }}">
                                <a href="{{ route('settings.phone.index') }}">Phone System Settings</a>
                            </li>
                            <li class="{{ Request::is('system_settings/folder-structure*') ? 'active' : '' }}">
                                <a href="{{ action('FoldersStructureController@index') }}">Client Folder Structure</a>
                            </li>
                            <li class="{{ Request::is('system_settings/library*') ? 'active' : '' }}">
                                <a href="{{ action('LibraryController@indexSettings') }}">Library Folder Structure</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
            @if (Auth::user()->isInReviewerGroup())
            <li  class="{{ Request::is('reviews*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-gear"></i>
                    <span class="nav-label">Reviews</span>
                    @if ($reviewsPending > 0)
                        <span class="label label-danger pull-right" style="margin-left: 10px;">{{$reviewsPending}}</span>
                    @endif
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ Request::is('reviews/pending') ? 'active' : '' }}">
                        <a href="{{ url('/reviews/pending') }}">
                            <i class="fa fa-edit"></i>
                            <span class="nav-label">Reviews pending</span>
                            @if ($reviewsPending > 0)
                                <span class="label label-danger pull-right">{{$reviewsPending}}</span>
                            @endif
                        </a>
                    </li>
                    <li class="{{ Request::is('reviews/completed') ? 'active' : '' }}">
                        <a href="{{ url('/reviews/completed') }}">
                            <i class="fa fa-edit"></i>
                            <span class="nav-label">Reviews completed</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            <li class="{{ Request::is('library*') ? 'active' : '' }}">
                <a href="{{ route('library.index') }}"><i class="fa fa-files-o"></i> <span class="nav-label">File Template Library</span></a>
            </li>
            <li class="{{ Request::is('information*') ? 'active' : '' }}">
                <a href="{{ route('information.index') }}"><i class="fa fa-sticky-note"></i> <span class="nav-label">Information</span></a>
            </li>
            <li>
                <a href="#"><i class="fa fa-pencil-square"></i> <span class="nav-label">Accounting Software</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li>
                        <a href="https://synega.onelogin.com/launch/618839" target="_blank">Fiken</a>
                    </li>
                    <li>
                        <a href="https://synega.onelogin.com/launch/622170" target="_blank">Tripletex</a>
                    </li>
                    <li>
                        <a href="https://synega.onelogin.com/launch/756168" target="_blank">PowerOffice Go</a>
                    </li>
                    <li>
                        <a href="https://synega.onelogin.com/launch/622173" target="_blank">Visma e-Accounting</a>
                    </li>
                    <li>
                        <a href="https://synega.onelogin.com/launch/659145" target="_blank">Kontohjelp</a>
                    </li>
                    <li>
                        <a href="https://synega.onelogin.com/launch/724038" target="_blank">Accountor Training</a>
                    </li>
                    @if (Auth::user()->isManager())                    
                        <li>
                            <a href="https://synega.onelogin.com/launch/821256" target="_blank">Sticos</a>
                        </li>
                    @endif
                    <li>
                        <a href="https://app.signere.no/SignereIDLogin/LoginSMSOtp" target="_blank">Signere</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#"><i class="fa fa-envelope"></i> <span class="nav-label">Communication</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li>
                        <a href="{{ route('information.zendesk') }}" target="_blank">Email</a>
                    </li>
                    <li>
                        <a href="https://synega.onelogin.com/launch/618070" target="_blank">Chat</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('information.harvest') }}" target="_blank"><i class="fa fa-clock-o"></i> <span class="nav-label">Time Recording</span></a>
            </li>
            <li>
                <a href="https://synega.onelogin.com/launch/654306" target="_blank"><i class="fa fa-life-ring"></i> <span class="nav-label">IT Support</span></a>
            </li>
            <li class="{{ Request::is('settings') ? 'active' : '' }}">
                <a href="{{ url('/settings') }}"><i class="fa fa-user"></i> <span class="nav-label">My Account</span></a>
            </li>
            @if (App\Repositories\FaqCategory\FaqCategory::where('active', 1)->get()->count() > 0)
                <li  class="{{ Request::is('faq*') ? 'active' : '' }}">
                    <a href="#"><i class="fa fa-question"></i> <span class="nav-label">FAQ</span> <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        @foreach (App\Repositories\FaqCategory\FaqCategory::where('active', 1)->where('visible', 1)->orderBy('order')->get() as $faqCategory)
                            <li class="{{ Request::is('faq-categories/' . $faqCategory->id) ? 'active' : '' }}">
                                <a href="{{ action('FaqCategoryController@show', $faqCategory) }}">{{ $faqCategory->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endif
            @if(Auth::user()->isAdmin())
                <li>
                    <a href="{{ route('documentation.pages.index') }}"><i class="fa fa-file-code-o"></i> <span class="nav-label">System Documentation</span></a>
                </li>
            @endif
        </ul>
    </div>
</nav>
