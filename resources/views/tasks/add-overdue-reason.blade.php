<div id="modal-form-{{ $task->id }}" class="modal fade text-left" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 b-r"><h3 class="m-t-none m-b">Add overdue reason</h3>
                        <form role="form" method="POST" action="{{ action('TaskController@createOverdue', $task) }}">
                            {{ csrf_field() }}
                            <div class="form-group"><label>Reason</label>
                                <select class="form-control m-b" name="reason" required>
                                    @foreach ($overdues as $overdue)
                                        <option value="{{ $overdue->id }}">{{ $overdue->reason }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Zendesk Ticket ID</label>
                                <input type="text" name="ticket_id" placeholder="Zendesk Ticket ID" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Comment</label>
                                <textarea name="comment" placeholder="Comment" class="form-control"></textarea>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-primary m-t-n-xs" type="submit"><strong>Create</strong></button>
                                <button type="button" class="btn btn-sm m-t-n-xs" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('script')
    <script type="text/javascript">
        $( document ).ready(function() {
            $('.modal-dialog button[type="submit"]').click(function(){
                $(this).hide();
            });
        });
    </script>
@endsection