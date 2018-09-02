@extends('layouts.app')

@section('title', 'Documentation Pages')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Documentation</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <strong>Documentation</strong>
                </li>
            </ol>
        </div>
    </div>
    <style>
        .folder-list li {
            padding-left: 10px;
        }

        .folder-list li.active {
            background: #ffffffb3;
        }

        .folder-list li.has-match-childs {
            background: #e9e9e9;
        }

        .folder-list li.matches {
            background: #ffeeccb3;
        }

        mark {
            background: #ffeeccb3;
            color: black;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">
                <div class="row">
                    <div class="col-lg-2">
                        <div class="ibox float-e-margins">
                            <div class="ibox-content mailbox-content">
                                <div class="file-manager">
                                    <div class="input-group">
                                        <input type="text" class="form-control input-sm search-matches" name="search" placeholder="Search">
                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                Search
                                            </button>
                                        </div>
                                    </div>
                                    <div class="space-25"></div>
                                    @if(Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                        <a href="{{ action('DocumentationPagesController@create') }}" class="btn btn-block btn-primary ">Create a new page</a>
                                    @endif
                                    <div class="space-25"></div>
                                    @if($documentationPages->count())
                                        <h5>Pages</h5>
                                        <ul class="folder-list pages-list m-b-md" style="padding: 0">
                                            @foreach ($documentationPages->where('parent_page_id', null) as $documentationPage)
                                                <li class="{{ $loop->first ? "active" : '' }}">
                                                    <a href="#" data-children-shown="0" class="dp-list-item" data-level="1" data-id="{{ $documentationPage->id }}"> <i class="fa fa-file-text-o"></i>
                                                        {{$documentationPage->title }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-10 animated fadeInRight">
                        @forelse ($documentationPages as $documentationPage)
                            <div data-id="{{ $documentationPage->id }}" class="page-content @if(!$loop->first) {{ 'hidden' }} @endif">
                                <div class="mail-box-header">
                                    <form method="get" action="" class="pull-right mail-search">
                                        <div class="input-group">
                                            <a href="{{ route('documentation.pages.edit', $documentationPage->id) }}" class="btn btn-block btn-primary ">Edit</a>
                                        </div>
                                    </form>
                                    <h2>
                                        {{ $documentationPage->title }}
                                    </h2>
                                </div>
                                <div class="mail-box">
                                    <div class="p-xl white-bg">
                                        <div data-converted="0" id="dp_{{ $documentationPage->id }}" class="page-markdown-content ">
                                        {!! \GrahamCampbell\Markdown\Facades\Markdown::convertToHtml($documentationPage->content) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="page-content">
                                <div class="mail-box-header">
                                    <h2 class="text-center">
                                        Create a new page to get started
                                    </h2>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        var documentationPages = @json($documentationPages);
        var master_pages = $('.pages-list li');
        $(document).ready(function () {
            $(document).on('click', '.dp-list-item', function () {
                var level = $(this).parents('ul').length;
                var thisDocPage = $(this);
                var thisDocPageId = thisDocPage.data('id');

                // set correct level
                $(this).attr('data-level', level);

                //make menu item active
                $('.folder-list li').removeClass('active');
                thisDocPage.closest('li').addClass('active');

                //show page content
                $('.page-content').addClass('hidden');
                $('#dp_' + thisDocPageId).closest('.page-content').removeClass('hidden');
                if ($(this).attr('data-children-shown') == 0) {
                    $('[data-level="'+level+'"]').parent().next('ul').remove();
                    $('[data-level="'+level+'"]').attr('data-children-shown', 0); 
                    var itemsHtml = getChildPagesItems(thisDocPageId);
                    thisDocPage.closest('li').after(itemsHtml);
                    $(this).attr('data-children-shown', 1);
                    highlightList();
                } else {
                    $('a[data-parent-id="' + thisDocPageId + '"]').closest('ul').remove();
                    $(this).attr('data-children-shown', 0);
                }
            })

            //search
            var instance = new Mark(".page-content");

            $(document).on('keyup', '.search-matches', function () {
                var term = $(this).val();
                delay(function () {
                    if (term == '') {
                        $('li').removeClass('matches');
                    } else {
                        $('.pages-list').html(master_pages);
                        getMenuStructure();

                        instance.unmark();
                        instance.mark(term);

                        highlightList();
                    }
                }, 600);

            })
            $('.dp-list-item').first().trigger('click');
        });

        function getChildPagesItems(parent_page_id) {
            var itemsHtml = '<ul class="folder-list">';
            var childCount = 0;
            for (var i = 0; i < documentationPages.length; i++) {
                if (documentationPages[i]['parent_page_id'] == parent_page_id && !$('a[data-id=' + documentationPages[i]['id'] + ']').length) {

                    itemsHtml += '<li><a href="#" class="dp-list-item" data-children-shown=0 data-parent-id="' + parent_page_id + '" data-id="'
                            + documentationPages[i]['id'] + '"> <i class="fa fa-file-text-o"></i>'
                            + documentationPages[i]['title'] + '</a></li>';
                    childCount++;
                }
            }
            if (!childCount) {
                return '';
            }
            return itemsHtml + '</ul>';
        }

        function highlightList() {
            $('li').removeClass('matches has-match-childs');

            var match_pages = $('mark').closest('.page-content');
            $.each(match_pages, function (index, element) {
                var match_page_id = $(element).attr('data-id');
                var match_link = $('a[data-id=' + match_page_id + ']');
                match_link.closest('li').addClass('matches');
            });
        }

        function getMenuStructure(lis) {
            if (typeof lis == 'undefined') {
                lis = $('ul.pages-list > li');
            }

            if (!lis.length) {
                return false;
            }

            $.each(lis, function (index, element) {
                var page_id = $(element).find('a').attr('data-id');
                var itemsHtml = getChildPagesItems(page_id);

                if (itemsHtml != '') {
                    itemsHtml = $.parseHTML(itemsHtml);
                    $(element).after(itemsHtml);
                    var next_level_lis = $(itemsHtml).find('li');
                    getMenuStructure(next_level_lis);
                }
            });
        }

        var delay = (function () {
            var timer = 0;
            return function (callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        })();

    </script>
@endsection
