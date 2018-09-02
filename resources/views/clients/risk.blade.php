<div id="risk-edit-modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit risk</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ action('ClientController@updateRisk', $client) }}">
                    {{ csrf_field() }}

                    {{-- Risk status --}}
                    <div class="form-group{{ $errors->has('risk') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label" for="risk">Client risk</label>

                        <div class="col-sm-4">
                            <select name="risk" id="risk" class="form-control">
                                @foreach($riskStatuses as $riskKey=>$riskValue)
                                    <option value="{{$riskKey}}" {{($riskKey == $client->risk) ? 'selected="selected"' : ''}}>{{$riskValue}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="risk-reason-container" class="form-group{{ $errors->has('risk') ? ' has-error' : '' }}" style="{{!$client->risk? 'display:none' : ''}}">
                        <label class="col-sm-3 control-label" for="risk_reason">Reason</label>

                        <div class="col-sm-9">
                            <textarea type="text" class="form-control" name="risk_reason" id="risk_reason">{{ $client->risk_reason }}</textarea>

                            @if ($errors->has('risk_reason'))
                                <span class="help-block m-b-none">
                                    <strong>{{ $errors->first('risk_reason') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group m-b-none">
                        <div class="col-sm-4 col-sm-offset-3">
                            <a class="btn btn-white" href="{{ action('ClientController@show', $client) }}">Cancel</a>
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
