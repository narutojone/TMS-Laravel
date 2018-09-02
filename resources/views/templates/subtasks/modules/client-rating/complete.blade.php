{{-- Rating --}}
<style>
    .rating-stars {
        padding-left: 0;
        line-height: 19px;
        list-style: none;
        display: inline-block;
    }

    .rating-stars li {
        display: inline-block;
    }

    .rating-stars li {
        font-size: 35px;
        color: #d2d2d2;
        transition: color 650ms;
        cursor: pointer;
    }

    .rating-stars:hover > li {
        color: gold;
    }

    .rating-stars > li:hover ~ li {
        color: #d2d2d2;
    }
    .rating-stars > li:after {
        content: "";
        display: block;
        position: absolute;
        bottom: -10px;
        left: 0;
        right: 0;
        height: 10px;
    }

    .rating-stars li.active {
        color: gold;
    }
</style>

<div class="form-group">
    <label class="col-sm-2 control-label">Rating</label>
    <div class="col-md-10">
        <ul class="rating-stars">
            <li data-rate="1"><i class="fa fa-star"></i></li>
            <li data-rate="2"><i class="fa fa-star"></i></li>
            <li data-rate="3"><i class="fa fa-star"></i></li>
            <li data-rate="4"><i class="fa fa-star"></i></li>
            <li data-rate="5"><i class="fa fa-star"></i></li>
        </ul>
        <span class="help-block">Gi oss en tilbakemelding på denne kunden.</span>
    </div>
    <input type="hidden" id="rating" name="modules[{{$module->id}}][rating]" value="">
</div>

{{-- Feedback --}}
<div class="form-group hidden feedback">
    <label class="col-sm-2 control-label">Feedback</label>
    <div class="col-md-10">
        <textarea type="text" class="form-control" name="modules[{{$module->id}}][feedback]" placeholder="Ettersom du ga denne kunden er dårlig rating ønsker vi å vite hva kunden burde ha gjort annerledes for å få en bedre rating av deg. (Dette er kun til internt bruk og vil ikke bli delt med kunden)"></textarea>
    </div>
</div>


@section('script')
    <script>
        $(document).ready(function () {
            $('.rating-stars li').on('click', function () {
                var clickedStar = $(this);
                var rate = clickedStar.attr('data-rate');
                clickedStar.addClass('active');
                clickedStar.prevAll().addClass('active');
                clickedStar.nextAll().removeClass('active');

                $('input#rating').val(rate);
                if (rate < 3) {
                    $('.feedback').removeClass('hidden');
                } else {
                    $('.feedback').addClass('hidden');
                }
            });
        });
    </script>
@append
